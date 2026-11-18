<?php

namespace Tests\Feature;

use App\Models\Categorie;
use App\Models\Commande;
use App\Models\CommandeDetail;
use App\Models\Produit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProduitTest extends TestCase
{
    use RefreshDatabase;

    private Categorie $categorie;

    protected function setUp(): void
    {
        parent::setUp();

        $this->categorie = Categorie::create([
            'nom' => 'Boissons',
            'is_active' => true,
        ]);
    }

    public function test_public_catalogue_only_returns_active_products_in_active_categories(): void
    {
        $visibleProduct = $this->createProduct('Visible');
        $this->createProduct('Inactif', ['is_active' => false]);

        $inactiveCategory = Categorie::create([
            'nom' => 'Archives',
            'is_active' => false,
        ]);
        $this->createProduct('Catégorie inactive', [
            'categorie_id' => $inactiveCategory->id,
        ]);

        $this->getJson('/api/v1/produits')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $visibleProduct->id);

        $this->getJson('/api/v1/produits/'.$this->productIdByName('Inactif'))
            ->assertNotFound();

        $this->getJson('/api/v1/categories')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_public_product_list_can_search_and_filter_products(): void
    {
        $matchingProduct = $this->createProduct('Jus d orange', [
            'prix' => '12.50',
        ]);
        $this->createProduct('Jus de pomme', ['prix' => '20.00']);
        $this->createProduct('Thé vert', ['prix' => '8.00']);

        $this->getJson('/api/v1/produits?search=Jus&categorie_id='.$this->categorie->id.'&prix_min=10&prix_max=15')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $matchingProduct->id)
            ->assertJsonPath('meta.total', 1);
    }

    public function test_public_product_list_is_paginated_and_preserves_filters_in_links(): void
    {
        $this->createProduct('Jus 1');
        $this->createProduct('Jus 2');
        $this->createProduct('Jus 3');

        $this->getJson('/api/v1/produits?search=Jus&per_page=2&page=2')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('meta.current_page', 2)
            ->assertJsonPath('meta.per_page', 2)
            ->assertJsonPath('meta.total', 3)
            ->assertJsonPath('meta.last_page', 2)
            ->assertJsonPath('links.next', null);
    }

    public function test_public_product_list_validates_filter_ranges_and_page_size(): void
    {
        $this->getJson('/api/v1/produits?prix_min=20&prix_max=10')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['prix_max']);

        $this->getJson('/api/v1/produits?per_page=101')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['per_page']);

        $this->getJson('/api/v1/produits?page=0')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['page']);
    }

    public function test_only_admin_can_manage_products(): void
    {
        $this->actingAs(User::factory()->create())
            ->postJson('/api/v1/produits', [
                'nom' => 'Nouveau',
                'prix' => 5,
                'categorie_id' => $this->categorie->id,
            ])
            ->assertForbidden();

        $this->actingAs(User::factory()->admin()->create())
            ->postJson('/api/v1/produits', [
                'nom' => 'Nouveau',
                'prix' => 5,
                'categorie_id' => $this->categorie->id,
            ])
            ->assertCreated()
            ->assertJsonPath('data.stock', 0)
            ->assertJsonPath('data.is_active', true);
    }

    public function test_product_validation_rejects_non_positive_price_and_invalid_stock(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->postJson('/api/v1/produits', [
                'nom' => 'Invalide',
                'prix' => 0,
                'categorie_id' => $this->categorie->id,
                'stock' => -1,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['prix', 'stock']);
    }

    public function test_product_validation_rejects_prices_that_do_not_fit_the_database_column(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->postJson('/api/v1/produits', [
                'nom' => 'Trop cher',
                'prix' => 1000000,
                'categorie_id' => $this->categorie->id,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['prix']);
    }

    public function test_category_cannot_be_deleted_while_it_has_products(): void
    {
        $this->createProduct('Jus');

        $this->actingAs(User::factory()->admin()->create())
            ->deleteJson('/api/v1/categories/'.$this->categorie->id)
            ->assertStatus(409)
            ->assertJsonPath(
                'message',
                'Cette catégorie ne peut pas être supprimée car elle contient des produits.'
            );

        $this->assertDatabaseHas('categories', ['id' => $this->categorie->id]);
    }

    public function test_product_used_by_an_order_cannot_be_deleted(): void
    {
        $product = $this->createProduct('Jus');
        $user = User::factory()->create();
        $commande = Commande::create([
            'numero_commande' => 'CMD-TEST',
            'user_id' => $user->id,
            'montant_total' => '10.00',
            'statut' => 'en_attente',
        ]);
        CommandeDetail::create([
            'commande_id' => $commande->id,
            'produit_id' => $product->id,
            'quantite' => 1,
            'prix_unitaire' => '10.00',
            'sous_total' => '10.00',
        ]);

        $this->actingAs(User::factory()->admin()->create())
            ->deleteJson('/api/v1/produits/'.$product->id)
            ->assertStatus(409)
            ->assertJsonPath(
                'message',
                'Ce produit ne peut pas être supprimé car il est utilisé dans une commande.'
            );

        $this->assertDatabaseHas('produits', ['id' => $product->id]);
    }

    private function createProduct(string $name, array $overrides = []): Produit
    {
        return Produit::create(array_merge([
            'nom' => $name,
            'prix' => '10.00',
            'categorie_id' => $this->categorie->id,
            'stock' => 5,
            'is_active' => true,
        ], $overrides));
    }

    private function productIdByName(string $name): int
    {
        return (int) Produit::query()->where('nom', $name)->value('id');
    }
}

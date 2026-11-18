<?php

namespace Tests\Feature;

use App\Models\Categorie;
use App\Models\Produit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategorieTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->admin()->create());
    }

    public function test_can_create_categorie(): void
    {
        $data = [
            'nom' => 'Boissons',
            'description' => 'Boissons fraîches et naturelles',
            'is_active' => true,
        ];

        $response = $this->postJson('/api/v1/categories', $data);

        $response->assertStatus(201);
    }

    public function test_cannot_create_categorie_without_nom(): void
    {
        $data = [
            'description' => 'Boissons fraîches et naturelles',
            'is_active' => true,
        ];

        $response = $this->postJson('/api/v1/categories', $data);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors(['nom']);
    }

    public function test_cannot_create_categorie_with_nom_exceeding_255_characters(): void
    {
        $data = [
            'nom' => str_repeat('A', 256),
            'description' => 'Une catégorie de test',
        ];

        $response = $this->postJson('/api/v1/categories', $data);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors(['nom']);
    }

    public function test_can_create_categorie_without_description(): void
    {
        $data = [
            'nom' => 'Pâtisseries',
            'is_active' => true,
        ];

        $response = $this->postJson('/api/v1/categories', $data);

        $response->assertStatus(201);
        $response->assertJsonMissingValidationErrors(['description']);
    }

    public function test_can_create_categorie_without_is_active(): void
    {
        $data = [
            'nom' => 'Glaces',
        ];

        $response = $this->postJson('/api/v1/categories', $data);

        $response->assertStatus(201);
    }

    public function test_cannot_create_categorie_with_invalid_is_active(): void
    {
        $data = [
            'nom' => 'Jus',
            'is_active' => 'oui',
        ];

        $response = $this->postJson('/api/v1/categories', $data);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors(['is_active']);
    }

    public function test_client_cannot_create_categorie(): void
    {
        $this->actingAs(User::factory()->create());

        $this->postJson('/api/v1/categories', [
            'nom' => 'Boissons',
        ])->assertForbidden();
    }

    public function test_category_with_products_cannot_be_deleted(): void
    {
        $categorie = Categorie::create([
            'nom' => 'Boissons',
            'is_active' => true,
        ]);

        Produit::create([
            'nom' => 'Jus',
            'prix' => '10.00',
            'categorie_id' => $categorie->id,
            'stock' => 5,
            'is_active' => true,
        ]);

        $this->deleteJson('/api/v1/categories/'.$categorie->id)
            ->assertStatus(409);

        $this->assertDatabaseHas('categories', ['id' => $categorie->id]);
    }

    public function test_can_get_single_categorie(): void
    {
        // Créer une catégorie dans la base de test
        $categorie = Categorie::create([
            'nom' => 'Chaussures',
            'description' => 'Chaussures pour femmes',
            'is_active' => true,
        ]);

        // Envoyer une requête GET
        $response = $this->getJson('/api/v1/categories/'.$categorie->id);

        // Vérifier le statut HTTP
        $response->assertStatus(200);

        // Vérifier les données retournées
        $response->assertJsonPath('message', 'Categorie details');
        $response->assertJsonPath('data.id', $categorie->id);
        $response->assertJsonPath('data.nom', 'Chaussures');
        $response->assertJsonPath('data.description', 'Chaussures pour femmes');
    }
}

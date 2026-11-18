<?php

namespace Tests\Feature;

use App\Models\Categorie;
use App\Models\Commande;
use App\Models\Produit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommandeTest extends TestCase
{
    use RefreshDatabase;

    private User $client;

    private Produit $produit;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = User::factory()->create();

        $categorie = Categorie::create([
            'nom' => 'Boissons',
            'is_active' => true,
        ]);

        $this->produit = Produit::create([
            'nom' => 'Jus',
            'prix' => '10.00',
            'categorie_id' => $categorie->id,
            'stock' => 5,
            'is_active' => true,
        ]);
    }

    public function test_client_can_create_and_confirm_order_and_stock_is_debited_once(): void
    {
        $this->actingAs($this->client);

        $response = $this->postJson('/api/v1/commandes', [
            'details' => [
                [
                    'produit_id' => $this->produit->id,
                    'quantite' => 2,
                ],
            ],
        ])->assertCreated()
            ->assertJsonPath('data.statut', 'en_attente')
            ->assertJsonPath('data.details.0.quantite', 2);

        $commandeId = $response->json('data.id');

        $this->assertSame(5, $this->produit->fresh()->stock);
        $this->assertDatabaseHas('commande_details', [
            'commande_id' => $commandeId,
            'produit_id' => $this->produit->id,
            'quantite' => 2,
            'prix_unitaire' => '10.00',
            'sous_total' => '20.00',
        ]);

        $this->patchJson("/api/v1/commandes/{$commandeId}/confirm")
            ->assertOk()
            ->assertJsonPath('data.statut', 'confirmee');

        $this->assertSame(3, $this->produit->fresh()->stock);

        $this->patchJson("/api/v1/commandes/{$commandeId}/confirm")
            ->assertUnprocessable();

        $this->assertSame(3, $this->produit->fresh()->stock);
    }

    public function test_client_cannot_read_or_confirm_another_clients_order(): void
    {
        $response = $this->actingAs($this->client)->postJson('/api/v1/commandes', [
            'details' => [
                [
                    'produit_id' => $this->produit->id,
                    'quantite' => 1,
                ],
            ],
        ])->assertCreated();

        $commandeId = $response->json('data.id');
        $autreClient = User::factory()->create();

        $this->actingAs($autreClient)
            ->getJson("/api/v1/commandes/{$commandeId}")
            ->assertNotFound();

        $this->patchJson("/api/v1/commandes/{$commandeId}/confirm")
            ->assertNotFound();

        $this->assertSame(5, $this->produit->fresh()->stock);
        $this->assertSame('en_attente', Commande::findOrFail($commandeId)->statut);
    }

    public function test_admin_can_confirm_a_clients_order(): void
    {
        $response = $this->actingAs($this->client)->postJson('/api/v1/commandes', [
            'details' => [
                [
                    'produit_id' => $this->produit->id,
                    'quantite' => 1,
                ],
            ],
        ])->assertCreated();

        $commandeId = $response->json('data.id');

        $this->actingAs(User::factory()->admin()->create())
            ->patchJson("/api/v1/commandes/{$commandeId}/confirm")
            ->assertOk()
            ->assertJsonPath('data.statut', 'confirmee');

        $this->assertSame(4, $this->produit->fresh()->stock);
    }

    public function test_confirmation_fails_if_stock_is_no_longer_available(): void
    {
        $response = $this->actingAs($this->client)->postJson('/api/v1/commandes', [
            'details' => [
                [
                    'produit_id' => $this->produit->id,
                    'quantite' => 2,
                ],
            ],
        ])->assertCreated();

        $commandeId = $response->json('data.id');
        $this->produit->update(['stock' => 1]);

        $this->patchJson("/api/v1/commandes/{$commandeId}/confirm")
            ->assertUnprocessable();

        $this->assertSame(1, $this->produit->fresh()->stock);
        $this->assertSame('en_attente', Commande::findOrFail($commandeId)->statut);
    }

    public function test_order_rejects_duplicate_products(): void
    {
        $this->actingAs($this->client)
            ->postJson('/api/v1/commandes', [
                'details' => [
                    [
                        'produit_id' => $this->produit->id,
                        'quantite' => 1,
                    ],
                    [
                        'produit_id' => $this->produit->id,
                        'quantite' => 1,
                    ],
                ],
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['details.1.produit_id']);

        $this->assertDatabaseCount('commandes', 0);
    }
}

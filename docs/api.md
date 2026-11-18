# Contrat de l’API

Base URL locale : `http://127.0.0.1:8000/api/v1`.

Les corps de requête et réponses sont en JSON. Pour les erreurs de validation,
l’API répond avec le statut `422` et le format standard Laravel (`message` et
`errors`). Une ressource introuvable répond `404`; une suppression impossible
à cause de relations existantes répond `409`.

## Authentification et autorisations

Les routes protégées utilisent `auth:sanctum`.

- `client` : consulter le catalogue, créer et consulter ses commandes, confirmer
  ses commandes.
- `admin` : mêmes actions, accès à toutes les commandes et gestion des catégories
  et produits.
- Les routes de consultation du catalogue sont publiques et n’affichent que les
  catégories et produits actifs. Un produit n’est pas publié si sa catégorie
  est inactive.
- L’inscription publique crée uniquement des clients.

### Routes d’authentification

| Méthode | Route | Accès | Description |
|---|---|---|---|
| `POST` | `/register` | Public | Créer un compte client |
| `POST` | `/mobile/login` | Public | Connexion et émission d’un jeton Sanctum |
| `POST` | `/mobile/logout` | Authentifié | Révoquer le jeton mobile courant |
| `POST` | `/login` | Session web | Connexion web |
| `POST` | `/logout` | Session web | Fermer la session web courante |
| `GET` | `/user` | Authentifié | Informations du compte courant |

Exemple de connexion mobile :

```json
{
  "email": "client@example.com",
  "password": "mot-de-passe"
}
```

Envoyer le jeton retourné dans l’en-tête `Authorization: Bearer <jeton>`.

## Catalogue

Les créations, modifications et suppressions sont réservées aux administrateurs.

| Méthode | Route | Accès | Action |
|---|---|---|---|
| `GET` | `/categories` | Public | Lister les catégories actives |
| `GET` | `/categories/{id}` | Public | Lire une catégorie active |
| `POST` | `/categories` | Admin | Créer une catégorie |
| `PUT`, `PATCH` | `/categories/{id}` | Admin | Modifier une catégorie |
| `DELETE` | `/categories/{id}` | Admin | Supprimer une catégorie sans produits |
| `GET` | `/produits` | Public | Rechercher, filtrer et paginer les produits publiés |
| `GET` | `/produits/{id}` | Public | Lire un produit publié |
| `POST` | `/produits` | Admin | Créer un produit |
| `PUT`, `PATCH` | `/produits/{id}` | Admin | Modifier un produit |
| `DELETE` | `/produits/{id}` | Admin | Supprimer un produit absent des commandes |

Exemple de création d’un produit :

```json
{
  "nom": "Jus",
  "prix": 10.00,
  "categorie_id": 1,
  "stock": 20,
  "is_active": true
}
```

Les catégories contenant des produits et les produits référencés par une
commande ne sont pas supprimables; l’API retourne `409 Conflict`.

### Recherche et filtres produits

`GET /produits` accepte les paramètres optionnels suivants. Ils peuvent être
combinés :

| Paramètre | Type | Description |
|---|---|---|
| `search` | Chaîne (max. 255) | Recherche partielle, sans distinction de casse selon la base de données, dans le nom du produit |
| `categorie_id` | Entier existant | Limiter à une catégorie |
| `prix_min` | Décimal (0 à 2 décimales) | Prix minimum inclus |
| `prix_max` | Décimal (0 à 2 décimales) | Prix maximum inclus |
| `per_page` | Entier de 1 à 100 | Nombre de résultats par page (15 par défaut) |
| `page` | Entier | Page demandée (géré par le paginator Laravel) |

Exemple :

```text
GET /api/v1/produits?search=jus&categorie_id=2&prix_min=5&prix_max=20&per_page=10&page=1
```

La réponse contient `data`, `links` et `meta` de pagination. Les liens de page
conservent les filtres transmis. Un prix minimum supérieur au prix maximum ou
une valeur hors limites retourne `422`.

## Commandes

| Méthode | Route | Accès | Action |
|---|---|---|---|
| `POST` | `/commandes` | Authentifié | Créer une commande pour le compte courant |
| `GET` | `/commandes` | Authentifié | Lister ses commandes; un admin voit toutes les commandes |
| `GET` | `/commandes/{id}` | Propriétaire ou admin | Consulter une commande |
| `PATCH` | `/commandes/{id}/confirm` | Propriétaire ou admin | Confirmer une commande |

Exemple de création :

```json
{
  "details": [
    {
      "produit_id": 1,
      "quantite": 2
    }
  ]
}
```

Le client ne transmet ni le prix, ni le montant total, ni l’identifiant du
propriétaire : le serveur les détermine à partir du compte et du catalogue.
Une commande créée est `en_attente`. Le prix unitaire et le sous-total sont
enregistrés dans les détails afin de conserver le prix appliqué lors de la
commande.

La création vérifie la disponibilité actuelle sans réduire le stock. La
confirmation reverrouille les produits, revérifie leur état et leur stock,
décrémente le stock et passe la commande à `confirmee` dans une même transaction.
Une commande ne peut être confirmée qu’une seule fois. Le stock non réservé peut
changer avant la confirmation; dans ce cas, celle-ci échoue avec `422` et la
commande reste en attente.

Les clients ne peuvent pas lire ni confirmer une commande appartenant à un autre
compte; ces requêtes répondent `404`.

# Gestion de commandes

API REST Laravel pour un catalogue de catégories et de produits, des comptes
client/administrateur et un cycle de commandes. Le code est organisé en couches
inspirées de la Clean Architecture :

- `app/Http` : contrôleurs, Form Requests, middleware et ressources JSON.
- `app/DTO` : objets de transfert entre l’API et les services.
- `app/Services` : règles métier et interfaces de services.
- `app/Repositories` : interfaces et accès aux données.
- `app/Models` : modèles Eloquent et relations.

## Prérequis et démarrage

- PHP 8.3 ou supérieur.
- Composer.
- Une base de données prise en charge par Laravel.

Depuis la racine du projet :

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
```

Configurez ensuite la connexion à la base de données dans `.env`, puis :

```powershell
php artisan migrate
php artisan serve
```

L’API est exposée sous `/api/v1`. Le contrat des routes, les rôles et les
exemples de requêtes sont décrits dans [docs/api.md](docs/api.md).
Le catalogue accepte la recherche `search`, les filtres `categorie_id`,
`prix_min` / `prix_max` et la pagination `per_page` / `page`.

## Comptes et rôles

L’inscription publique crée toujours un compte `client`. Elle ne permet pas de
choisir le rôle. Les écritures du catalogue sont réservées aux comptes `admin`.
Pour promouvoir manuellement un compte existant en administrateur :

```powershell
php artisan tinker
```

Puis dans Tinker :

```php
App\Models\User::where('email', 'admin@example.com')->update(['role' => 'admin']);
```

Ne définissez jamais un mot de passe ou une clé réelle dans le dépôt.

## Commandes de vérification

```powershell
php artisan test
vendor/bin/pint --test
php artisan route:list --path=api/v1
```

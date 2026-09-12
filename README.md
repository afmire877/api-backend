# Share Your Iftar API

A Laravel API for coordinating meal requests, charities, and collection points during Ramadan.

Share Your Iftar was created in response to the loss of communal iftar meals during COVID-19. The service supports people requesting meals, charities coordinating fulfilment, and collection points managing local hand-off.

> **Project status:** This is a legacy Laravel 7 project built in 2020. The repository is preserved as a technical case study; its original Heroku deployment may no longer be available.

## What the system demonstrates

- OAuth2-protected API authentication with Laravel Passport
- Role-oriented workflows for users, charities, collection points, and administrators
- Order creation, lookup, daily fulfilment views, and status updates
- Location-aware collection-point discovery and delivery-radius checks
- Email/SMS integration points, scheduled notifications, and Sentry monitoring
- Feature tests backed by an in-memory SQLite database
- Continuous integration with GitHub Actions

## Architecture

The API is organised around Laravel controllers, Eloquent models, services, events/listeners, and scheduled console commands.

```text
Client applications
       |
       v
Laravel routes + Passport authentication
       |
       v
Role-specific controllers
       |
       +--> Orders and fulfilment workflow
       +--> Charity and collection-point lookup
       +--> Location/delivery checks
       |
       v
Eloquent models + relational database
       |
       +--> Notifications / scheduled email
       +--> MessageBird SMS
       +--> Sentry monitoring
```

See [docs/architecture.md](docs/architecture.md) for the domain model, role boundaries, and representative endpoint flows.

## API at a glance

All routes are prefixed with `/api`.

| Access | Method | Endpoint | Purpose |
|---|---:|---|---|
| Public | POST | `/register` | Create an account |
| Public | POST | `/login` | Exchange credentials for an access token |
| Public | POST | `/forgot-password` | Start password recovery |
| Authenticated user | POST | `/user/orders` | Request or donate a meal |
| Authenticated user | GET | `/user/orders/today` | View today's orders |
| Charity | GET | `/charity/orders` | View orders assigned to a charity |
| Collection point | GET | `/collection-point/orders` | View collection-point orders |
| Administrator | GET | `/admin/orders/today` | View today's operational workload |
| Authenticated | POST | `/collection-points/near-me` | Find nearby collection points |
| Authenticated | POST | `/collection-points/{id}/can-deliver-to-location` | Check a collection point's delivery coverage |
| Authenticated | POST | `/logout` | Revoke the current session |

Protected endpoints require:

```http
Authorization: Bearer <access-token>
Accept: application/json
Content-Type: application/json
```

The route list in [routes/api.php](routes/api.php) is the source of truth. The historical API reference is also available in this [Google Doc](https://docs.google.com/document/d/1VLw98wvg7Yyq46QgAv5rYjwUJCk4Uwb_dC9OBWCLvaQ/view).

## Run locally

### Requirements

- PHP 7.2.5 or a compatible PHP 7.x runtime
- Composer
- SQLite
- Node.js and npm (only required for the small frontend asset bundle)

### Setup

```bash
git clone https://github.com/afmire877/api-backend.git
cd api-backend
composer install
cp .env.example .env
touch database/database.sqlite
```

Set `DB_DATABASE` in `.env` to the absolute path of `database/database.sqlite`, then run:

```bash
php artisan key:generate
php artisan passport:keys
php artisan migrate
php artisan serve
```

Optional frontend assets:

```bash
npm install
npm run dev
```

SMS is disabled by default. Mail, MessageBird, and Sentry credentials are only needed to exercise those integrations.

## Test

```bash
composer test
# or
php artisan test
```

The PHPUnit configuration uses SQLite in memory and disables external delivery behaviour through the test environment.

## Repository provenance and contribution

This repository is a fork of [iftar/api-backend](https://github.com/iftar/api-backend).

Before using this project as evidence of personal work, replace this paragraph with a precise, verifiable contribution statement: the role held, features personally owned, key technical decisions, team context, and links to attributable commits or pull requests. No individual contribution claim is made here because the current fork history does not provide enough evidence to verify one.

## Further context

- [Architecture and domain guide](docs/architecture.md)
- [Original project presentation](https://docs.google.com/presentation/d/e/2PACX-1vS0bv3ICWNIU2epGje3SOO3GMEh8iphTjc9RlUlmqhOwXckGptDU3sOI5v77rTvGcU74BYAkTBSTLgh/pub)
- [Order-flow wireframe](wireframes/share_your_iftar_-_order_flow_.png)
- [Charity dashboard wireframe](wireframes/Share_your_iftar-_charity_dashboard_.png)
- [Collection dashboard wireframe](wireframes/share_your_iftar_-_collection_dashboard_.png)

## License

The package metadata declares the project under the MIT license.

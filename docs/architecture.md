# Architecture and domain guide

This guide describes the architecture visible in the codebase. It is intentionally implementation-focused and does not claim production scale or current deployment status.

## System boundaries

Share Your Iftar is a Laravel 7 HTTP API. Clients call routes under `/api`; Laravel Passport authenticates protected requests. Controllers are split by actor where their operational views differ, while shared models and services implement the underlying meal and collection-point workflows.

External integration points include:

- MessageBird for optional SMS delivery
- Laravel mail and notifications for transactional and scheduled messages
- Sentry for application monitoring
- Laravel Telescope for local diagnostics

## Core domain

| Concept | Responsibility |
|---|---|
| User | Authenticated person who can maintain a profile and create or view orders |
| Order | A meal request/donation record moving through fulfilment |
| Charity | Organisation involved in coordinating orders |
| Collection point | Physical hand-off location with coordinates and a delivery radius |
| Time slot | Collection availability associated with a collection point |
| Batch | Operational grouping of orders, including CSV-based workflows |

The migrations also model the associations between charities, collection points, their users, batches, and orders.

## Role-oriented API surface

### User

The `/api/user` routes expose profile and order operations. Users can list, create, inspect, and update orders, as well as query today's order or whether an order already exists.

### Charity

The `/api/charity` routes expose the charity profile and the orders relevant to that charity.

### Collection point

The `/api/collection-point` routes expose the collection-point profile and its assigned orders. Shared authenticated routes also support nearby searches and delivery-radius checks.

### Administrator

The `/api/admin/orders/today` route provides an operational view of the day's orders.

Route prefixes express the intended actor boundary. Authorization behaviour should still be verified in controllers and tests before extending the application.

## Representative flows

### Authentication

1. A client registers or submits credentials to `POST /api/login`.
2. Laravel Passport issues credentials used as a bearer token.
3. The `auth:api` middleware protects the role and fulfilment routes.
4. `POST /api/logout` ends the authenticated session.

### Meal order

1. An authenticated user calls `POST /api/user/orders`.
2. The user order controller validates and persists the order.
3. Charity and collection-point views expose relevant orders through their role-specific endpoints.
4. Daily endpoints support operational fulfilment.
5. Events, listeners, notifications, and scheduled commands coordinate follow-up communication.

### Location lookup

1. An authenticated client submits coordinates to `POST /api/collection-points/near-me`.
2. The collection-point controller searches using stored latitude/longitude data.
3. A client can check a selected point with `POST /api/collection-points/{id}/can-deliver-to-location`.
4. The result is evaluated against that collection point's configured delivery radius.

## Persistence

Eloquent models sit above a relational schema created by Laravel migrations. Tests use SQLite in memory; local development defaults to a file-backed SQLite database. The schema includes foreign-key relationships and join tables for the role and fulfilment associations.

## Testing and delivery

Feature tests cover authentication and the user, charity, collection-point, admin, and homepage surfaces. GitHub Actions installs Composer dependencies, creates an SQLite database and Passport key pair, and runs the Laravel test suite.

Because the dependency set targets Laravel 7 and PHP 7.2.5+, reproducing the historical runtime may require a compatible container or older PHP environment. Any framework upgrade should be handled separately from documentation changes and validated as an application migration.

# Laravel Microservices

A microservices architecture built with Laravel, consisting of three independent services that communicate via HTTP APIs and message queues. The system manages companies, categories, user authentication, and email notifications.

## Architecture

```
                         ┌──────────────┐
                         │   Client     │
                         └──────┬───────┘
                                │
                         ┌──────▼───────┐
                         │ micro-gateway│  :8098
                         │  (API Gateway)│
                         └──┬───────┬───┘
                   HTTP     │       │     HTTP
              ┌─────────────┘       └─────────────┐
              │                                    │
       ┌──────▼───────┐                    ┌───────▼──────┐
       │   micro-01   │  :8000             │  micro-auth  │
       │  (Business   │                    │(Authentication│
       │   Logic)     │                    │  Service)    │
       └──────┬───────┘                    └──────────────┘
              │ RabbitMQ
       ┌──────▼───────┐
       │  micro-email  │  :8005
       │  (Email &     │
       │   Queues)     │
       └──────────────┘
```

| Service | Port | Laravel | PHP | Responsibility |
|---------|------|---------|-----|----------------|
| **micro-gateway** | 8098 | 8.40 | 7.4 | API gateway, request routing, auth middleware |
| **micro-01** | 8000 | 10.0 | 8.2 | Companies & categories CRUD, business logic |
| **micro-email** | 8005 | 8.40 | 8.2 | Async email notifications via queues |

## Tech Stack

- **Framework:** Laravel 8.x / 10.x
- **Authentication:** Laravel Sanctum (token-based)
- **Database:** MySQL 5.7
- **Cache / Sessions:** Redis
- **Message Queue:** RabbitMQ
- **HTTP Client:** Guzzle (inter-service communication)
- **Containerization:** Docker & Docker Compose
- **Web Server:** Nginx + PHP-FPM
- **Mail (dev):** Mailhog / Mailpit
- **Testing:** PHPUnit (SQLite in-memory)

## Getting Started

### Prerequisites

- Docker & Docker Compose
- Git

### Setup

1. **Clone the repository**

   ```bash
   git clone <repository-url>
   cd laravel-micro-services
   ```

2. **Configure environment variables**

   Copy `.env.example` to `.env` in each service and adjust as needed:

   ```bash
   cp micro-01/.env.example micro-01/.env
   cp micro-gateway/.env.example micro-gateway/.env
   cp micro-email/.env.example micro-email/.env
   ```

3. **Start the services**

   Each service has its own `docker-compose.yml`. Start them individually:

   ```bash
   # Start micro-01 (business logic + MySQL + Redis)
   cd micro-01 && docker-compose up -d && cd ..

   # Start micro-email (email service + Redis)
   cd micro-email && docker-compose up -d && cd ..

   # Start micro-gateway (API gateway)
   cd micro-gateway && docker-compose up -d && cd ..
   ```

4. **Install dependencies and run migrations**

   ```bash
   # micro-01
   docker-compose -f micro-01/docker-compose.yml exec app composer install
   docker-compose -f micro-01/docker-compose.yml exec app php artisan key:generate
   docker-compose -f micro-01/docker-compose.yml exec app php artisan migrate

   # micro-gateway
   docker-compose -f micro-gateway/docker-compose.yml exec gateway composer install
   docker-compose -f micro-gateway/docker-compose.yml exec gateway php artisan key:generate

   # micro-email
   docker-compose -f micro-email/docker-compose.yml exec app composer install
   docker-compose -f micro-email/docker-compose.yml exec app php artisan key:generate
   ```

## API Endpoints

All requests go through the **gateway** at `http://localhost:8098`.

### Authentication

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/register` | Register a new user |
| POST | `/api/auth` | Login |
| GET | `/api/me` | Get authenticated user |
| POST | `/api/logout` | Logout |

### Companies

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/companies` | List all companies (paginated, filterable) |
| POST | `/api/companies` | Create a company |
| GET | `/api/companies/{uuid}` | Get a company with evaluations |
| PUT | `/api/companies/{uuid}` | Update a company |
| DELETE | `/api/companies/{uuid}` | Delete a company |

### Categories

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/categories` | List all categories |
| POST | `/api/categories` | Create a category |
| GET | `/api/categories/{id}` | Get a category |
| PUT | `/api/categories/{id}` | Update a category |
| DELETE | `/api/categories/{id}` | Delete a category |

### Users & Permissions

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/users` | List users |
| POST | `/api/users` | Create a user |
| GET | `/api/users/{id}` | Get a user |
| PUT | `/api/users/{id}` | Update a user |
| DELETE | `/api/users/{id}` | Delete a user |
| GET | `/api/users/{id}/permissions` | Get user permissions |
| POST | `/api/users/permissions` | Add permission |
| DELETE | `/api/users/permissions` | Remove permission |

### Evaluations

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/companies/{uuid}/evaluations` | Create an evaluation |

## Inter-Service Communication

- **Gateway &rarr; micro-01:** Synchronous HTTP via Guzzle. Configured in `config/microservices.php`.
- **Gateway &rarr; micro-auth:** Authentication and permission validation through custom middleware (`CheckUserAuth`, `EnsureUserHasPermission`).
- **micro-01 &rarr; micro-email:** Asynchronous via RabbitMQ. Jobs like `CompanyCreated` are dispatched to the `queue_email` queue.

## Database Schema

### Companies

| Column | Type | Notes |
|--------|------|-------|
| id | bigint | Primary key |
| category_id | bigint | Foreign key |
| uuid | string | Unique identifier |
| name | string | Unique |
| url | string | Unique |
| phone | string | Nullable |
| whatsapp | string | |
| email | string | Unique |
| facebook | string | Nullable |
| instagram | string | Nullable |
| youtube | string | Nullable |
| image | string | Storage path |

### Categories

| Column | Type | Notes |
|--------|------|-------|
| id | bigint | Primary key |
| title | string | Unique |
| url | string | Unique |
| description | string | |

## Testing

Each service includes PHPUnit tests. Micro-01 uses an SQLite in-memory database for test isolation.

```bash
# Run tests for micro-01
cd micro-01 && ./vendor/bin/phpunit

# Or via Docker
docker-compose -f micro-01/docker-compose.yml exec app ./vendor/bin/phpunit
```

## Docker Services

Each microservice spins up its own set of containers:

**micro-01:** `app` (PHP-FPM), `nginx`, `mysql`, `redis`, `queue` (worker)
**micro-email:** `app` (PHP-FPM), `nginx`, `redis`, `queue` (worker)
**micro-gateway:** `gateway` (PHP-FPM), `nginx`

## Key Design Patterns

- **API Gateway** — Single entry point for all client requests
- **Service Layer** — Business logic encapsulated in service classes
- **Async Job Processing** — Email notifications decoupled via RabbitMQ
- **Form Request Validation** — Input validation via dedicated request classes
- **API Resources** — Consistent JSON responses via Laravel Resource classes
- **Permission Middleware** — Role-based access control per endpoint

## License

This project is open-sourced software.

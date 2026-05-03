# Travel Experience

A collaborative travel planning application — create trips, add locations on a map, schedule them in a calendar, share with other users, chat in real-time, and upload photos.

Demo: http://35.193.209.157:8000/public/index.php/

## Features

- **Travel Management** — create, edit, and delete travel plans with dates, descriptions, and photos
- **Interactive Map** — Leaflet.js with multiple tile layers (OSM, Google Maps, satellite, terrain), GPX track rendering, routing
- **Location Management** — add, edit, and delete locations with marks, type classification, visit dates, and notes
- **Calendar View** — visualize travel schedules in a calendar interface
- **Travel Sharing** — share travels with other users who can add locations and collaborate
- **Real-time Chat** — WebSocket-based chat for each travel using Go backend
- **Image Uploads** — upload and optimize images (auto-convert to WebP, resize to 1920px max)
- **Read-only Mode** — ability to view travels without editing
- **Watch Counter** — track travel views
- **Markdown Support** — travel descriptions with markdown rendering

## Tech Stack

### Backend — PHP / Symfony 4
- **Symfony 4.4** — framework, routing, security (CSRF protection), forms, console
- **Doctrine ORM** — MySQL persistence with UUID primary keys (`ramsey/uuid-doctrine`), embeddables, custom mapping types, and Gedmo extensions (sluggable, timestampable)
- **CQRS + Command Bus** — commands and queries dispatched through `league/tactician` with Doctrine transactional middleware
- **Hexagonal Architecture / DDD** — domain, application, and infrastructure layers; no framework leaking into the domain
- **JMS Serializer** — flexible serialization for API responses
- **Symfony Messenger** — async message handling
- **Guzzle 6.5** — HTTP client for internal service communication (PHP → Go WebSocket server)
- **Symfony Security** — form-based authentication, role system (ROLE_USER, ROLE_ADMIN), travel ownership and sharing model
- **Twig 2.16** — server-rendered templates with component includes
- **KnpMarkdownBundle** — markdown support for travel descriptions
- **Webpack Encore** — asset bundling with React components

### WebSocket Server — Go
A standalone Go service (`PlanningTravelsSocketio/`) that manages real-time collaboration:
- **gorilla/websocket** — WebSocket upgrade and connection management
- Room-based pub/sub: each travel has its own room; clients join via `GET /ws/{travelId}`
- PHP broadcasts events to connected clients via `POST /travel/{travelId}/broadcast`
- Real-time location updates, chat messages, and notifications
- Ping/pong keepalive, graceful shutdown

### Frontend
- **jQuery** + vanilla JS — map interactions, drag-and-drop, file uploads (`jquery.fileupload`), sortable lists
- **Leaflet.js** — interactive map with multiple tile layers (OSM, Google Maps, satellite, terrain, traffic), routing machine, GPX track rendering, fullscreen, and print
- **React** (via Encore) — used for select components and dynamic UI elements
- **Bootstrap** — responsive design

### Infrastructure & DevOps
- **Docker Compose** — multi-container setup: PHP/Apache app, Go WebSocket server, MySQL 5.7 with persistent volumes, Adminer
- **MySQL 5.7** — database with proper indexing for performance
- **GitHub Actions** — CI pipeline (`.github/workflows/validation.yml`)
- **Xdebug** — remote debugging (enabled via `docker-compose.override.yml` for local development)
- **Adminer** — database UI at `localhost:8080`
- **Elasticsearch 6.x** — search integration (currently disabled in docker-compose)

### Code Quality
- **PHPStan** — static analysis
- **PHP CodeSniffer** + **PHP CS Fixer** — coding standards
- **PHPMD** — mess detection
- **PHPUnit** — unit and functional tests with fixtures

## Running Locally

### Prerequisites
- Docker & Docker Compose
- (Optional) PHP 7.4+ and Composer for local development

### Quick Start

```bash
# Clone and start all services
docker-compose up -d

# The app will be available at:
# App: http://localhost:8000
# Adminer: http://localhost:8080 (server: mysql, user: root, password: root)
# WebSocket: ws://localhost:5555
```

### Development with Xdebug

```bash
# docker-compose.override.yml enables Xdebug automatically
# No need to modify docker-compose.yml

# Stop and restart
docker-compose down
docker-compose up -d

# Access the app container
docker-compose exec app bash

# Clear cache
php bin/console cache:clear --env=dev
```

### Production Deployment

```bash
# 1. Merge xxxx branches
git checkout master
git merge xxxxx

# 2. Update dependencies
docker-compose exec app composer install --no-dev --optimize-autoloader

# 3. Run migrations
docker-compose exec app php bin/console doctrine:migrations:migrate --env=prod --no-interaction

# 4. Clear cache
docker-compose exec app php bin/console cache:clear --env=prod

# 5. Rebuild and restart (Xdebug disabled by default)
docker-compose build app
docker-compose up -d app
```

### Useful Commands

```bash
make down       # stop containers
make bash       # shell into the app container
make exec CMD='php bin/console cache:clear'

# Initialize Webpack Encore assets
mkdir -p public/build
echo "{}" > public/build/manifest.json
echo '{"entrypoints": {}}' > public/build/entrypoints.json
```

## Architecture Notes

The codebase follows a strict layered structure under `src/`:

```
src/
├── Domain/        # entities, value objects, repository interfaces — no framework dependencies
│   ├── Travel/    # Travel aggregate root, repository interfaces
│   ├── Location/  # Location entity, mark, type
│   ├── User/      # User entity, authentication
│   └── Shared/    # Shared kernel, events, exceptions
├── Application/   # commands, queries, handlers, use cases
│   ├── UseCases/  # business logic (Travel/, Location/, User/)
│   └── Command/    # command/query objects
├── Infrastructure/ # implementations (Doctrine repositories, external services)
└── UI/            # controllers, templates, forms
    ├── Controller/ # HTTP and API controllers
    └── templates/  # Twig templates
```

Commands are dispatched through the tactician command bus, which wraps handlers in a Doctrine transaction. Queries return read models directly from the repository. The Go WebSocket server is intentionally decoupled — PHP calls it over HTTP after persisting a change, and the Go server fans the event out to all connected browser clients in that travel's room.

## Database Migrations

```bash
# Create a new migration
docker-compose exec app php bin/console make:migration

# Run pending migrations
docker-compose exec app php bin/console doctrine:migrations:migrate

# Check migration status
docker-compose exec app php bin/console doctrine:migrations:status
```

## Contributing

1. Create a feature branch from `master`
2. Make your changes following the layered architecture
3. Run code quality tools: `phpstan`, `phpcs`, `phpmd`
4. Write tests for new functionality
5. Create a pull request to `master`

# Travel Experience

A personal sandbox for experimenting with backend architecture, infrastructure patterns, and full-stack development techniques. The domain is a collaborative travel planning app — create trips, add locations on a map, schedule them in a calendar, share with other users, and upload photos.

## Tech Stack

### Backend — PHP / Symfony 4
- **Symfony 4** — framework, routing, security, forms, console
- **Doctrine ORM** — MySQL persistence with UUID primary keys (`ramsey/uuid-doctrine`), embeddables, custom mapping types, and Gedmo extensions (sluggable, timestampable)
- **CQRS + Command Bus** — commands and queries dispatched through `league/tactician` with Doctrine transactional middleware
- **Hexagonal Architecture / DDD** — domain, application, and infrastructure layers; no framework leaking into the domain
- **JMS Serializer** — flexible serialization for API responses
- **Symfony Messenger** — async message handling
- **Guzzle** — HTTP client for internal service communication (PHP → Go WebSocket server)
- **Symfony Security** — form-based authentication, role system, travel ownership and sharing model
- **Twig** — server-rendered templates with component includes
- **KnpMarkdownBundle** — markdown support for travel descriptions
- **FOSElasticaBundle** — Elasticsearch integration for search (currently disabled in docker-compose)

### WebSocket Server — Go
A standalone Go service (`PlanningTravelsSocketio/`) that manages real-time collaboration:
- **gorilla/websocket** — WebSocket upgrade and connection management
- Room-based pub/sub: each travel has its own room; clients join via `GET /ws/{travelId}`
- PHP broadcasts events to connected clients via `POST /travel/{travelId}/broadcast`
- Ping/pong keepalive, graceful shutdown

### Frontend
- **jQuery** + vanilla JS — map interactions, drag-and-drop, file uploads (`jquery.fileupload`), sortable lists
- **Leaflet.js** — interactive map with multiple tile layers (OSM, Google Maps, satellite, terrain, traffic), routing machine, GPX track rendering, fullscreen, and print
- **Webpack Encore** — asset bundling
- **React** (via Encore) — used for select components

### Infrastructure
- **Docker Compose** — multi-container setup: PHP/Apache app, Go WebSocket server, MySQL 5.7, Adminer
- **GitHub Actions** — CI pipeline (`.github/workflows/validation.yml`)
- **Xdebug** — remote debugging configured in the PHP container
- **Adminer** — database UI at `localhost:8080`

### Code Quality
- **PHPStan** — static analysis
- **PHP CodeSniffer** + **PHP CS Fixer** — coding standards
- **PHPMD** — mess detection
- **PHPUnit** — unit and functional tests with fixtures

## Running Locally

```bash
make up
```

App: `http://localhost:8000`  
Adminer: `http://localhost:8080` (server: `mysql`, user: `root`, password: `root`)  
WebSocket server: `ws://localhost:5555`

```bash
make down       # stop containers
make bash       # shell into the app container
make exec CMD='php bin/console cache:clear'
```

## Architecture Notes

The codebase follows a strict layered structure under `src/`:

```
src/
├── Domain/        # entities, value objects, repository interfaces — no framework dependencies
├── Application/   # commands, queries, handlers
└── UI/            # controllers, templates, forms
```

Commands are dispatched through the tactician command bus, which wraps handlers in a Doctrine transaction. Queries return read models directly from the repository. The Go WebSocket server is intentionally decoupled — PHP calls it over HTTP after persisting a change, and the Go server fans the event out to all connected browser clients in that travel's room.

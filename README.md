# Travel Experience

A collaborative travel planning application — create trips, add locations on a map, schedule them in a calendar, share with other users, chat in real-time, and upload photos.

Demo: http://35.193.209.157:8000/public/index.php/

## Features

### Travel & Collaboration
- **Travel management** — create, edit, clone, publish/unpublish (draft mode) and delete travels with title, dates, description, cover photo and rating
- **Travel sharing** — invite other users to collaborate (add locations, edit dates, upload images, manage GPX, edit budget) with a shared-users management modal
- **Travel cloning** — duplicate any published travel into your account with full lineage tracking (`travel_clone` table)
- **Read-only public mode** — anyone can view a published travel; only owners and shared users can edit
- **Watch counter** — track travel views
- **Best travels listing** — public discovery page ordered by rating
- **Print / PDF export** — dedicated print view (`/travel/{slug}/print`) optimized for paper and PDF export

### Locations
- **Location management** — add, edit, drag-to-reorder and delete locations with a Google Places marker, type classification (hotel, restaurant, sight, transport…), description and notes
- **Multi-day visits** — assign each location to one or more dates of the trip
- **Time slots** — start/end time per visit-date with inline `<input type="time">` editing
- **Notes per location** — add and remove free-form notes
- **Image gallery** — multi-image upload per location with auto-conversion to WebP (max 1920 px wide), lightbox viewer
- **Driving time between consecutive locations** — computed via OSRM router and shown in the calendar

### Map (showTravel)
- **Multiple basemaps** — OSM, Google Maps roadmap/satellite/terrain/traffic (switchable)
- **Interactive markers** — click to open a side panel with images, notes, weather and details
- **Route panel** — drag locations into a route, calculate driving directions (Leaflet Routing Machine) and reset/remove
- **Easy print** — export the current map view to PNG / A4 portrait/landscape
- **Real-time collaboration overlay** — locations added/removed/updated by other shared users appear instantly via WebSocket

### GPX Tracks
- **Upload GPX files** — dropzone (click or drag-and-drop) with up to 3 MB per file and `.gpx` validation
- **Automatic simplification** — uploaded files are reduced in place using a pure-PHP Douglas–Peucker implementation (`GpxSimplifier`); large tracks (~16 000 pts / 2.4 MB) shrink to ~600 pts / 150 KB without visible loss
- **High-contrast rendering** — every track is drawn with a white halo underneath the colored line so it stands out on any basemap (satellite, roadmap, terrain)
- **Per-track colour picker** — each track has an inline `<input type="color">` swatch with live preview on the map and debounced auto-save
- **Editable title** — rename a track inline (auto-saves on blur or Enter)
- **Distance** — total length in km is computed server-side at upload (haversine) and refined client-side with leaflet-gpx (`get_distance` includes elevation); shown per track and as a cumulated total in the panel header
- **Day assignment** — each track can be linked to a specific day of the trip; the track then appears as a card in the calendar views with its colour and km
- **Toggle / view / delete** — show/hide individual tracks, zoom-to-track button, and one-click deletion
- **Show all** — single button to bulk-show or bulk-hide all tracks and re-fit the map

### Calendar Views
- **Calendar (`/travel/{slug}/calendar-view`)** — vertical day list with weekend/today highlights, month dividers, drag-from-unscheduled and per-day add picker; supports inline time editing and removing locations from a day; GPX tracks assigned to a day appear as cards
- **Calendar + Route (`/travel/{slug}/calendar`)** — calendar on the left, Leaflet route map on the right with three modes (All days / Selected day / Full trip), per-day colour-coded markers and polylines, OSRM driving-time badges between consecutive locations, GPX tracks shown for the assigned day
- **Reusable navigation tabs** — Map / Calendar / Calendar+Route / Budget tabs plus Edit / Print / Clone actions are available on every travel view

### Budget
- **Set a travel budget** — amount + currency (EUR, USD, GBP, CHF, JPY)
- **Add expenses** — description, amount, currency, category (accommodation, transport, food, activities, shopping, other) and optional date
- **Live summary bar** — total budget, spent, remaining (with over-budget detection), and a colour-graded progress bar
- **Breakdown by category** — coloured donut/list per category
- **Expense list** — sortable list with delete
- **All budget data is included in the print view**

### Weather
- **Travel forecast widget** — daily forecast for each day of the trip (clickable on calendar entries), backed by a cached OpenWeatherMap provider (`CachedWeatherProvider`, 1 h TTL)

### Real-time (WebSocket)
- **Per-travel rooms** — clients join `GET /ws/{travelId}`, server fans events to everyone in that room
- **Live events** — `location_added`, `location_removed`, `location_updated`, `visit_date_changed`, `visit_dates_changed`, `image_uploaded`, `note_added`, `note_deleted`, plus chat messages
- **Chat per travel** — send and receive chat messages tied to a travel
- **Resilient** — PHP broadcasts are fire-and-forget; WS server failures never block writes

### API Endpoints (selection)
- `POST /api/user/{userId}/location` — create a location
- `PATCH /api/location/{id}` — update title/description/type
- `DELETE /api/travel/{travel}/location/{location}` — delete a location
- `PATCH /api/location/{id}/visit-date` — add/remove a visit date
- `PATCH /api/location/{id}/visit-time` — set start/end times
- `POST /api/locations/positions` — bulk reorder visit positions
- `POST /api/location/{id}/image` — upload image (auto WebP)
- `GET/POST/DELETE /api/location/{id}/notes[/{noteId}]` — manage notes
- `POST /api/travel/{id}/gpx` — upload GPX (auto-simplified, distance computed)
- `PATCH /api/gpx/{id}` — update title, color or visitDate
- `DELETE /api/gpx/{id}` — delete a GPX track
- `GET/POST /api/travel/{id}/budget` — read/save budget
- `POST /api/travel/{id}/expense` — add expense
- `DELETE /api/expense/{id}` — delete expense
- `POST /api/travel/{id}/share` and `DELETE /api/travel/{id}/share/{username}` — share/unshare
- `POST /api/travel/{id}/clone` — clone a travel
- `GET /api/travel/{slug}/weather` — daily forecast
- `POST /api/travel/{id}/chat` — send chat message

### Console Commands
- `app:initialize-positions` — backfill `position` for existing `location_visit_date` rows
- `app:gpx:backfill-distance` — compute and store `distance` for legacy GPX tracks
- `app:populate-elasticsearch` — populate the Elasticsearch index (when enabled)

### Authentication
- Email + password sign-up / sign-in
- Forgotten-password flow with token-based reset email
- Profile editing (display name, language, avatar)

## Tech Stack

### Backend — PHP 8.2 / Symfony 7
- **Symfony 7.0** — framework, routing, security (CSRF protection), forms, console
- **Doctrine ORM** — MySQL persistence with UUID primary keys (`ramsey/uuid-doctrine`), embeddables, custom mapping types, and Gedmo extensions (sluggable, timestampable)
- **CQRS + Command/Query Buses** — commands and queries dispatched through Symfony Messenger with two separate buses (`command.bus` and `query.bus`); `doctrine_transaction` middleware wraps command handlers in a Doctrine transaction; a custom `DomainEventsMiddleware` collects and dispatches domain events after each command
- **Hexagonal Architecture / DDD** — domain, application, and infrastructure layers; no framework leaking into the domain
- **JMS Serializer** — flexible serialization for API responses
- **Symfony Messenger** — async message handling
- **Guzzle 7** — HTTP client for internal service communication (PHP → Go WebSocket server)
- **Symfony Security** — form-based authentication, role system (ROLE_USER, ROLE_ADMIN), travel ownership and sharing model
- **Twig 3** — server-rendered templates with component includes
- **Webpack Encore** — asset bundling with React components
- **phayes/geophp** — GPX parsing helpers (used together with a custom Douglas–Peucker simplifier and a haversine distance calculator)
- **OpenWeatherMap** — daily forecast provider, wrapped behind a domain interface and a cache decorator

### WebSocket Server — Go
A standalone Go service (`PlanningTravelsSocketio/`) that manages real-time collaboration:
- **gorilla/websocket** — WebSocket upgrade and connection management (Go 1.20)
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
- (Optional) PHP 8.2+ and Composer for local development

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

Commands are dispatched through Symfony Messenger's `command.bus`, which wraps handlers in a Doctrine transaction via the `doctrine_transaction` middleware. A custom `DomainEventsMiddleware` collects and dispatches domain events after each command. Queries are dispatched through a separate `query.bus` and return read models directly from the repository. The Go WebSocket server is intentionally decoupled — PHP calls it over HTTP after persisting a change, and the Go server fans the event out to all connected browser clients in that travel's room.

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

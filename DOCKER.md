# Docker Infrastructure for Laravel LLM Content Platform

This project includes a complete Docker setup with PostgreSQL, Nginx, and Ollama for local development and production deployment.

## Services

- **app**: PHP 8.2 FPM with Laravel application
- **nginx**: Nginx web server (Alpine)
- **postgres**: PostgreSQL 16 database (Alpine)
- **ollama**: Ollama LLM service
- **node**: Node.js 20 for Vite development (optional, dev profile)

## Prerequisites

- Docker Engine 20.10+
- Docker Compose 2.0+
- (Optional) NVIDIA GPU drivers and nvidia-docker2 for GPU-accelerated Ollama

## Quick Start

### 1. Initial Setup

```bash
# Copy environment file
cp .env.example .env

# Update APP_KEY if needed
# You can generate one using: docker compose run --rm app php artisan key:generate
```

### 2. Start Services

```bash
# Build and start all services
docker compose up -d

# Or use the Makefile (if available)
make up
```

### 3. Install Dependencies and Setup Application

```bash
# Install Composer dependencies
docker compose exec app composer install

# Generate application key
docker compose exec app php artisan key:generate

# Run migrations
docker compose exec app php artisan migrate

# (Optional) Seed database
docker compose exec app php artisan db:seed
```

### 4. Build Frontend Assets

**Option A: Build once**
```bash
docker compose run --rm node npm install
docker compose run --rm node npm run build
```

**Option B: Run Vite dev server (recommended for development)**
```bash
# Start the node service for hot-reload
docker compose --profile dev up -d node

# Access Vite dev server at http://localhost:5173
```

### 5. Access the Application

- **Application**: http://localhost
- **Ollama API**: http://localhost:11434
- **PostgreSQL**: localhost:5432

## Common Commands

### Service Management

```bash
# Start all services
docker compose up -d

# Stop all services
docker compose down

# Restart a specific service
docker compose restart app

# View logs
docker compose logs -f app

# View all logs
docker compose logs -f
```

### Laravel Artisan Commands

```bash
# Run artisan commands
docker compose exec app php artisan migrate
docker compose exec app php artisan tinker
docker compose exec app php artisan queue:work

# Clear caches
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:clear
docker compose exec app php artisan view:clear
```

### Database Management

```bash
# Access PostgreSQL CLI
docker compose exec postgres psql -U laravel -d laravel

# Backup database
docker compose exec postgres pg_dump -U laravel laravel > backup.sql

# Restore database
docker compose exec -T postgres psql -U laravel laravel < backup.sql

# Fresh migration
docker compose exec app php artisan migrate:fresh --seed
```

### Ollama Commands

```bash
# Pull a model (e.g., llama2)
docker compose exec ollama ollama pull llama2

# List installed models
docker compose exec ollama ollama list

# Run a model interactively
docker compose exec ollama ollama run llama2

# Test Ollama API
curl http://localhost:11434/api/generate -d '{
  "model": "llama2",
  "prompt": "Hello, world!"
}'
```

### Development Workflow

```bash
# Watch logs
docker compose logs -f app nginx

# Run tests
docker compose exec app php artisan test

# Run code formatting
docker compose exec app ./vendor/bin/pint

# Install a new Composer package
docker compose exec app composer require package/name

# Install a new NPM package
docker compose run --rm node npm install package-name
```

## Configuration

### Environment Variables

Edit `.env` file to configure:

- `APP_PORT`: Nginx port (default: 80)
- `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`: Database credentials
- `OLLAMA_PORT`: Ollama API port (default: 11434)
- `VITE_PORT`: Vite dev server port (default: 5173)

### GPU Support for Ollama

If you have an NVIDIA GPU, uncomment the deploy section in `docker-compose.yml`:

```yaml
ollama:
  # ...
  deploy:
    resources:
      reservations:
        devices:
          - driver: nvidia
            count: all
            capabilities: [gpu]
```

### PHP Configuration

- Development: `docker/php/php.ini`
- Production: `docker/php/php-production.ini`

### Nginx Configuration

Edit `docker/nginx/nginx.conf` to customize web server settings.

## Production Deployment

### 1. Update Environment

```bash
cp .env.example .env
# Edit .env and set:
# APP_ENV=production
# APP_DEBUG=false
# Update APP_URL, DB credentials, etc.
```

### 2. Build Production Image

```bash
# Build with production target
docker compose -f docker-compose.yml build --target production
```

### 3. Optimize Application

```bash
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache
docker compose exec app php artisan optimize
```

### 4. Build Frontend

```bash
docker compose run --rm node npm run build
```

## Volumes

- `postgres_data`: PostgreSQL data persistence
- `ollama_data`: Ollama models and data persistence

## Networking

All services communicate through the `laravel-network` bridge network.

## Troubleshooting

### Permission Issues

```bash
# Fix storage and cache permissions
docker compose exec app chown -R www:www /var/www/storage /var/www/bootstrap/cache
docker compose exec app chmod -R 775 /var/www/storage /var/www/bootstrap/cache
```

### Service Not Starting

```bash
# Check logs
docker compose logs app

# Rebuild containers
docker compose down
docker compose build --no-cache
docker compose up -d
```

### Database Connection Issues

```bash
# Check if PostgreSQL is healthy
docker compose ps

# Test connection
docker compose exec app php artisan tinker
>>> DB::connection()->getPdo();
```

### Ollama Model Not Loading

```bash
# Check Ollama logs
docker compose logs ollama

# Ensure model is pulled
docker compose exec ollama ollama list

# Pull model if needed
docker compose exec ollama ollama pull llama2
```

## Development Tips

1. **Hot Reload**: Use the node service with `--profile dev` for Vite hot-reload
2. **Database GUI**: Connect to PostgreSQL at `localhost:5432` with your favorite client
3. **Queue Workers**: Run `docker compose exec app php artisan queue:work` for background jobs
4. **Scheduler**: Add cron job: `* * * * * docker compose exec app php artisan schedule:run`

## Security Notes

- Change default database credentials in production
- Use strong `APP_KEY` (generate with `php artisan key:generate`)
- Configure firewall rules for exposed ports
- Use HTTPS in production (configure SSL in nginx)
- Keep Docker images updated

## License

This Docker configuration is part of the Laravel LLM Content Platform project.


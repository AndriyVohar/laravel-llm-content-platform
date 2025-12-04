# Laravel LLM Content Platform

A modern content platform powered by Laravel 12, Inertia.js, and Large Language Models (LLMs) via Ollama, running in a containerized Docker environment.

## 🚀 Features

- **Laravel 12**: Latest version of the Laravel framework
- **Inertia.js**: Modern monolith architecture with SPA experience
- **PostgreSQL**: Robust relational database
- **Ollama Integration**: Local LLM capabilities for AI-powered content generation
- **Docker Infrastructure**: Complete containerized environment for consistent development and deployment
- **Nginx**: High-performance web server
- **Vite**: Lightning-fast frontend build tool with hot module replacement

## 📋 Prerequisites

- **Docker Engine** 20.10 or higher
- **Docker Compose** 2.0 or higher
- **(Optional)** NVIDIA GPU with nvidia-docker2 for GPU-accelerated LLMs
- **(Optional)** Make utility for convenience commands

## 🛠️ Quick Start

### Automated Setup (Recommended)

The fastest way to get started is using the automated setup script:

```bash
./docker-setup.sh
```

This script will:
1. Create your `.env` file from `.env.example`
2. Build Docker images
3. Start all services
4. Install dependencies
5. Run migrations
6. Build frontend assets

### Manual Setup

If you prefer manual setup or need more control:

```bash
# 1. Copy environment file
cp .env.example .env

# 2. Build and start services
docker compose up -d

# 3. Install dependencies
docker compose exec app composer install

# 4. Generate application key
docker compose exec app php artisan key:generate

# 5. Run migrations
docker compose exec app php artisan migrate

# 6. Install frontend dependencies and build
docker compose run --rm node npm install
docker compose run --rm node npm run build
```

### Using Makefile (Convenience)

If you have `make` installed, you can use convenient shortcuts:

```bash
# Complete installation
make install

# Start services
make up

# View all available commands
make help
```

## 🐳 Docker Services

The application runs on four main services:

| Service | Description | Port | Container Name |
|---------|-------------|------|----------------|
| **app** | PHP 8.2 FPM with Laravel | 9000 | laravel_app |
| **nginx** | Nginx web server | 80 | laravel_nginx |
| **postgres** | PostgreSQL 16 database | 5432 | laravel_postgres |
| **ollama** | Ollama LLM service | 11434 | laravel_ollama |
| **node** | Node.js for Vite (dev only) | 5173 | laravel_node |

## 🔗 Access Points

After starting the services:

- **Application**: http://localhost
- **Vite Dev Server**: http://localhost:5173 (when running with dev profile)
- **Ollama API**: http://localhost:11434
- **PostgreSQL**: localhost:5432

Default database credentials:
- Database: `laravel`
- Username: `laravel`
- Password: `secret`

## 📚 Common Commands

### Service Management

```bash
# Start all services
docker compose up -d

# Start with Vite dev server (hot reload)
docker compose --profile dev up -d

# Stop all services
docker compose down

# Restart services
docker compose restart

# View logs
docker compose logs -f

# View specific service logs
docker compose logs -f app
```

### Laravel Artisan

```bash
# Run migrations
docker compose exec app php artisan migrate

# Create a migration
docker compose exec app php artisan make:migration create_posts_table

# Run seeders
docker compose exec app php artisan db:seed

# Clear caches
docker compose exec app php artisan cache:clear

# Run tinker
docker compose exec app php artisan tinker

# Run tests
docker compose exec app php artisan test
```

### Database Operations

```bash
# Access PostgreSQL CLI
docker compose exec postgres psql -U laravel -d laravel

# Backup database
docker compose exec postgres pg_dump -U laravel laravel > backup.sql

# Restore database
docker compose exec -T postgres psql -U laravel laravel < backup.sql

# Fresh migration with seeding
docker compose exec app php artisan migrate:fresh --seed
```

### Ollama (LLM) Management

```bash
# Pull a model (e.g., llama2, llama3, mistral, phi)
docker compose exec ollama ollama pull llama2

# List installed models
docker compose exec ollama ollama list

# Run interactive chat
docker compose exec ollama ollama run llama2

# Test API
curl http://localhost:11434/api/generate -d '{
  "model": "llama2",
  "prompt": "Hello, world!",
  "stream": false
}'
```

### Frontend Development

```bash
# Install NPM packages
docker compose run --rm node npm install

# Build for production
docker compose run --rm node npm run build

# Run dev server with hot reload
docker compose --profile dev up -d node

# Install a new package
docker compose run --rm node npm install package-name
```

## 🔧 Configuration

### Environment Variables

Key environment variables in `.env`:

```env
# Application
APP_NAME="Laravel LLM Platform"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost
APP_PORT=80

# Database
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=secret

# Ollama
OLLAMA_HOST=http://ollama:11434
OLLAMA_PORT=11434

# Vite
VITE_PORT=5173
```

### GPU Support for Ollama

If you have an NVIDIA GPU, enable GPU support in `docker-compose.yml`:

```yaml
ollama:
  deploy:
    resources:
      reservations:
        devices:
          - driver: nvidia
            count: all
            capabilities: [gpu]
```

### PHP Configuration

- **Development**: `docker/php/php.ini`
- **Production**: `docker/php/php-production.ini`

Key settings:
- Memory limit: 512M (dev), 256M (prod)
- Upload max filesize: 100M (dev), 50M (prod)
- Max execution time: 300s (dev), 60s (prod)

### Nginx Configuration

Edit `docker/nginx/nginx.conf` to customize:
- Server name
- SSL/HTTPS settings
- Client body size
- Timeouts
- Cache settings

## 🧪 Testing

```bash
# Run all tests
docker compose exec app php artisan test

# Run specific test
docker compose exec app php artisan test --filter TestName

# Run with coverage (requires Xdebug)
docker compose exec app php artisan test --coverage
```

## 🎨 Code Style

```bash
# Check code style
docker compose exec app ./vendor/bin/pint --test

# Fix code style
docker compose exec app ./vendor/bin/pint
```

## 🚀 Production Deployment

### Build for Production

```bash
# Use production compose file
docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d

# Optimize Laravel
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache
docker compose exec app php artisan optimize

# Build frontend assets
docker compose run --rm node npm run build
```

### Security Checklist

- [ ] Change default database credentials
- [ ] Set strong `APP_KEY`
- [ ] Set `APP_ENV=production` and `APP_DEBUG=false`
- [ ] Configure SSL/HTTPS in Nginx
- [ ] Set up firewall rules
- [ ] Configure proper backup strategy
- [ ] Review and update CORS settings
- [ ] Set up monitoring and logging

## 📖 Additional Documentation

- [Docker Setup Guide](DOCKER.md) - Detailed Docker infrastructure documentation
- [Laravel Documentation](https://laravel.com/docs)
- [Inertia.js Documentation](https://inertiajs.com)
- [Ollama Documentation](https://ollama.ai)

## 🛟 Troubleshooting

### Services Not Starting

```bash
# Check service status
docker compose ps

# View detailed logs
docker compose logs app

# Rebuild containers
docker compose down
docker compose build --no-cache
docker compose up -d
```

### Permission Issues

```bash
# Fix permissions
docker compose exec -u root app chown -R www:www /var/www/storage /var/www/bootstrap/cache
docker compose exec -u root app chmod -R 775 /var/www/storage /var/www/bootstrap/cache
```

### Database Connection Issues

```bash
# Test database connection
docker compose exec app php artisan tinker
>>> DB::connection()->getPdo();

# Check PostgreSQL health
docker compose exec postgres pg_isready -U laravel
```

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 🙏 Acknowledgments

Built with:
- [Laravel](https://laravel.com) - The PHP Framework
- [Inertia.js](https://inertiajs.com) - Modern Monolith
- [Ollama](https://ollama.ai) - Local LLM Runtime
- [PostgreSQL](https://www.postgresql.org) - Advanced Database
- [Nginx](https://nginx.org) - High-Performance Web Server
- [Docker](https://www.docker.com) - Containerization Platform

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

# Laravel LLM Content Platform

A beautiful, modern AI chat application powered by Laravel 12, Vue 3, and AI Service for flexible LLM integration. Features a stunning gradient UI with real-time AI responses.

## ✨ Features

### AI Chat Interface
- 🎨 **Beautiful Modern UI**: Gradient backgrounds, smooth animations, and polished design
- 💬 **Real-time Chat**: Instant AI responses with typing indicators
- 🤖 **AI Service Integration**: Support for multiple LLM providers (DeepInfra, OpenAI, etc.)
- 📜 **Message History**: Maintains conversation context with timestamps
- 🗑️ **Clear Chat**: Easy conversation reset
- ⚡ **Fast & Responsive**: Optimized for performance
- 🎯 **Auto-scroll**: Automatically follows conversation

### Technical Stack
- **Laravel 12**: Latest version of the Laravel framework
- **Vue 3 + Inertia.js**: Modern SPA experience with server-side rendering
- **Tailwind CSS 4**: Beautiful, customizable styling
- **AI Service**: Flexible LLM integration with multiple providers
- **PostgreSQL**: Robust relational database
- **Docker**: Complete containerized environment
- **Vite**: Lightning-fast frontend build tool

## 🎯 Quick Start

### Option 1: Local Development (Recommended)

1. **Setup Laravel Application**
```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

2. **Configure AI Service (in .env)**
```env
AI_SERVICE_URL=http://localhost:8000
AI_SERVICE_PROVIDER=deepinfra
AI_SERVICE_TIMEOUT=30
```

3. **Start Development Servers**
```bash
# Terminal 1
php artisan serve

# Terminal 2
npm run dev
```

4. **Open Your Browser**
```
http://localhost:8000
```

Note: Make sure your AI Service backend is running on `http://localhost:8000`. See [INTEGRATION.md](INTEGRATION.md) for setup instructions.

### Option 2: Docker Environment

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
| **node** | Node.js for Vite (dev only) | 5173 | laravel_node |

## 🔗 Access Points

After starting the services:

- **Application**: http://localhost
- **Vite Dev Server**: http://localhost:5173 (when running with dev profile)
- **AI Service API**: http://localhost:8000 (must be started separately)
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

# AI Service
AI_SERVICE_URL=http://localhost:8000
AI_SERVICE_PROVIDER=deepinfra
AI_SERVICE_TIMEOUT=30

# Vite
VITE_PORT=5173
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

## 💬 Using the AI Chat

### Getting Started

1. **Make sure AI Service is running**
```bash
# Check AI Service status
curl http://localhost:8000/api/health
# Should return: {"status": "ok"}
```

2. **Open the application**
   - Navigate to `http://localhost:8000` (or `http://localhost` if using Docker)
   - You'll see the beautiful AI Chat interface

3. **Start chatting!**
   - Type your message in the input field
   - Press Enter or click "Відправити" (Send)
   - Watch as the AI responds in real-time

### Features

- **Message History**: Your conversation is maintained during the session
- **Timestamps**: Each message shows the time it was sent
- **Loading Indicator**: Animated dots show when AI is thinking
- **Clear Chat**: Click the trash button to start a new conversation
- **Auto-scroll**: Chat automatically scrolls to show new messages
- **Error Handling**: Helpful error messages if AI Service isn't available

### Changing the AI Provider

Edit your `.env` file:
```env
AI_SERVICE_PROVIDER=deepinfra      # Default - DeepInfra provider
# AI_SERVICE_PROVIDER=openai       # OpenAI provider
# AI_SERVICE_PROVIDER=your_provider # Custom provider
```

### Available Providers

For a list of available providers and their models, check your AI Service documentation or run:

```bash
# Get available providers from AI Service
curl http://localhost:8000/api/providers
```

### Customization

Want to customize the UI? Check out these files:
- **Frontend**: `resources/js/Pages/Home.vue`
- **Backend**: `app/Http/Controllers/AIChatController.php`
- **Service**: `app/Services/AIServiceClient.php`
- **Config**: `config/services.php`

See [DEVELOPMENT.md](DEVELOPMENT.md) for detailed customization guide.

### Troubleshooting

**"Error: Unable to get response from AI Service"**
```bash
# Make sure AI Service is running
# Check the AI_SERVICE_URL in your .env file
# Default: http://localhost:8000
```

**Slow responses?**
- Check AI Service status: `curl http://localhost:8000/api/health`
- Increase timeout in `.env`: `AI_SERVICE_TIMEOUT=60`
- Verify network connection to AI Service

**Chat not updating?**
```bash
# Clear browser cache and rebuild assets
npm run build
# Or restart dev server
npm run dev
```

## 📖 Documentation

- [INTEGRATION.md](INTEGRATION.md) - AI Service integration guide
- [QUICKSTART.md](QUICKSTART.md) - Get up and running in 5 minutes
- [DEVELOPMENT.md](DEVELOPMENT.md) - Detailed development guide
- [DOCKER.md](DOCKER.md) - Docker configuration details

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 🙏 Acknowledgments

Built with:
- [Laravel](https://laravel.com) - The PHP Framework
- [Inertia.js](https://inertiajs.com) - Modern Monolith
- [PostgreSQL](https://www.postgresql.org) - Advanced Database
- [Nginx](https://nginx.org) - High-Performance Web Server
- [Docker](https://www.docker.com) - Containerization Platform

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

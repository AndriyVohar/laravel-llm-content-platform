#!/bin/bash
# Docker cleanup and restart script for Laravel LLM Content Platform

echo "================================================"
echo "Cleaning up Docker containers and networks..."
echo "================================================"

# Stop and remove all containers related to this project
docker compose down -v

echo ""
echo "================================================"
echo "Removing any stale containers on port 11434..."
echo "================================================"

# Find and kill any process using port 11434
if command -v lsof &> /dev/null; then
    PID=$(lsof -t -i :11434)
    if [ ! -z "$PID" ]; then
        echo "Killing process on port 11434 (PID: $PID)"
        kill -9 $PID 2>/dev/null || true
    fi
fi

echo ""
echo "================================================"
echo "Waiting 5 seconds for ports to be released..."
echo "================================================"
sleep 5

echo ""
echo "================================================"
echo "Starting containers with new configuration..."
echo "================================================"

# Start all services
docker compose up -d --build

echo ""
echo "================================================"
echo "Running database migrations..."
echo "================================================"

# Wait for app container to be ready
sleep 5

# Run migrations
docker compose exec -T app php artisan migrate

echo ""
echo "================================================"
echo "Setup Complete!"
echo "================================================"
echo ""
echo "Access your application at: http://llm.localhost"
echo "Ollama API at: http://localhost:11435"
echo "PostgreSQL at: localhost:5432"
echo ""
echo "Container Status:"
docker compose ps


#!/bin/bash

# Health check script for monitoring Docker services
# Usage: ./docker-health-check.sh

set -e

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo "Checking Docker services health..."
echo ""

# Function to check service
check_service() {
    local service=$1
    local status=$(docker compose ps --format json $service | grep -o '"Status":"[^"]*"' | cut -d'"' -f4 || echo "not found")

    if [[ $status == *"Up"* ]]; then
        echo -e "${GREEN}✓${NC} $service: Running"
        return 0
    else
        echo -e "${RED}✗${NC} $service: $status"
        return 1
    fi
}

# Check all services
services=("app" "nginx" "postgres" "ollama")
all_ok=true

for service in "${services[@]}"; do
    if ! check_service $service; then
        all_ok=false
    fi
done

echo ""

# Check PostgreSQL connection
echo "Testing PostgreSQL connection..."
if docker compose exec -T postgres pg_isready -U laravel &>/dev/null; then
    echo -e "${GREEN}✓${NC} PostgreSQL is accepting connections"
else
    echo -e "${RED}✗${NC} PostgreSQL is not accepting connections"
    all_ok=false
fi

# Check Ollama API
echo "Testing Ollama API..."
if curl -s http://localhost:11434/api/version &>/dev/null; then
    echo -e "${GREEN}✓${NC} Ollama API is responding"
else
    echo -e "${YELLOW}!${NC} Ollama API is not responding (may be starting)"
fi

# Check Nginx
echo "Testing Nginx..."
if curl -s -o /dev/null -w "%{http_code}" http://localhost | grep -q "200\|302\|301"; then
    echo -e "${GREEN}✓${NC} Nginx is responding"
else
    echo -e "${RED}✗${NC} Nginx is not responding"
    all_ok=false
fi

echo ""

if [ "$all_ok" = true ]; then
    echo -e "${GREEN}All critical services are healthy!${NC}"
    exit 0
else
    echo -e "${RED}Some services are not healthy. Check logs with: docker compose logs${NC}"
    exit 1
fi


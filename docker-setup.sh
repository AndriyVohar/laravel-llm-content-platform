#!/bin/bash

# Laravel LLM Platform - Docker Setup Script
# This script helps you get started with the Docker environment

set -e

# Colors
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${BLUE}======================================${NC}"
echo -e "${BLUE}Laravel LLM Platform - Docker Setup${NC}"
echo -e "${BLUE}======================================${NC}"
echo ""

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    echo -e "${RED}Error: Docker is not installed. Please install Docker first.${NC}"
    exit 1
fi

# Check if Docker Compose is installed
if ! command -v docker compose &> /dev/null; then
    echo -e "${RED}Error: Docker Compose is not installed. Please install Docker Compose first.${NC}"
    exit 1
fi

echo -e "${GREEN}✓ Docker and Docker Compose are installed${NC}"
echo ""

# Check if .env file exists
if [ ! -f .env ]; then
    echo -e "${YELLOW}Creating .env file from .env.example...${NC}"
    cp .env.example .env
    echo -e "${GREEN}✓ .env file created${NC}"
else
    echo -e "${YELLOW}.env file already exists. Skipping...${NC}"
fi

echo ""
echo -e "${BLUE}Building Docker images...${NC}"
docker compose build

echo ""
echo -e "${BLUE}Starting services...${NC}"
docker compose up -d

echo ""
echo -e "${YELLOW}Waiting for services to be ready...${NC}"
sleep 10

echo ""
echo -e "${BLUE}Installing Composer dependencies...${NC}"
docker compose exec app composer install --no-interaction

echo ""
echo -e "${BLUE}Generating application key...${NC}"
docker compose exec app php artisan key:generate

echo ""
echo -e "${BLUE}Running database migrations...${NC}"
docker compose exec app php artisan migrate --force

echo ""
echo -e "${BLUE}Installing NPM dependencies...${NC}"
docker compose run --rm node npm install

echo ""
echo -e "${BLUE}Building frontend assets...${NC}"
docker compose run --rm node npm run build

echo ""
echo -e "${GREEN}======================================${NC}"
echo -e "${GREEN}Setup Complete!${NC}"
echo -e "${GREEN}======================================${NC}"
echo ""
echo -e "${BLUE}Your application is ready!${NC}"
echo ""
echo -e "Access your application at: ${GREEN}http://localhost${NC}"
echo -e "Ollama API available at: ${GREEN}http://localhost:11434${NC}"
echo -e "PostgreSQL available at: ${GREEN}localhost:5432${NC}"
echo ""
echo -e "${YELLOW}Next Steps:${NC}"
echo -e "  1. Pull an Ollama model: ${BLUE}docker compose exec ollama ollama pull llama2${NC}"
echo -e "  2. View logs: ${BLUE}docker compose logs -f${NC}"
echo -e "  3. Run tests: ${BLUE}docker compose exec app php artisan test${NC}"
echo -e "  4. For development with hot-reload: ${BLUE}docker compose --profile dev up -d node${NC}"
echo ""
echo -e "${YELLOW}Helpful Commands:${NC}"
echo -e "  - Stop services: ${BLUE}docker compose down${NC}"
echo -e "  - Restart services: ${BLUE}docker compose restart${NC}"
echo -e "  - View all commands: ${BLUE}make help${NC} (if make is installed)"
echo ""
echo -e "${GREEN}Happy coding!${NC}"


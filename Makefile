# =============================================
# Makefile para Dever.io - Sistema de Gerenciamento de Tarefas
# =============================================

.PHONY: help up down build restart logs bash composer migrate seed setup test clean setup-with-seed

# Cores para output
GREEN := \033[0;32m
BLUE := \033[0;34m
YELLOW := \033[1;33m
RED := \033[0;31m
NC := \033[0m # No Color

# =============================================
# HELP - Comandos disponíveis
# =============================================
help:
	@echo "$(BLUE)🚀 Dever.io - Sistema de Gerenciamento de Tarefas$(NC)"
	@echo ""
	@echo "$(YELLOW)Comandos disponíveis:$(NC)"
	@echo "  $(GREEN)make help$(NC)       - mostra esta ajuda"
	@echo ""
	@echo "$(YELLOW)🐳 Docker:$(NC)"
	@echo "  $(GREEN)make up$(NC)         - sobe os containers"
	@echo "  $(GREEN)make down$(NC)       - derruba os containers"
	@echo "  $(GREEN)make build$(NC)      - builda os containers"
	@echo "  $(GREEN)make restart$(NC)    - reinicia os containers"
	@echo "  $(GREEN)make logs$(NC)       - mostra logs dos containers"
	@echo "  $(GREEN)make logs-php$(NC)   - mostra logs do PHP"
	@echo "  $(GREEN)make bash$(NC)       - entra no container PHP"
	@echo ""
	@echo "$(YELLOW)📦 Dependências:$(NC)"
	@echo "  $(GREEN)make composer$(NC)   - instala dependências PHP"
	@echo "  $(GREEN)make migrate$(NC)    - roda migrations do banco"
	@echo "  $(GREEN)make seed$(NC)       - popula banco com dados demo"
	@echo ""
	@echo "$(YELLOW)⚡ Setup:$(NC)"
	@echo "  $(GREEN)make setup$(NC)      - setup completo (composer + migrate)"
	@echo "  $(GREEN)make setup-full$(NC) - setup completo + seed (dados demo)"
	@echo ""
	@echo "$(YELLOW)🧪 Testes:$(NC)"
	@echo "  $(GREEN)make test$(NC)       - roda todos os testes"
	@echo "  $(GREEN)make test-unit$(NC)  - roda apenas testes unitários"
	@echo ""
	@echo "$(YELLOW)🧹 Limpeza:$(NC)"
	@echo "  $(GREEN)make clean$(NC)      - limpa containers e volumes"
	@echo "  $(GREEN)make clean-all$(NC)  - limpa tudo (containers, volumes, imagens)"
	@echo ""
	@echo "$(YELLOW)📊 Status:$(NC)"
	@echo "  $(GREEN)make status$(NC)     - mostra status dos containers"
	@echo "  $(GREEN)make ps$(NC)         - lista containers em execução"

# =============================================
# DOCKER - Gerenciamento de containers
# =============================================
up:
	@echo "$(BLUE)🐳 Subindo containers...$(NC)"
	docker compose up -d
	@echo "$(GREEN)✅ Containers iniciados!$(NC)"
	@echo "$(YELLOW)📍 Aplicação disponível em: http://localhost:8080$(NC)"

down:
	@echo "$(BLUE)🐳 Derrubando containers...$(NC)"
	docker compose down
	@echo "$(GREEN)✅ Containers parados!$(NC)"

build:
	@echo "$(BLUE)🔨 Buildando containers...$(NC)"
	docker compose build --no-cache
	@echo "$(GREEN)✅ Build concluído!$(NC)"

restart: down up

logs:
	@echo "$(BLUE)📋 Logs de todos os containers:$(NC)"
	docker compose logs -f

logs-php:
	@echo "$(BLUE)📋 Logs do container PHP:$(NC)"
	docker compose logs -f php

bash:
	@echo "$(BLUE)🐚 Entrando no container PHP...$(NC)"
	docker compose exec php bash

# =============================================
# DEPENDÊNCIAS - Composer e Banco
# =============================================
composer:
	@echo "$(BLUE)📦 Instalando dependências PHP...$(NC)"
	docker compose exec php composer install --no-interaction --optimize-autoloader
	@echo "$(GREEN)✅ Dependências instaladas!$(NC)"

migrate:
	@echo "$(BLUE)🗄️ Executando migrations...$(NC)"
	docker compose exec php php /var/www/html/yii migrate --interactive=0
	@echo "$(GREEN)✅ Migrations executadas!$(NC)"

seed:
	@echo "$(BLUE)🌱 Populando banco com dados de demonstração...$(NC)"
	docker compose exec php php /var/www/html/yii seed
	@echo "$(GREEN)✅ Seed executado!$(NC)"

# =============================================
# SETUP - Configuração completa
# =============================================
setup: up composer migrate
	@echo ""
	@echo "$(GREEN)🎉 Setup básico concluído!$(NC)"
	@echo "$(YELLOW)📍 Aplicação disponível em: http://localhost:8080$(NC)"
	@echo "$(YELLOW)🔑 Para popular com dados demo: make seed$(NC)"

setup-full: up composer migrate seed
	@echo ""
	@echo "$(GREEN)🎉 Setup completo com dados demo concluído!$(NC)"
	@echo "$(YELLOW)📍 Aplicação disponível em: http://localhost:8080$(NC)"
	@echo "$(YELLOW)🔑 Login: admin@dever.io / admin123$(NC)"

# =============================================
# TESTES - PHPUnit
# =============================================
test:
	@echo "$(BLUE)🧪 Executando todos os testes...$(NC)"
	docker compose exec php vendor/bin/phpunit --configuration /var/www/html/phpunit.xml
	@echo "$(GREEN)✅ Testes concluídos!$(NC)"

test-unit:
	@echo "$(BLUE)🧪 Executando testes unitários...$(NC)"
	docker compose exec php vendor/bin/phpunit --testsuite Unit --configuration /var/www/html/phpunit.xml
	@echo "$(GREEN)✅ Testes unitários concluídos!$(NC)"

# =============================================
# LIMPEZA - Containers e volumes
# =============================================
clean:
	@echo "$(BLUE)🧹 Limpando containers e volumes...$(NC)"
	docker compose down -v
	docker system prune -f
	@echo "$(GREEN)✅ Limpeza concluída!$(NC)"

clean-all: clean
	@echo "$(BLUE)🧹 Limpando imagens não utilizadas...$(NC)"
	docker image prune -f
	docker volume prune -f
	@echo "$(GREEN)✅ Limpeza completa concluída!$(NC)"

# =============================================
# STATUS - Informações dos containers
# =============================================
status:
	@echo "$(BLUE)📊 Status dos containers:$(NC)"
	docker compose ps

ps: status
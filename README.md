# Dever.io

**Sistema de Gerenciamento de Tarefas para Desenvolvedores**

Este sistema foi desenvolvido por Thomaz Juliann Boncompagni, para o teste técnico da vaga de Desenvolvedor Fullstack PHP Pleno da Empresa Leme Forense. É um Sistema completo para gerenciar projetos e tarefas com colaboração em equipe, upload de arquivos e dashboard em tempo real. Partes do Front-End foram escritas com auxílio do Claude Opus 4.6 para melhor organização das classes utilizadas para estilização e eficiência. 

---

## Stack Tecnológica

| Camada      | Tecnologia                   |
|-------------|------------------------------|
| Backend     | PHP 8.3 + Yii2 Framework     |
| Frontend    | Tailwind CSS + Lucide Icons  |
| Banco       | MySQL 8.0                    |
| Storage     | MinIO (S3-compatible)        |
| Infra       | Docker + Docker Compose      |
| Testes      | PHPUnit 10                   |

---

## Funcionalidades

- **Autenticação** — Login, registro e sessões seguras com `DbSession`
- **Projetos** — CRUD completo com gerenciamento de membros e anexos
- **Tarefas** — CRUD com prioridades (alta/média/baixa), status, filtros e atribuição
- **Dashboard** — Contadores, tarefas atrasadas, próximas do vencimento e projetos recentes
- **Upload** — Armazenamento de arquivos no MinIO com URLs pré-assinadas
- **Segurança** — CSRF, XSS prevention, validação de MIME e VerbFilter

---

## Instalação

### Pré-requisitos

- Docker e Docker Compose instalados.

### Comandos Disponíveis

A Utilização dos comandos make é opcional (makefile), mas altamente recomendada para melhor gerenciamento dos conteiners e testes do projeto.

```bash
# 🐳 Docker
make up          # Sobe os containers
make down        # Derruba os containers
make build       # Builda os containers
make restart     # Reinicia os containers
make logs        # Mostra logs
make logs-php    # Logs do PHP
make bash        # Entra no container PHP

# 📦 Dependências
make composer    # Instala dependências PHP
make migrate     # Roda migrations
make seed        # Popula banco com dados demo

# ⚡ Setup
make setup       # Setup completo (composer + migrate)
make setup-full  # Setup completo + seed

# 🧪 Testes
make test        # Roda todos os testes
make test-unit   # Apenas testes unitários

# 🧹 Limpeza
make clean       # Limpa containers e volumes
make clean-all   # Limpa tudo

# 📊 Status
make status      # Status dos containers
make ps          # Lista containers
```


### Subir o Ambiente

**Opção 1: Makefile (Recomendado)**

```bash
# Clonar o repositório
git clone https://github.com/thomazjb/dever.io.git
cd dever.io

# Subir os containers
make up

# Aguardar o entrypoint executar:
# - composer install
# - migrations
# - php-fpm start
```

**Opção 2: Docker Compose Direto**

```bash
# Clonar o repositório
git clone https://github.com/thomazjb/dever.io.git
cd dever.io

# Subir os containers
docker compose up -d

# Aguardar o entrypoint executar:
# - composer install
# - migrations
# - php-fpm start
```

A aplicação estará disponível em **http://localhost:8080**

### Configurar dados de demonstração (seed)

**Opção 1: Makefile (Recomendado)**

```bash
make seed
```

**Opção 2: Docker Compose Direto**

```bash
docker compose exec php php /var/www/html/yii seed
```

Isso cria 4 usuários, 3 projetos e 20 tarefas de exemplo.

**Login padrão:**
```
Email: admin@dever.io
Senha: admin123
```

---
## 🛠️ Setup Rápido - Makefile (Recomendado)

Para facilitar o desenvolvimento, incluímos um `Makefile` com comandos comuns:

```bash
# Setup completo com dados de demonstração (recomendado para primeiros usos)
make setup-full

# Ou setup básico
make setup
make migrate
make seed
```

### Exemplo de Primeiro Uso

```bash
git clone https://github.com/thomazjb/dever.io.git
cd dever.io
make setup-full
```

A aplicação estará pronta em **http://localhost:8080** com dados de demonstração!

---
## Estrutura do Projeto

Muitas partes do código foram comentadas utilizando o padrão DOC Block para PHP, permitindo maior clareza nas escolhas técnicas e na documentação do código.

O projeto está conteinerizado para permitir mais eficiência no deploy e, também, isolamento do ambiente de quem estará testando o código.
 Na pasta "docker" estão todos os arquivos de configuração dos conteiners.
 Na pasta "src" estão os arquivos do framework Yii2 estruturados em MVC e outras pastas de auxílio do back-end. Na pasta "tests" é possível encontrar os testes unitários que foram descritos como necessários na conversa técnica.

```
dever.io/
├── docker/
│   ├── nginx/default.conf      # Config Nginx
│   ├── php/Dockerfile           # PHP 8.3-FPM
│   ├── php/entrypoint.sh        # Setup automático
│   └── mysql/init.sql           # Charset UTF-8
├── src/
│   ├── commands/                # Console (SeedController)
│   ├── components/              # MinioComponent
│   ├── config/                  # web, console, db, params
│   ├── controllers/             # Auth, Dashboard, Project, Task, Site
│   ├── migrations/              # Tabelas do banco
│   ├── models/                  # User, Project, Task, Attachment...
│   ├── views/                   # Templates (auth, dashboard, project, task)
│   └── web/                     # Entry point (index.php)
├── tests/
│   ├── unit/                    # UserTest, ProjectTest, TaskTest, AuthTest
│   ├── bootstrap.php
│   ├── config.php               # Config com SQLite em memória
│   └── TestCase.php             # Base com helpers
├── docker-compose.yml
└── phpunit.xml
```

---

## Testes

Para melhor isolamento dos testes unitários, no armazenamento de informações em memória, foi utilizado SQLite.

### Opção 1: Makefile (Recomendado)

```bash
# Rodar todos os testes
make test

# Apenas unitários
make test-unit
```

### Opção 2: Docker Compose Direto

```bash
# Rodar todos os testes
docker compose exec php vendor/bin/phpunit --configuration /var/www/html/phpunit.xml

# Apenas unitários
docker compose exec php vendor/bin/phpunit --testsuite Unit --configuration /var/www/html/phpunit.xml
```

### Com Relatório de Cobertura

**Makefile:**
```bash
make test -- --coverage-text
```

**Docker Compose Direto:**
```bash
docker compose exec php vendor/bin/phpunit --coverage-text --configuration /var/www/html/phpunit.xml
```

---

## Comandos Úteis
### Makefile (Recomendado)

```bash
# Acessar container PHP
make bash

# Rodar migrations
make migrate

# Popular banco (seed)
make seed

# Limpar banco
docker compose exec php php /var/www/html/yii seed/clear

# Logs da aplicação
make logs
```

### Docker Compose (Alternativo)
```bash
# Acessar container PHP
docker compose exec php bash

# Rodar migrations
docker compose exec php php /var/www/html/yii migrate

# Popular banco (seed)
docker compose exec php php /var/www/html/yii seed

# Limpar banco
docker compose exec php php /var/www/html/yii seed/clear

# Logs da aplicação
docker compose logs -f php
```

---

## Banco de Dados

No descritivo do teste técnico me foram pedidos Diagrama de entidades e relacionamentos (DER) do banco de dados. Para melhor interpretação do diagrama estou utilizando o mermaid dentro deste arquivo MD e que será considerado na visualização pelo GitHub. 

```mermaid
erDiagram
    USER ||--o{ PROJECT : "owns"
    USER ||--o{ PROJECT_USER : "member of"
    PROJECT ||--o{ PROJECT_USER : "has members"
    PROJECT ||--o{ TASK : "contains"
    USER ||--o{ TASK : "assigned to"
    PROJECT ||--o{ ATTACHMENT : "has files"
    TASK ||--o{ ATTACHMENT : "has files"

    USER {
        int id PK
        string name
        string email UK
        string password_hash
        string auth_key
        int status
    }

    PROJECT {
        int id PK
        string title
        text description
        date start_date
        date end_date
        int owner_id FK
        string status
    }

    TASK {
        int id PK
        int project_id FK
        int assigned_to FK
        string title
        text description
        date due_date
        string priority
        string status
        int completed_at
        int created_by FK
    }

    ATTACHMENT {
        int id PK
        string entity_type
        int entity_id
        string filename
        string original_name
        string storage_path
        int uploaded_by FK
    }
```

---

## Variáveis de Ambiente

Definidas no `docker-compose.yml`:

| Variável          | Descrição                | Padrão         |
|-------------------|--------------------------|----------------|
| `DB_HOST`         | Host do MySQL            | `mysql`        |
| `DB_NAME`         | Nome do banco            | `dever_io`     |
| `DB_USER`         | Usuário do banco         | `dever`        |
| `DB_PASS`         | Senha do banco           | `dever_secret` |
| `MINIO_ENDPOINT`  | URL do MinIO             | `http://minio:9000` |
| `MINIO_KEY`       | Access key do MinIO      | `minioadmin`   |
| `MINIO_SECRET`    | Secret key do MinIO      | `minioadmin`   |
| `MINIO_BUCKET`    | Bucket para uploads      | `dever-files`  |

---

## Licença
Como se trata de um teste com propósito não comercial, todas as partes do código são licenciadas pela MIT License — veja [LICENSE](LICENSE) para detalhes.

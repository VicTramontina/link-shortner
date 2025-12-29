# DDSV Link Shortener

Encurtador de links full-stack com Vue.js, Laravel e MySQL.

## Requisitos

- Docker e Docker Compose
- Node.js 18+ (para desenvolvimento do frontend)

## Estrutura do Projeto

```
link-shortner/
├── backend/                 # Laravel API
│   ├── app/
│   │   ├── Console/Commands/  # Comandos Artisan (reset mensal)
│   │   ├── Http/
│   │   │   ├── Controllers/   # AuthController, LinkController, etc.
│   │   │   ├── Requests/      # Form Requests para validacao
│   │   │   └── Resources/     # API Resources (JSON formatting)
│   │   ├── Models/            # User, Link, AccessLog
│   │   └── Services/          # SlugGeneratorService
│   ├── database/
│   │   ├── factories/         # Model Factories para testes
│   │   └── migrations/        # Migrações do banco
│   └── tests/                 # Testes PHPUnit
├── frontend/                # Vue.js SPA
│   ├── src/
│   │   ├── components/        # Componentes reutilizaveis
│   │   ├── views/             # Paginas da aplicacao
│   │   ├── services/          # API service (Axios)
│   │   └── router/            # Vue Router
│   └── tests/                 # Testes Vitest
├── docker/                  # Configuracoes Docker
│   ├── cron/                  # Crontab para Laravel Scheduler
│   ├── nginx/                 # Nginx config
│   └── php/                   # Scripts PHP (start.sh)
└── docker-compose.yml       # Orquestracao dos containers
```

## Instalacao e Execucao

### 1. Clonar o repositorio

```bash
git clone https://github.com/VicTramontina/link-shortner.git
cd link-shortner
```

### 2. Configurar variaveis de ambiente

```bash
cp backend/.env.example backend/.env
```

### 3. Subir os containers

```bash
docker compose up -d
```

### 4. Instalar dependencias do Laravel

```bash
docker compose exec app composer install
```

### 5. Gerar chave da aplicacao

```bash
docker compose exec app php artisan key:generate
```

### 6. Executar migracoes

```bash
docker compose exec app php artisan migrate
```

### 7. Instalar dependencias do frontend

```bash
cd frontend
npm install
```

### 8. Iniciar o frontend (desenvolvimento)

```bash
npm run dev
```

## URLs

- **Frontend**: http://localhost:5173
- **API**: http://localhost:8000/api
- **Swagger Docs**: http://localhost:8000/api/documentation
- **phpMyAdmin**: http://localhost:8080

## Testes

### Backend (PHPUnit)

```bash
docker compose exec app php artisan test
```

### Frontend (Vitest)

```bash
cd frontend
npm test
```

## Funcionalidades

- **Autenticacao**: Registro, login e logout com Laravel Sanctum
- **CRUD de Links**: Criar, editar e deletar links encurtados
- **Slugs**: Personalizados (6-8 caracteres) ou auto-gerados
- **Soft Delete**: Links vao para lixeira antes de exclusão permanente
- **Estatísticas**: Contagem de acessos, views e CTR
- **Logs de Acesso**: IP, User-Agent e timestamp de cada acesso
- **Reset Mensal**: Comando Cron para resetar contadores no dia 1

## Laravel Scheduler

O scheduler do Laravel inicia automaticamente com `docker compose up`. O cron executa `php artisan schedule:run` a cada minuto dentro do container `app`.

### Configurar Agendamentos

Edite `backend/routes/console.php` para adicionar ou modificar tarefas agendadas:

```php
use Illuminate\Support\Facades\Schedule;

// Reset mensal dos contadores (dia 1, meia-noite)
Schedule::command('links:reset-access-count')->monthlyOn(1, '00:00');
```

### Logs

Os logs ficam em `backend/storage/logs/laravel-YYYY-MM-DD.log` com rotacao automatica (14 dias).

```bash
# Ver logs em tempo real
tail -f backend/storage/logs/laravel-*.log
```

## Swagger (API Docs)

A documentacao da API usa L5-Swagger com annotations `@OA\` nos Controllers.

**Acessar:** http://localhost:8000/api/documentation

### Gerar documentacao

```bash
docker compose exec app php artisan l5-swagger:generate
```

## Tecnologias

### Backend
- PHP 8.2
- Laravel 12
- Laravel Sanctum (autenticacao)
- L5-Swagger (documentacao)
- PHPUnit (testes)

### Frontend
- Vue.js 3 (Composition API)
- Vite
- Vue Router
- Axios
- TailwindCSS
- Vitest (testes)

### Infraestrutura
- Docker e Docker Compose
- Nginx
- MySQL 8.0
- phpMyAdmin

## Design Patterns

- **Service Pattern**: `SlugGeneratorService` para logica de geracao de slugs
- **Form Request**: Validacao centralizada em classes dedicadas- **API Resource**: Formatacao de respostas JSON
- **Factory Pattern**: Geracao de dados para testes

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
│   └── nginx/                 # Nginx config
└── docker-compose.yml       # Orquestracao dos containers
```

## Instalacao e Execucao

### 1. Clonar o repositorio

```bash
git clone <url-do-repositorio>
cd link-shortner
```

### 2. Configurar variaveis de ambiente

```bash
cp backend/.env.example backend/.env
```

### 3. Subir os containers

```bash
docker-compose up -d
```

### 4. Instalar dependencias do Laravel

```bash
docker-compose exec app composer install
```

### 5. Gerar chave da aplicacao

```bash
docker-compose exec app php artisan key:generate
```

### 6. Executar migracoes

```bash
docker-compose exec app php artisan migrate
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

## Endpoints da API

### Autenticacao

| Metodo | Rota | Descricao |
|--------|------|-----------|
| POST | `/api/register` | Criar conta |
| POST | `/api/login` | Login (retorna token) |
| POST | `/api/logout` | Logout (invalida token) |
| GET | `/api/user` | Dados do usuario logado |

### Links (autenticados)

| Metodo | Rota | Descricao |
|--------|------|-----------|
| GET | `/api/links` | Listar links (paginacao, busca, ordenacao) |
| POST | `/api/links` | Criar novo link |
| GET | `/api/links/{id}` | Obter link especifico |
| PUT | `/api/links/{id}` | Atualizar link |
| DELETE | `/api/links/{id}` | Soft delete (move para lixeira) |
| GET | `/api/links/trash` | Listar links na lixeira |
| POST | `/api/links/{id}/restore` | Restaurar da lixeira |
| DELETE | `/api/links/{id}/force` | Deletar permanentemente |

### Estatisticas (autenticados)

| Metodo | Rota | Descricao |
|--------|------|-----------|
| GET | `/api/stats` | Estatisticas resumidas |
| GET | `/api/stats/detailed` | Estatisticas detalhadas |

### Redirecionamento (publico)

| Metodo | Rota | Descricao |
|--------|------|-----------|
| GET | `/{slug}` | Redirecionar para URL original |

## Testes

### Backend (PHPUnit)

```bash
docker-compose exec app php artisan test
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
- **Soft Delete**: Links vao para lixeira antes de exclusao permanente
- **Estatisticas**: Contagem de acessos, views e CTR
- **Logs de Acesso**: IP, User-Agent e timestamp de cada acesso
- **Reset Mensal**: Comando Cron para resetar contadores no dia 1

## Cron Job (Reset Mensal)

Para configurar o reset automatico dos contadores:

```bash
# Adicionar ao crontab do servidor
0 0 1 * * docker-compose exec app php artisan links:reset-access-count
```

Ou usar o scheduler do Laravel:

```bash
docker-compose exec app php artisan schedule:work
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
- **Form Request**: Validacao centralizada em classes dedicadas
- **API Resource**: Formatacao de respostas JSON
- **Factory Pattern**: Geracao de dados para testes

## Licenca

MIT

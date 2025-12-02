# KinLink Backend Setup

Laravel 12 API with Scout + Elasticsearch search. Follow these steps to install and run locally.

## Prerequisites
- PHP 8.2+, Composer
- Node 20+ (Vite build)
- PostgreSQL (or update `.env` for your DB)
- Elasticsearch 8.x

## Install & run
1) Install dependencies
```
composer install
npm install
```

2) Configure environment
```
cp .env.example .env
php artisan key:generate
```
Edit `.env` for DB/mail/storage and search:
```
SCOUT_DRIVER=elastic
ELASTIC_CONNECTION=default
ELASTIC_HOST=http://localhost:9200   # or host.docker.internal:9200 from ddev
ELASTIC_SCOUT_DRIVER_REFRESH_DOCUMENTS=false
```

3) Database setup
```
php artisan migrate
php artisan storage:link
```

4) Build assets
```
npm run build
```

5) Run services
```
php artisan serve
php artisan queue:work   # if using queues
```

## Elasticsearch
- Quick start (host):
```
docker run -d --name kinlink-es -p 9200:9200 -e "discovery.type=single-node" -e "xpack.security.enabled=false" docker.elastic.co/elasticsearch/elasticsearch:8.13.4
```
- From ddev/container, use `ELASTIC_HOST=http://host.docker.internal:9200`; if you add ES to docker-compose, use `http://elasticsearch:9200`.
- After ES is reachable, clear config cache and import indexes:
```
php artisan config:clear
php artisan scout:import "App\Models\Family"
php artisan scout:import "App\Models\Person"
php artisan scout:import "App\Models\Post"
```

## Environment template
```env
APP_NAME=Laravel
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file
# APP_MAINTENANCE_STORE=database

PHP_CLI_SERVER_WORKERS=4

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=pgsql
DB_HOST=localhost
DB_PORT=5432
DB_DATABASE=your_database_name
DB_USERNAME=homestead
DB_PASSWORD=secret

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database
# CACHE_PREFIX=

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

SCOUT_DRIVER=elastic
ELASTIC_CONNECTION=default
ELASTIC_HOST=localhost:9200
ELASTIC_SCOUT_DRIVER_REFRESH_DOCUMENTS=false

MAIL_MAILER=log
MAIL_SCHEME=null
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

# Cloudflare R2 Storage
R2_ACCESS_KEY_ID=
R2_SECRET_ACCESS_KEY=
R2_ACCOUNT_ID=
R2_BUCKET=
R2_PUBLIC_DOMAIN=
# R2_ENDPOINT= (optional, defaults to https://{R2_ACCOUNT_ID}.r2.cloudflarestorage.com)
# R2_DEFAULT_REGION=auto (default)

FRONTEND_URL=http://localhost:3000

GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=

FACEBOOK_CLIENT_ID=
FACEBOOK_CLIENT_SECRET=
FACEBOOK_REDIRECT_URI=

TINIFY_API_KEY=

VITE_APP_NAME="${APP_NAME}"
```

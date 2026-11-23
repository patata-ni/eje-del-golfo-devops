# Guía para reproducir y defender el proyecto

## Iniciar desde el código

Requisitos: PHP 8.2 o posterior con PDO, mbstring y SQLite/MySQL; Composer 2; Node 24. Para un entorno local vacío:

```sh
composer install
npm ci
cp .env.example .env
php artisan key:generate
```

En .env configura APP_ENV=local, APP_DEBUG=true, APP_URL=http://127.0.0.1:8085, DB_CONNECTION=sqlite y DB_DATABASE con la ruta de un archivo SQLite vacío. No uses credenciales de una base existente para una demostración de instalación. Después:

```sh
php artisan migrate
php artisan db:seed
npm run build
php artisan serve --host=127.0.0.1 --port=8085
```

El registro normal crea clientes. Las cuentas administrativas se crean mediante variables DEMO_* si DEMO_ACCOUNTS=true. Consulta el archivo privado de accesos entregado a las integrantes; no subas ese archivo a GitHub.

## Ejecutar comprobaciones

```sh
php artisan test --compact
npm run build
npm run test:e2e
npx playwright show-report
```

Las pruebas de navegador necesitan las cuentas configuradas en E2E_ADMIN_EMAIL/PASSWORD, E2E_MECHANIC_EMAIL/PASSWORD y E2E_CLIENT_EMAIL/PASSWORD. Para instalar los navegadores usa npx playwright install chromium firefox webkit; en Linux agrega --with-deps. Las pruebas PHP preparan sus propios datos y no deben apuntar a una base de operación real.

## Hacer un cambio

Textos y formularios: resources/views. Colores y distribución: public/css/ejegolfo.css o resources/css, según la pantalla. Acciones y validación: app/Http/Controllers. Tablas: nueva migración en database/migrations. No cambies una migración ya aplicada para intentar alterar una base remota.

```sh
git status
git switch -c feature/ajuste-revision
```

Edita, prueba y agrega sólo los archivos de tu cambio. Después crea un commit, sube la rama y abre un PR. La integración a main inicia la entrega automatizada. No subas .env, SQL con registros, sesiones, contraseñas, vendor ni node_modules.

## Preguntas de la exposición

CI detecta errores al integrar. El artefacto es la imagen Docker, identificada por digest. Pruebas y preproducción usan el mismo digest y bases distintas. /version demuestra la revisión y contenido publicado y comprueba MySQL. Los reportes documentan los casos y plataformas realmente ejecutados. Una versión validada en preproducción todavía requiere una decisión para liberarse a producción.

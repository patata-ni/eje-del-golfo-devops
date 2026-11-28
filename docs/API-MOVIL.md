# API de pasajeros · v1

Extensión del servidor Laravel para la app React Native. No cambia las vistas ni la navegación web. Los clientes móviles usan `/api/mobile/v1` sobre HTTPS.

## Acceso

- `GET /catalog`: rutas, paradas, salidas con fecha, capacidad y asientos ocupados. No publica nombres de pasajeros, correos, boletos ni datos personales de conductores.
- `POST /register`: `name`, `email`, `password`. Crea exclusivamente el rol 3 de pasajero; ignora roles enviados por el cliente.
- `POST /login`: `email`, `password`. Devuelve `token`, `user`, `tickets`.
- `GET /me`: cuenta y reservas propias. Requiere `Authorization: Bearer <token>`.
- `POST /logout`: revoca el token del dispositivo actual.
- `POST /reservations`: `service` (id_salida), `date` (AAAA-MM-DD), `seat`, `requestId` (UUID).

Los tokens de Sanctum expiran a los 30 días; solo se guarda su hash en el servidor. El móvil guarda el token en SecureStore. El catálogo y las reservas confirmadas se guardan en AsyncStorage para consulta; no se guardan contraseñas ni tokens allí. La vista de navegador para QA mantiene el token solo en memoria.

## Confirmación de asientos

El precio y la identidad se calculan en el servidor. Se comprueban fecha de la salida, hora futura en America/Mexico_City, capacidad, ruta/horario activos, unidad, conductor, incidencias y mantenimiento. La aplicación no procesa pagos; la reserva permanece pendiente de pago y validación para abordar.

Una transacción bloquea al pasajero y la salida. La lectura del asiento es también una lectura con bloqueo para obtener el último estado confirmado de MySQL, incluso bajo REPEATABLE READ. Un índice único `(departure_id, seat)` protege además la integridad. El índice `(user_id, request_id)` y la respuesta guardada permiten reintentar sin duplicar una reserva. Reutilizar la misma solicitud con otra selección devuelve 409.

Los errores 401 requieren iniciar sesión otra vez; 409 pide cambiar una selección que ya no está disponible; 422 indica datos inválidos y 429 aplica el límite de solicitudes. Nunca debe mostrarse una reserva confirmada si la petición de red falló. Tras un fallo de transporte, el móvil conserva el UUID para reintentar la misma selección.

## Despliegue y pruebas

Instalar dependencias con `composer install`, ejecutar `php artisan migrate --force` y desplegar el servidor completo. No usar `migrate:fresh` en bases compartidas. Las migraciones agregan `personal_access_tokens` y `mobile_reservations`; no borran registros existentes.

La integración está desplegada en **pruebas**: `https://eje-web-pruebas.up.railway.app/api/mobile/v1`. Preproducción y producción no recibieron este despliegue.

`php artisan mobile:demo-schedule --days=14` crea salidas ficticias futuras sin reemplazar reservas ni salidas existentes. Solo funciona con `DEMO_ACCOUNTS=true` en local/testing o en el entorno Railway `pruebas`. No es un calendario real de operación. Las salidas tienen unidad/conductor de prueba y observaciones explícitas. No se reponen automáticamente al pasar los días.

Validación: 36 pruebas Laravel, 140 aserciones, incluidas autorización, privacidad, fechas, capacidad, disponibilidad y reintentos. Se comprobó además contra MySQL remoto que dos solicitudes simultáneas al mismo asiento producen un 201 y un 409, que repetir el UUID devuelve la misma reserva y que cada cuenta ve solo sus reservas.

Antes de uso operativo deben integrarse el calendario real, las condiciones de pago y abordaje, políticas de cancelación, aviso de privacidad y publicación firmada en las tiendas. La copia de Expo Go es únicamente para revisión y comparte el servidor de pruebas.

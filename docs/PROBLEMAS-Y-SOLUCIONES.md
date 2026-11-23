# Problemas encontrados y soluciones

| Observación real | Corrección o tratamiento |
|---|---|
| El SQL tenía 25 tablas y el código sólo contenía las migraciones de usuarios, caché, trabajos y roles | Se generaron migraciones de 15 tablas operativas, 21 relaciones y google_id. Se comprobó su instalación local. |
| El registro asignaba el rol Cliente=3, pero el seeder sólo creaba dos roles | Se añadió el rol Cliente con identificador estable y una prueba de registro. |
| Administración y taller exigían autenticación pero no comprobaban el rol | Se añadió middleware de rol con pruebas de denegación para clientes y mecánicos. |
| Existía una contraseña administrativa fija dentro del seeder | Las cuentas de revisión se crean sólo con variables privadas y contraseñas nuevas. |
| AppServiceProvider forzaba http://127.0.0.1:8000 y rompía el login al utilizar otro puerto | Se retiró la dirección fija; las URLs se generan desde la petición y se reconocen los proxies HTTPS de Railway. |
| Las pruebas de la plantilla esperaban /dashboard, mientras la aplicación redirige según el rol | Se ajustaron a la conducta real y se añadieron casos para los tres roles. |
| La prueba de inicio no preparaba tablas | Se aplicó RefreshDatabase y se preparan los roles en los tests. |
| La extracción inicial omitió las vistas propias resources/views/vendor por confundirlas con dependencias | Se recuperaron las nueve vistas personalizadas del ZIP original y se añadió comprobación del listado administrativo. |
| npm audit informó dos dependencias vulnerables | Se actualizaron las versiones permitidas; la auditoría posterior informó cero vulnerabilidades. |
| El comando local de Docker no tenía un motor iniciado | La construcción y comprobación del contenedor se realizan en los ejecutores Linux de GitHub. |
| La sesión de Railway CLI permite desplegar, pero rechazó crear tokens de proyecto | Se requiere configurar tokens limitados a cada ambiente desde la cuenta Railway para la automatización. |

Las incidencias posteriores del pipeline se incorporan al informe final con enlaces y resultados reales.

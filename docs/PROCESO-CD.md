# Proceso DevOps de Transportes EJE del Golfo

Integrantes: Tania Gonzalez y Alondra Aimee Garcia Gomez.

## Alcance real del sistema

Aplicación Laravel 12 con MySQL y frontend adaptable. Incluye inicio, autenticación, registro de clientes, consulta y filtrado de salidas, detalle de viaje, boleto imprimible, administración de rutas, unidades, personal, choferes, horarios, salidas, usuarios, incidencias e inventario; además de taller y mantenimiento. El boleto es una vista imprimible: no se implementa una pasarela de pago ni una reserva persistida de asientos. Google, mapas y clima dependen de credenciales y servicios externos; el acceso local por correo funciona sin Google.

## Etapas y conceptos

**CI:** un push o PR inicia la instalación reproducible, construcción de assets, pruebas Laravel y pruebas de navegador en Windows y Linux. Los PR no despliegan.

**Construcción del artefacto:** sólo después de validar, se genera una imagen Docker con PHP, Apache, dependencias Composer y assets Vite. Esa imagen se ejecuta contra MySQL real y supera comprobaciones de versión y pruebas de navegador antes de publicarse en GHCR. El código se identifica por SHA de Git y por un hash del contenido. La imagen se identifica por un digest inmutable.

**Entrega continua en pruebas:** Actions configura Railway para ejecutar ese digest exacto en el ambiente pruebas. Espera que /version responda con la revisión y hash esperados, comprueba conexión con MySQL y ejecuta la suite funcional sobre la URL pública. Conserva los reportes automáticamente.

**Promoción a preproducción:** depende del éxito anterior. Railway recibe el mismo digest que pasó en pruebas, sin reconstruir. El ambiente tiene una base MySQL y dominio propios. Se repiten las verificaciones y pruebas remotas y se registra la versión candidata.

**Liberación:** decisión de hacer disponible la versión para usuarios de producción. Este ejercicio llega a preproducción y no libera producción. **Despliegue continuo a producción** automatizaría ese último paso sin aprobación por versión; no se afirma haberlo implementado.

## Eventos y dependencias

deploy.yml se activa por push a main, develop, feature/** y codex/**, PR hacia main/develop o ejecución manual. Sólo main publica la imagen y despliega. `needs` bloquea los pasos siguientes si falla un requisito. Las ejecuciones de main se serializan. Los reportes disponibles se conservan incluso ante fallos.

## Bases y credenciales

Cada ambiente Railway usa su propio servicio MySQL y volumen. El SQL original se importa de forma privada en copias nuevas, sin sesiones, cachés ni trabajos antiguos. Las contraseñas previas de users se sustituyen únicamente en esas copias; se crean cuentas de revisión mediante secretos. El SQL con datos y .env no se publican en GitHub ni en el artefacto. Las migraciones permiten instalar desde cero sin depender de una importación manual.

Los secretos de Actions están separados por ambiente. Cada token de Railway se limita al proyecto y ambiente correspondiente. El contenedor obtiene las credenciales MySQL de referencias internas de Railway y no las incorpora a la imagen.

## Compatibilidad

Pruebas Laravel y Chromium en Windows y Linux; en Linux también Firefox, WebKit y una pantalla móvil Pixel 7 emulada. No se confunde emulación con un teléfono físico ni WebKit con Safari en un iPhone real. Los reportes registran los resultados efectivamente obtenidos.

## Recuperación

Un fallo impide la promoción. Para revertir, elegir un digest previamente validado y ejecutar nuevamente las comprobaciones antes de autorizar su uso. Una reversión de imagen no revierte automáticamente los datos; las migraciones y copias de respaldo deben revisarse por separado. No se ejecutan migraciones destructivas ni restauraciones automáticas sobre una base existente.

## Referencias técnicas

- Laravel: https://laravel.com/docs/12.x/deployment
- Railway: https://docs.railway.com/cli/deploying
- Playwright: https://playwright.dev/docs/test-projects

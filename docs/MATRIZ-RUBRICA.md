# Matriz de cumplimiento y evidencias

Integrantes: **Tania Gonzalez** y **Alondra Aimee Garcia Gomez**. Grupo y matrículas: completar por las integrantes.

La rúbrica suministrada pondera siete criterios que suman 70%. No se inventa el porcentaje restante ni se garantiza una calificación.

| Criterio | Peso | Implementación | Evidencia |
|---|---:|---|---|
| Identificación de CD | 8% | docs/PROCESO-CD.md diferencia CI, entrega, liberación y despliegue continuo | Explicación y secuencia ejecutada |
| Repositorio y flujo | 10% | Ramas main, develop y funcionalidad, estructura Laravel y protecciones | Repositorio, ramas, PR y configuración |
| Pipeline Actions | 10% | .github/workflows/deploy.yml | Ejecución automática, pruebas y construcción |
| Entrega en pruebas | 12% | Imagen por digest + Railway pruebas + MySQL propio | URL, manifest, reportes remotos y despliegue |
| Entrega en preproducción | 12% | Promoción del mismo digest después de validar pruebas | URL y versión coincidente, reporte y despliegue exitosos |
| Multiplataforma | 10% | Windows/Linux, Chromium/Firefox/WebKit y móvil emulado | XML, HTML y capturas reales por plataforma |
| Evidencias y documentación | 8% | Guías, informe, reportes, capturas, problemas y soluciones | Paquete final y enlaces a ejecuciones |

## Archivos que explican el proyecto

- app/Http/Controllers: comportamiento de cada módulo.
- app/Models: tablas y relaciones del dominio de transporte.
- resources/views y public/css: pantallas y diseño adaptable.
- routes/web.php y routes/auth.php: direcciones y permisos.
- database/migrations: estructura reproducible de la base.
- database/seeders: roles y datos de demostración controlados por variables.
- tests/Feature: validación Laravel, autenticación, roles y operaciones de transporte.
- tests/browser y playwright.config.mjs: pruebas reales de interfaz.
- Dockerfile: construcción del artefacto desplegable.
- scripts/build-manifest.php y /version: identificación de contenido y revisión.
- scripts/wait-deployment.mjs: espera y comprobación de la versión remota.
- railway.json: configuración de salud y reinicio.

## Evidencias que deben acompañar la entrega

Capturas del repositorio, ramas y PR; Actions con resultados; imagen/digest y artefactos; servicios Railway y ambos dominios; versión y hash idénticos; reportes funcionales y multiplataforma; incidencias observadas y soluciones. El informe final debe citar la ejecución concreta, su commit y su resultado, sin presentar preparativos como despliegues ya realizados.

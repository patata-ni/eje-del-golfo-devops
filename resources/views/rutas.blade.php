@extends('layouts.app')

@section('content')
    <section class="container content-section">
        <p class="eyebrow">PLANIFICA TU VIAJE</p>
        <h1>Rutas y Horarios</h1>
        <p class="section-intro">Selecciona una salida para consultar sus detalles y paradas.</p>

        <div class="route-location-filter">
            <div>
                <p class="eyebrow">SALIDAS CERCANAS</p>
                <h2 id="location-heading">
                    @if(request('origen') || request('destino'))
                        {{ request('origen', 'Cualquier lugar') }} a {{ request('destino', 'Cualquier lugar') }}
                    @else
                        Todas las salidas
                    @endif
                </h2>
                <p id="location-status" role="status">Filtra por ciudad de origen.</p>
            </div>
            <label for="departure-city">Ciudad de salida
                <select id="departure-city">
                    <option value="Todas">Todas las ciudades</option>
                    @foreach(collect($corridas)->pluck('origen')->unique() as $origen)
                        <option value="{{ $origen }}">{{ $origen }}</option>
                    @endforeach
                </select>
            </label>
            <button type="button" class="location-button" id="detect-location">Usar mi ubicación</button>
        </div>

        <div class="routes-workspace">
            <section class="departures-panel" aria-labelledby="departures-title">
                <div class="departures-heading">
                    <div>
                        <h2 id="departures-title">Salidas encontradas</h2>
                        <p id="visible-run-count">{{ count($corridas) }} corridas disponibles</p>
                    </div>
                    <span class="sort-label">Ordenar por precio</span>
                </div>

                <div class="run-list">
                    @if(count($corridas) == 0)
                        <div style="background: #fff; padding: 2rem; border-radius: 8px; text-align: center; border: 1px solid #eee;">
                            <h3 style="color: var(--verde-bosque); margin-bottom: 0.5rem;">No se encontraron salidas</h3>
                            <p style="color: #666;">Intenta buscar con otros términos o selecciona una fecha diferente.</p>
                        </div>
                    @endif
                    @foreach ($corridas as $corrida)
                        <article class="run-card{{ $loop->first ? ' is-selected' : '' }}" data-origin="{{ $corrida['origen'] }}">
                            <div class="run-card-topline">
                                <span class="badge-directo">{{ $corrida['tipo'] }}</span>
                                <span class="run-seats">{{ $corrida['asientos'] }} asientos disponibles</span>
                            </div>
                            <div class="run-schedule">
                                <div class="run-time">
                                    <strong>{{ $corrida['hora_salida'] }}</strong>
                                    <span>{{ $corrida['origen'] }}</span>
                                </div>
                                <div class="run-duration">
                                    <span>{{ $corrida['duracion'] }}</span>
                                    <span class="run-line"><i></i></span>
                                    <span>{{ $corrida['unidad'] }}</span>
                                </div>
                                <div class="run-time run-time--arrival">
                                    <strong>{{ $corrida['hora_llegada'] }}</strong>
                                    <span>{{ $corrida['destino'] }}</span>
                                </div>
                            </div>
                            <div class="run-card-footer">
                                <a
                                    href="#route-detail"
                                    class="run-details-link"
                                    data-run-id="{{ $corrida['id'] }}"
                                    data-origin="{{ $corrida['origen'] }}"
                                    data-departure="{{ $corrida['hora_salida'] }}"
                                    data-arrival="{{ $corrida['hora_llegada'] }}"
                                    data-duration="{{ $corrida['duracion'] }}"
                                    data-unit="{{ $corrida['unidad'] }}"
                                    data-seats="{{ $corrida['asientos'] }}"
                                    data-type="{{ $corrida['tipo'] }}"
                                    data-destination="{{ $corrida['destino'] }}"
                                    data-stop-count="{{ count($corrida['paradas']) }}"
                                    data-map="{{ json_encode($corrida['mapa']) }}"
                                    aria-pressed="{{ $loop->first ? 'true' : 'false' }}"
                                >Ver detalles y paradas</a>
                                <strong class="run-price">${{ $corrida['precio'] }}</strong>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>

            @if(count($corridas) > 0)
            <aside class="route-detail-panel" id="route-detail" aria-labelledby="route-detail-title">
                <div class="route-detail-heading">
                    <div>
                        <p class="eyebrow">DETALLE DE RUTA</p>
                        <h2 id="route-detail-title">{{ $corridas[0]['origen'] }} — {{ $corridas[0]['destino'] }}</h2>
                    </div>
                    <div class="route-weather" id="route-weather" aria-live="polite">
                        <span class="route-weather-location">POZA RICA</span>
                        <div class="route-weather-current">
                            <img id="route-weather-icon" alt="" hidden>
                            <strong id="route-weather-temperature">--°C</strong>
                        </div>
                        <span id="route-weather-description">Consultando clima...</span>
                    </div>
                </div>

                <div id="google-route-map" class="route-map-canvas" role="application" aria-label="Mapa con el recorrido por carretera entre Poza Rica y Martínez de la Torre"></div>
                <p id="route-map-message" class="map-status" role="status">Cargando mapa y ruta por carretera...</p>

                <div class="route-facts">
                    <div class="route-fact">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                        <span>Duración estimada</span>
                        <strong id="detail-duration">{{ $corridas[0]['duracion'] }}</strong>
                    </div>
                    <div class="route-fact">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg>
                        <span>Número de paradas</span>
                        <strong id="detail-stop-count">{{ count($corridas[0]['paradas']) }} paradas</strong>
                    </div>
                    <div class="route-fact">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 17h14l-1-8H6l-1 8Zm1 0v2m12-2v2M8 9l1-4h6l1 4M8 13h.01M16 13h.01"/><path d="M3 12h2m14 0h2"/></svg>
                        <span>Unidad</span>
                        <strong id="detail-unit">{{ $corridas[0]['unidad'] }} · {{ $corridas[0]['asientos'] }} lugares</strong>
                    </div>
                </div>

                <a href="#" class="btn-primary select-run-button" id="select-run-button">Seleccionar horario</a>
                <button type="button" onclick="abrirModalParadas()" style="display: block; width: 100%; text-align: center; margin-top: 0.5rem; background: transparent; border: 1px solid var(--verde-esmeralda); color: var(--verde-esmeralda); padding: 0.8rem; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    Ver paradas de la ruta
                </button>
            </aside>
            @else
            <aside class="route-detail-panel" style="display: flex; align-items: center; justify-content: center; min-height: 400px; color: #999;">
                <p>No hay salidas programadas por el momento.</p>
            </aside>
            @endif
        </div>
    </section>

        <!-- MODAL DE PARADAS -->
    <div id="modal-paradas" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: white; padding: 2rem; border-radius: 12px; width: 100%; max-width: 400px;">
            <h3 style="margin-bottom: 1rem; color: var(--verde-bosque);">Paradas de la Ruta</h3>
            <p style="color: #666; font-size: 0.85rem; margin-bottom: 1.5rem;">La unidad hace las siguientes paradas antes de llegar a su destino:</p>
            
            <ul id="lista-paradas" style="list-style: none; padding: 0; display: grid; gap: 0.8rem; max-height: 350px; overflow-y: auto; padding-right: 10px;">
                <!-- Aquí se inyectan las paradas con JS -->
            </ul>
            
            <div style="margin-top: 1.5rem; text-align: right;">
                <button type="button" onclick="document.getElementById('modal-paradas').style.display='none'" style="background: #eee; border: none; padding: 0.6rem 1.2rem; border-radius: 6px; cursor: pointer; font-weight: 600;">Cerrar</button>
            </div>
        </div>
    </div>

    <div id="modal-reserva" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: white; padding: 2rem; border-radius: 12px; width: 100%; max-width: 500px;">
            <h3 style="margin-bottom: 0.5rem; color: var(--verde-bosque); font-size: 1.5rem;">Confirmar Viaje</h3>
            <p style="color: #666; font-size: 0.85rem; margin-bottom: 1.5rem;">Revisa los detalles antes de continuar.</p>
            
            <div style="background: var(--verde-menta); padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
                <h2 style="color: var(--verde-bosque); margin-bottom: 0.5rem;" id="reserva-ruta">Cargando...</h2>
                <p style="color: #555; margin-bottom: 1rem;" id="reserva-fecha-hora">Cargando...</p>
                
                <div style="display: flex; justify-content: space-between; border-top: 1px solid rgba(0,132,90,0.2); padding-top: 1rem;">
                    <div>
                        <span style="font-size: 0.8rem; color: #666;">Unidad</span>
                        <p style="font-weight: 600; color: #333;" id="reserva-unidad">--</p>
                    </div>
                    <div style="text-align: right;">
                        <span style="font-size: 0.8rem; color: #666;">Total a pagar</span>
                        <h3 style="color: var(--verde-esmeralda); font-size: 1.8rem;" id="reserva-precio">$--</h3>
                    </div>
                </div>
            </div>
            
            <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                <button type="button" onclick="document.getElementById('modal-reserva').style.display='none'" style="background: #eee; border: none; padding: 0.6rem 1.2rem; border-radius: 6px; cursor: pointer; font-weight: 600;">Cancelar</button>
                <a href="#" id="btn-imprimir-boleto" target="_blank" class="btn-primary" style="text-decoration: none; display: inline-block; text-align: center;">Reservar y Descargar Boleto</a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @if (config('services.google_maps.key'))
        <script>
            let routeMap;
            let routePolyline;
            let routeMarkers = [];
            let latestRouteRequest = 0;

            window.initRouteMap = () => {
                const mapElement = document.getElementById('google-route-map');
                routeMap = new google.maps.Map(mapElement, {
                    center: { lat: 20.35, lng: -97.27 },
                    zoom: 9,
                    mapId: 'DEMO_MAP_ID',
                    mapTypeControl: false,
                    streetViewControl: false,
                    fullscreenControl: true,
                });

                const firstLink = document.querySelector('.run-details-link[aria-pressed="true"]');
                if (firstLink) {
                    window.updateRouteMap(firstLink);
                }
            };

            window.updateRouteMap = async (link) => {
                if (!routeMap) return;
                const requestId = ++latestRouteRequest;
                const mapStatus = document.getElementById('route-map-message');
                const routeData = JSON.parse(link.dataset.map);
                
                mapStatus.hidden = false;
                mapStatus.textContent = 'Trazando ruta...';

                routeMarkers.forEach((marker) => { marker.map = null; });
                routeMarkers = [];
                if (routePolyline) routePolyline.setMap(null);

                try {
                    const [{ Route }, { AdvancedMarkerElement, PinElement }] = await Promise.all([
                        google.maps.importLibrary('routes'),
                        google.maps.importLibrary('marker'),
                    ]);
                    
                    const { routes } = await Route.computeRoutes({
                        origin: routeData.origen,
                        destination: routeData.destino,
                        intermediates: routeData.waypoints.map((location) => ({ location })),
                        travelMode: 'DRIVING',
                        fields: ['path', 'viewport'],
                    });
                    
                    const route = routes?.[0];
                    if (requestId !== latestRouteRequest) return; 
                    if (!route?.path?.length) throw new Error('No route geometry');

                    const stops = [
                        { title: link.dataset.origin, position: routeData.origen },
                        ...routeData.waypoints.map((position, index) => ({
                            title: routeData.nombres_paradas[index],
                            position,
                        })),
                        { title: link.dataset.destination, position: routeData.destino },
                    ];

                    stops.forEach((stop, index) => {
                        const marker = new AdvancedMarkerElement({
                            map: routeMap,
                            position: stop.position,
                            title: stop.title,
                        });
                        marker.append(new PinElement({ glyphText: String(index + 1) }));
                        routeMarkers.push(marker);
                    });

                    routePolyline = new google.maps.Polyline({
                        map: routeMap,
                        path: route.path,
                        strokeColor: '#00845A',
                        strokeWeight: 5,
                    });
                    
                    routeMap.fitBounds(route.viewport, 40);
                    mapStatus.hidden = true;
                } catch (error) {
                    console.error(error);
                    mapStatus.textContent = 'No se pudo calcular la ruta.';
                }
            };
        </script>
        <script async defer src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&callback=initRouteMap&language=es&region=MX&loading=async"></script>
    @else
        <script>
            document.getElementById('route-map-message').textContent = 'Configura GOOGLE_MAPS_API_KEY para mostrar el mapa.';
        </script>
    @endif

        <script>
        (() => {
            const selector = document.getElementById('departure-city');
            const status = document.getElementById('location-status');
            const btnUbicacion = document.getElementById('detect-location');
            const cards = Array.from(document.querySelectorAll('.run-card'));

            const ciudadesGPS = {
                'Poza Rica': { lat: 20.5333, lng: -97.4500 },
                'Papantla': { lat: 20.4469, lng: -97.3214 },
                'Martínez de la Torre': { lat: 20.0667, lng: -97.0500 },
                'Gutiérrez Zamora': { lat: 20.4500, lng: -97.0833 },
                'Tecolutla': { lat: 20.4800, lng: -97.0000 },
            };

            const seleccionarCorrida = (link) => {
                document.querySelectorAll('.run-card').forEach((card) => card.classList.remove('is-selected'));
                document.querySelectorAll('.run-details-link').forEach((item) => item.setAttribute('aria-pressed', 'false'));
                link.closest('.run-card').classList.add('is-selected');
                link.setAttribute('aria-pressed', 'true');

                document.getElementById('route-detail-title').textContent = `${link.dataset.origin} — ${link.dataset.destination}`;
                document.getElementById('detail-duration').textContent = link.dataset.duration;
                document.getElementById('detail-unit').textContent = `${link.dataset.unit} · ${link.dataset.seats} lugares`;
                document.getElementById('detail-stop-count').textContent = `${link.dataset.stopCount} paradas`;
                
                document.getElementById('select-run-button').dataset.runId = link.dataset.runId;

                if (typeof window.updateRouteMap === 'function') {
                    window.updateRouteMap(link);
                }
            };

            document.querySelectorAll('.run-details-link').forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    seleccionarCorrida(link);
                });
            });

            const filtrarPorCiudad = (ciudad) => {
                let visibles = 0;
                cards.forEach((card) => {
                    if (ciudad === 'Todas' || card.dataset.origin === ciudad) {
                        card.hidden = false;
                        visibles++;
                    } else {
                        card.hidden = true;
                    }
                });

                document.getElementById('location-heading').textContent = ciudad === 'Todas' ? 'Todas las salidas' : `Salidas desde ${ciudad}`;
                document.getElementById('visible-run-count').textContent = `${visibles} salidas disponibles`;

                const primeraCorrida = cards.find((card) => !card.hidden)?.querySelector('.run-details-link');
                if (primeraCorrida) seleccionarCorrida(primeraCorrida);
            };

            selector.addEventListener('change', () => filtrarPorCiudad(selector.value));

            btnUbicacion.addEventListener('click', () => {
                if (!navigator.geolocation) {
                    status.textContent = 'Tu navegador no soporta geolocalización.';
                    return;
                }

                status.textContent = 'Detectando tu ubicación...';

                navigator.geolocation.getCurrentPosition((position) => {
                    const userLat = position.coords.latitude;
                    const userLng = position.coords.longitude;
                    let ciudadCercana = 'Poza Rica';
                    let distanciaMinima = Infinity;

                    Object.entries(ciudadesGPS).forEach(([nombre, coords]) => {
                        const dist = Math.sqrt(Math.pow(userLat - coords.lat, 2) + Math.pow(userLng - coords.lng, 2));
                        if (dist < distanciaMinima) {
                            distanciaMinima = dist;
                            ciudadCercana = nombre;
                        }
                    });

                    const opcionExiste = Array.from(selector.options).find(opt => opt.value === ciudadCercana);
                    
                    if (opcionExiste) {
                        selector.value = ciudadCercana;
                        filtrarPorCiudad(ciudadCercana);
                        status.textContent = `Estás cerca de ${ciudadCercana}. Mostrando salidas desde ahí.`;
                    } else {
                        status.textContent = `Estás cerca de ${ciudadCercana}, pero no hay salidas programadas de ahí.`;
                    }

                }, (error) => {
                    status.textContent = 'No pudimos acceder a tu ubicación. Verifica los permisos.';
                });
            });

            const primeraCorrida = cards[0]?.querySelector('.run-details-link');
            if (primeraCorrida) seleccionarCorrida(primeraCorrida);
        })();

        document.getElementById('select-run-button').addEventListener('click', function(e) {
            e.preventDefault();
                const runId = this.dataset.runId;
                
                if (!runId) return;

                fetch(`/reservar/${runId}`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('reserva-ruta').textContent = `${data.origen} a ${data.destino}`;
                        document.getElementById('reserva-fecha-hora').textContent = `${data.fecha} a las ${data.hora_salida}`;
                        document.getElementById('reserva-unidad').textContent = data.unidad;
                        document.getElementById('reserva-precio').textContent = `$${data.precio}`;
                        
                        // Actualizamos el enlace del botón de descarga del boleto
                        document.getElementById('btn-imprimir-boleto').href = `/boleto/${runId}`;
                        
                        document.getElementById('modal-reserva').style.display = 'flex';
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Hubo un error al cargar los datos del viaje. Revisa la consola (F12) para más detalles.');
            });
        });

        function abrirModalParadas() {
            const activeLink = document.querySelector('.run-details-link[aria-pressed="true"]');
            if (!activeLink) return;

            const mapData = JSON.parse(activeLink.dataset.map);
            const paradas = mapData.nombres_paradas || [];
            const listContainer = document.getElementById('lista-paradas');
            
            listContainer.innerHTML = '';

            if (paradas.length === 0) {
                listContainer.innerHTML = '<li style="color: #999; font-style: italic;">Esta ruta es directa, no tiene paradas intermedias.</li>';
            } else {
                paradas.forEach((parada, index) => {
                    const li = document.createElement('li');
                    li.style.display = 'flex';
                    li.style.alignItems = 'center';
                    li.style.gap = '10px';
                    li.style.fontSize = '0.9rem';
                    li.innerHTML = `
                        <span style="background: var(--verde-menta); color: var(--verde-bosque); width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-weight: bold; font-size: 0.75rem;">${index + 1}</span>
                        <span style="color: #333; font-weight: 600;">${parada}</span>
                    `;
                    listContainer.appendChild(li);
                });
            }

            document.getElementById('modal-paradas').style.display = 'flex';
        }
    </script>
@endpush
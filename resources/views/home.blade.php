@extends('layouts.app')

@section('content')
    <section class="hero">
        <h1>Tu próximo destino comienza aquí.</h1>
        <p>Viaja seguro y a tiempo con Transportes Eje del Golfo.</p>
        
        <form action="{{ route('rutas') }}" method="GET" class="search-box">
            <input type="text" name="origen" placeholder="Origen (Ej: Poza Rica)">
            <input type="text" name="destino" placeholder="Destino (Ej: Papantla)">
            <input type="date" name="fecha" class="input-date" required>
            <button type="submit" class="btn-primary" style="border: none; cursor: pointer;">Buscar Viaje</button>
        </form>
    </section>
    <section class="popular-routes-section">
        <div class="container">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">RUTAS REGIONALES</p>
                    <h2>Corridas más solicitadas</h2>
                </div>
                <a class="text-link" href="{{ route('rutas') }}">Ver todos los horarios <span aria-hidden="true">→</span></a>
            </div>
            
            <div class="popular-routes-grid">
                @foreach($rutasPopulares as $ruta)
                    <article class="popular-route-card">
                        <p class="popular-route-name">{{ $ruta->origen }} <span aria-hidden="true">→</span> {{ $ruta->destino }}</p>
                        <div class="popular-route-meta">
                            <span><small>Duración</small><strong>{{ $ruta->duracion_minutos }} min</strong></span>
                            <span><small>Precio desde</small><strong>${{ $ruta->precio }}</strong></span>
                            <a href="{{ route('rutas', ['origen' => $ruta->origen, 'destino' => $ruta->destino]) }}">Ver horarios</a>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="container weather-section">
        <div class="weather-panel" id="weather-panel">
            <div class="weather-panel-text">
                <p class="eyebrow" style="color: rgba(255,255,255,0.8);">PLANEA TU VIAJE</p>
                <h2 style="color: white; font-size: 2rem; margin-bottom: 0.5rem;">Clima en tiempo real</h2>
                <p style="color: rgba(255,255,255,0.9); font-size: 1.1rem;">Consulta el clima de tu destino para viajar preparado.</p>
            </div>
            
            <div class="weather-widget-custom" id="weather-widget">
                <div class="weather-info">
                    <span class="weather-location" id="weather-location">Buscando ubicación...</span>
                    <div class="weather-temp">
                        <strong id="weather-temperature">--°C</strong>
                        <div class="weather-extras">
                            <p id="weather-description" class="weather-description">Consultando...</p>
                            <p id="weather-min-max" class="weather-min-max">M: --°C | m: --°C</p>
                            <p id="weather-humidity" class="weather-humidity">Humedad: --%</p>
                        </div>
                    </div>
                </div>
                
                <div class="weather-icon-container" id="weather-icon">
                    ☁️
                </div>
            </div>
        </div>
    </section>

    <section class="container home-features">
        <p class="eyebrow">VIAJA CON TRANQUILIDAD</p>
        <h2>Todo lo que necesitas para tu viaje</h2>
        <div class="cards-grid home-feature-grid">
            <article class="card feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="m3 6 6-3 6 3 6-3v15l-6 3-6-3-6 3V6Z" />
                        <path d="M9 3v15m6-12v15" />
                        <circle cx="12" cy="9" r="1.5" />
                    </svg>
                </div>
                <h3>Consulta de rutas</h3>
                <p>Encuentra tu ruta ideal y revisa las paradas.</p>
            </article>
            <article class="card feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                    </svg>
                </div>
                <h3>Precios Accesibles</h3>
                <p>Tarifas que se adaptan a tu bolsillo, sin sorpresas y con la mejor comodidad.</p>
            </article>
            <article class="card feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="12" cy="12" r="9" />
                        <path d="M12 7v5l3 2" />
                    </svg>
                </div>
                <h3>Puntualidad</h3>
                <p>Salidas a tiempo, llega seguro.</p>
            </article>
            <article class="card feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M4 13v-2a8 8 0 0 1 16 0v2" />
                        <path d="M4 13H2v4h4v-4H4Zm16 0h2v4h-4v-4h2Zm-14 5a7 7 0 0 0 12 2" />
                        <path d="M18 20h-4" />
                    </svg>
                </div>
                <h3>Soporte</h3>
                <p>Atención a clientes y ayuda con tu viaje.</p>
            </article>
        </div>
    </section>

    <section class="container testimonials-section">
        <div class="section-heading" style="text-align: center; display: block; margin-bottom: 3rem;">
            <p class="eyebrow">LO QUE DICEN DE NOSOTROS</p>
            <h2>Experiencias de nuestros pasajeros</h2>
        </div>
        
        <div class="testimonial-carousel-container">
            <div class="testimonial-carousel">
                <!-- Testimonio 1 -->
                <div class="carousel-card">
                    <div class="stars">⭐⭐⭐⭐⭐</div>
                    <p>"Excelente servicio, los autobuses siempre están limpios y los choferes son muy amables."</p>
                    <div class="author"><strong>María Fernanda L.</strong><span>Viajera frecuente</span></div>
                </div>
                <!-- Testimonio 2 -->
                <div class="carousel-card">
                    <div class="stars">⭐⭐⭐⭐⭐</div>
                    <p>"Sus precios son super económicos y accesibles. Es la única línea que uso para ir a Martínez de la Torre por trabajo."</p>
                    <div class="author"><strong>Carlos Ruiz</strong><span>Pasajero de negocios</span></div>
                </div>
                <!-- Testimonio 3 -->
                <div class="carousel-card">
                    <div class="stars">⭐⭐⭐⭐⭐</div>
                    <p>"Me encanta que ahora tengan nuevas unidades con clima y de gas natural, se siente más limpio y silencioso."</p>
                    <div class="author"><strong>Ana Gómez</strong><span>Estudiante</span></div>
                </div>
                <!-- Testimonio 4 -->
                <div class="carousel-card">
                    <div class="stars">⭐⭐⭐⭐⭐</div>
                    <p>"La atención al cliente es inigualable. Me ayudaron a recuperar mi equipaje en tiempo récord."</p>
                    <div class="author"><strong>Luis Miguel T.</strong><span>Turista</span></div>
                </div>
                <!-- Testimonio 5 -->
                <div class="carousel-card">
                    <div class="stars">⭐⭐⭐⭐⭐</div>
                    <p>"Los asientos son súper cómodos y el aire acondicionado funciona perfecto. ¡Recomendados!"</p>
                    <div class="author"><strong>Diana Laura R.</strong><span>Pasajera frecuente</span></div>
                </div>
            </div>
            <!-- Botones de control -->
            <div class="carousel-controls">
                <button id="prevTestimonial">❮</button>
                <button id="nextTestimonial">❯</button>
            </div>
        </div>

        <!-- FORMULARIO PARA DEJAR TESTIMONIO -->
        <div class="testimonial-form-container">
            <h3>Déjanos tu opinión</h3>
            <p>Tu experiencia nos ayuda a mejorar. ¡Cuéntanos qué tal viajaste!</p>
            <form class="testimonial-form">
                <input type="text" placeholder="Tu nombre" required>
                <textarea placeholder="Escribe tu comentario aquí..." rows="3" required></textarea>
                <button type="button" class="btn-primary">Enviar Comentario</button>
            </form>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        (() => {
            const apiKey = '{{ config('services.openweather.key') }}';
            
            const locationText = document.getElementById('weather-location');
            const temperature = document.getElementById('weather-temperature');
            const description = document.getElementById('weather-description');
            const minMax = document.getElementById('weather-min-max');
            const humidity = document.getElementById('weather-humidity');
            const iconContainer = document.getElementById('weather-icon');
            const panel = document.getElementById('weather-panel');

            const fetchWeather = (lat, lon) => {
                const params = new URLSearchParams({
                    lat: lat,
                    lon: lon,
                    appid: apiKey,
                    units: 'metric',
                    lang: 'es',
                });

                fetch(`https://api.openweathermap.org/data/2.5/weather?${params}`)
                    .then(res => res.json())
                    .then(data => {
                        locationText.textContent = `${data.name}, ${data.sys.country}`;
                        temperature.textContent = `${Math.round(data.main.temp)}°C`;
                        description.textContent = data.weather[0].description;
                        minMax.textContent = `M: ${Math.round(data.main.temp_max)}°C | m: ${Math.round(data.main.temp_min)}°C`;
                        humidity.textContent = `Humedad: ${data.main.humidity}%`;

                        const mainWeather = data.weather[0].main.toLowerCase();
                        const iconCode = data.weather[0].icon;
                        const isNight = iconCode.includes('n');

                        let emoji = '☁️';
                        let bgGradient = 'linear-gradient(135deg, #757F9A 0%, #D7DDE8 100%)';

                        if (mainWeather.includes('clear')) {
                            emoji = isNight ? '🌙' : '☀️';
                            bgGradient = isNight 
                                ? 'linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%)'
                                : 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)';
                        } else if (mainWeather.includes('rain') || mainWeather.includes('drizzle')) {
                            emoji = '🌧️';
                            bgGradient = 'linear-gradient(135deg, #2b5876 0%, #4e4376 100%)';
                        } else if (mainWeather.includes('thunderstorm')) {
                            emoji = '⛈️';
                            bgGradient = 'linear-gradient(135deg, #141E30 0%, #243B55 100%)';
                        } else if (mainWeather.includes('snow')) {
                            emoji = '❄️';
                            bgGradient = 'linear-gradient(135deg, #e6dada 0%, #274046 100%)';
                        }

                        iconContainer.textContent = emoji;
                        panel.style.background = bgGradient;
                    })
                    .catch(err => {
                        locationText.textContent = 'Error al cargar';
                        description.textContent = 'Intenta de nuevo';
                        console.error(err);
                    });
            };

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        fetchWeather(position.coords.latitude, position.coords.longitude);
                    },
                    (error) => {
                        console.warn("Geolocalización denegada o fallida. Usando Papantla.");
                        fetchWeather(20.4469, -97.3214);
                    }
                );
            } else {
                fetchWeather(20.4469, -97.3214);
            }
        })();
        
        document.addEventListener('DOMContentLoaded', () => {
            const cards = document.querySelectorAll('.carousel-card');
            let currentIndex = 0;

            const updateCarousel = () => {
                cards.forEach((card, index) => {
                    card.classList.remove('active', 'prev', 'next', 'hidden');
                    
                    if (index === currentIndex) {
                        card.classList.add('active');
                    } else if (index === (currentIndex - 1 + cards.length) % cards.length) {
                        card.classList.add('prev');
                    } else if (index === (currentIndex + 1) % cards.length) {
                        card.classList.add('next');
                    } else {
                        card.classList.add('hidden');
                    }
                });
            };

            document.getElementById('nextTestimonial').addEventListener('click', () => {
                currentIndex = (currentIndex + 1) % cards.length;
                updateCarousel();
            });

            document.getElementById('prevTestimonial').addEventListener('click', () => {
                currentIndex = (currentIndex - 1 + cards.length) % cards.length;
                updateCarousel();
            });

            updateCarousel();
        });
    </script>
@endpush
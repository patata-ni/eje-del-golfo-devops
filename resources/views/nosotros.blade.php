@extends('layouts.app')

@section('content')
    <!-- HERO CON IMAGEN Y DEGRADADO -->
    <section class="page-hero about-hero">
        <div class="container">
            <p class="eyebrow" style="color: var(--verde-menta);">SOBRE NOSOTROS</p>
            <h1>Conectamos caminos, acercamos personas.</h1>
            <p>Somos Transportes Eje del Golfo, una empresa comprometida con ofrecer recorridos seguros y confiables entre Poza Rica y la región.</p>
        </div>
    </section>

    <!-- HISTORIA Y ESTADÍSTICAS EN LÍNEA -->
    <section class="container content-section split-layout">
        <div class="story-copy">
            <p class="eyebrow">NUESTRA HISTORIA</p>
            <h2>Transportes Eje del Golfo</h2>
            <p>Nació el año de 1996 como una alternativa de transporte económico para enlazar ciudades, comunidades, rancherías y 
                municipios de la región del Totonacapan y la costa veracruzana con la ciudad de Poza Rica.</p>
            <p>Brindamos conexiones de corto y mediano alcance combinando dinámicas de autobús interurbano con paradas continuas, nuestros principales
                recorridos conectan puntos como Poza Rica, Papantla, Gutiérrez Zamora, Tecolutla, Martínez de la Torre, San Rafael, Casitas, Coyutla, 
                Nautla, Zozocolco, Mesa de Metlaltoyuca, Casillo de Teayo, Defensa, Rancho Playa y Cazones de Herrera. </p>
            <p> Somos considerados por muchos usuarios como una de las líneas tradicionales y pioneras en ofrecer servicio de categoría económica 
                con paradas intermedias entre localidades de alta demanda</p>
            <p>Historicamente nuestras rutas rutas comprendían hacia las ciudades de Tuxpan, Tamiahua, Puebla, Xalapa y Veracruz </p>
        </div>
        
        <div class="stats-sidebar">
            <article class="stat-box">
                <strong class="counter" data-target="30">0</strong><span class="stat-symbol">+</span>
                <p>Años de experiencia</p>
            </article>
            <article class="stat-box">
                <strong class="counter" data-target="250">0</strong>
                <p>Unidades</p>
            </article>
            <article class="stat-box">
                <strong class="counter" data-target="98">0</strong><span class="stat-symbol">%</span>
                <p>Puntualidad</p>
            </article>
        </div>
    </section>

    <!-- MISIÓN, VISIÓN Y VALORES  -->
    <section class="mvv-section">
        <div class="mvv-overlay">
            <div class="container mvv-container">
                <div class="mvv-text-content">
                    <p class="eyebrow" style="color: var(--verde-menta);">NUESTRO COMPROMISO</p>
                    <h2>Misión, Visión y Valores</h2>
                    
                    <div class="mvv-list">
                        <div class="mvv-item">
                            <h3>Misión</h3>
                            <p>Transportar usuarios con calidad, satisfaciendo plenamente a nuestros clientes, realizándolo con base en las necesidades del cliente, con innovación, tecnología y seguridad de nuestros clientes</p>
                        </div>
                        <div class="mvv-item">
                            <h3>Visión</h3>
                            <p>Ser la empresa líder en el Estado de Veracruz con soluciones de transporte con ejemplo de excelencia.</p>
                        </div>
                        <div class="mvv-item">
                            <h3>Valores</h3>
                            <ul>
                                <li><strong>Servicio:</strong> Brindar una atención oportuna y adecuada con el fin de beneficiar a otros.</li>
                                <li><strong>Compromiso:</strong> Ser una organización comprometida con la responsabilidad social y el mejoramiento de la calidad de vida de nuestro entorno.</li>
                                <li><strong>Trabajo en equipo:</strong> Fomentar el compañerismo, la disponibilidad y la comunicación entre todos los colaboradores del organismo.</li>
                                <li><strong>Lealtad:</strong> Actuamos siempre fieles a la empresa y a nuestros colaboradores.</li>
                                <li><strong>Sostenibilidad:</strong> Mantenemos el equilibrio de la organización a través de un modelo económico sostenible y socialmente responsable.</li>
                                <li><strong>Responsabilidad social:</strong> Empresa comprometida a mejorar la calidad de vida de los diferentes grupos de interés, generando un impacto positivo en la sociedad.</li>
                                <li><strong>Puntualidad:</strong> La empresa controla de forma profesional los recursos necesarios para conseguir un rendimiento óptimo.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CARRUSEL ESTILO GALERÍA -->
    <section class="container carousel-section">
        <div class="section-heading text-center" style="justify-content: center; text-align: center; margin-bottom: 2rem; display: block;">
            <p class="eyebrow">GALERÍA</p>
            <h2>Conoce nuestras unidades</h2>
        </div>
        
        <div class="carousel-container">
            <div class="carousel-slide active">
                <img src="{{ asset('img/carrusel1-bus.jpg')}}" alt="Autobús EJE del Golfo">
            </div>
            <div class="carousel-slide">
                <img src="{{ asset('img/carrusel2-bus.jpg')}}" alt="Autobús EJE del Golfo recientemente">
            </div>
            <div class="carousel-slide">
                <img src="{{ asset('img/carrusel3-bus.jpg')}}" alt="Autobús EJE del Golfo en terminal">
            </div>
            <div class="carousel-slide">
                <img src="{{ asset('img/carrusel5-bus.jpg')}}" alt="Autobús EJE del Golfo en taller">
            </div>
            <div class="carousel-slide">
                <img src="{{ asset('img/bus.jpg')}}" alt="Autobús">
            </div>
            
            <button class="carousel-btn prev" onclick="moveSlide(-1)">&#10094;</button>
            <button class="carousel-btn next" onclick="moveSlide(1)">&#10095;</button>
        </div>
    </section>

        <!-- SECCIÓN DE SUSTENTABILIDAD -->
    <section class="container sustainability-section">
        <div class="sustain-content">
            <div class="sustain-logo">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                </svg>
                <span>Eje Sustentable</span>
            </div>
            <h2>Nuestro compromiso con la sustentabilidad</h2>
            <p>Entendemos que el futuro depende de las acciones que tomemos hoy. Por eso, hemos asumido el compromiso de integrar la sustentabilidad en el centro de nuestras operaciones, construyendo un impacto positivo para las personas, el ambiente y la sociedad.</p>
            <p>En Transportes Eje del Golfo ya contamos con unidades impulsadas por Gas Natural, reduciendo significativamente nuestra huella de carbono y contribuyendo a un aire más limpio en nuestra región.</p>
        </div>
        <div class="sustain-image">
            <img src="{{ asset('img/carrusel4-bus.jpg')}}" alt="Autobús ecológico EJE del Golfo">
        </div>
    </section>
@endsection

@push('scripts')
<script>
    let currentSlide = 0;
    const slides = document.querySelectorAll('.carousel-slide');

    function moveSlide(direction) {
        slides[currentSlide].classList.remove('active');
        currentSlide = (currentSlide + direction + slides.length) % slides.length;
        slides[currentSlide].classList.add('active');
    }

    setInterval(() => { moveSlide(1); }, 5000);

    const counters = document.querySelectorAll('.counter');
    const speed = 200;

    const animateCounters = () => {
        counters.forEach(counter => {
            const updateCount = () => {
                const target = +counter.getAttribute('data-target');
                const count = +counter.innerText;
                const inc = target / speed;

                if (count < target) {
                    counter.innerText = Math.ceil(count + inc);
                    setTimeout(updateCount, 20);
                } else {
                    counter.innerText = target;
                }
            };
            updateCount();
        });
    };

    const statsSection = document.querySelector('.stats-sidebar');
    let animated = false;

    const checkScroll = () => {
        if(!statsSection) return;
        const sectionPos = statsSection.getBoundingClientRect().top;
        const screenPos = window.innerHeight / 1.3;

        if (sectionPos < screenPos && !animated) {
            animateCounters();
            animated = true;
        }
    };
    
    window.addEventListener('scroll', checkScroll);
    checkScroll();
</script>
@endpush
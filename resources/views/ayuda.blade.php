@extends('layouts.app')

@section('content')
    <section class="container content-section">
        <p class="eyebrow">CENTRO DE AYUDA</p>
        <h1>Estamos para ayudarte</h1>
        <p class="section-intro">Encuentra respuestas rápidas o envíanos tu consulta a través de nuestro formulario.</p>

        <div class="faq-grid">
            <details class="faq-item">
                <summary>¿Cómo puedo consultar las corridas disponibles y sus precios?</summary>
                <p>Dirígete a la página de inicio o a la sección "Rutas y Horarios". Ahí encontrarás un buscador donde puedes ingresar tu origen, destino y fecha de viaje. Te mostraremos las salidas disponibles, la duración del viaje, la unidad asignada y el precio del boleto. Recuerda que para ver los precios y detalles completos, es necesario que hayas iniciado sesión.</p>
            </details>
            <details class="faq-item">
                <summary>¿Con cuánta anticipación debo llegar a la terminal?</summary>
                <p>Te recomendamos llegar al menos <strong>30 minutos antes</strong> de la hora de salida programada. Esto te dará tiempo suficiente para abordar con calma, guardar tu equipaje y encontrar tu asiento. Nuestras unidades salen con estricta puntualidad, por lo que no podemos garantizar el abordaje si llegas después de la hora indicada.</p>
            </details>
            <details class="faq-item">
                <summary>¿Puedo cambiar o cancelar mi boleto?</summary>
                <p>Los cambios de fecha o ruta están sujetos a disponibilidad de asientos en la nueva corrida y deben realizarse con al menos 2 horas de anticipación a la salida original. Las cancelaciones están sujetas a las políticas de la empresa y podrían generar un cargo administrativo. Te recomendamos contactar a nuestro equipo de atención a clientes lo antes posible para revisar tu caso.</p>
            </details>
            <details class="faq-item">
                <summary>¿Cuánto equipaje puedo llevar conmigo?</summary>
                <p>Cada pasajero puede llevar hasta <strong>dos maletas grandes</strong> (con un peso máximo de 25 kg cada una) en la bodega del autobús, además de un equipaje de mano que debe caber en el portaequipajes superior. No nos hacemos responsables por objetos de valor dentro del equipaje facturado. Si llevas artículos especiales (bicicletas, instrumentos grandes), consulta previamente con atención a clientes.</p>
            </details>
            <details class="faq-item">
                <summary>¿Tienen descuentos especiales para estudiantes o adultos mayores?</summary>
                <p>Sí, contamos con descuentos especiales para estudiantes (presentando credencial vigente) y adultos mayores (con INAPAM). Estos descuentos aplican únicamente en taquilla al momento de comprar el boleto, no en compras en línea, y están sujetos a temporadas y disponibilidad de asientos.</p>
            </details>
            <details class="faq-item">
                <summary>¿Qué pasa si mi autobús se retrasa?</summary>
                <p>En Transportes EJE del Golfo nos comprometemos con la puntualidad. Sin embargo, en caso de retrasos por causas de fuerza mayor (clima, accidentes viales, bloqueos), mantendremos informados a los pasajeros en la terminal y a través de nuestras redes sociales. Si el retraso supera los 60 minutos, tendrás derecho a un reembolso total o cambio de horario sin penalización.</p>
            </details>
        </div>

        <section class="contact-and-privacy" id="contacto">
            <div class="contact-wrapper">
                <h2>Contáctanos</h2>
                <p class="section-intro">¿Tienes dudas o necesitas asistencia? Escríbenos y te responderemos en un máximo de 24 horas hábiles.</p>
                
                @if(session('success'))
                    <div style="background-color: #d4edda; color: #155724; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                        {{ session('success') }}
                    </div>
                @endif

                <form class="contact-form" action="{{ route('contacto.store') }}" method="POST">
                    @csrf
                    
                    <label>Nombre completo
                        <input type="text" name="nombre" placeholder="Tu nombre" 
                               value="{{ Auth::check() ? Auth::user()->name : old('nombre') }}" 
                               {{ Auth::check() ? 'readonly style=background-color:#f0f0f0;' : 'required' }}>
                    </label>
                    
                    <label>Correo electrónico
                        <input type="email" name="correo" placeholder="nombre@correo.com" 
                               value="{{ Auth::check() ? Auth::user()->email : old('correo') }}" 
                               {{ Auth::check() ? 'readonly style=background-color:#f0f0f0;' : 'required' }}>
                    </label>
                    
                    <label>Teléfono de contacto
                        <input type="tel" name="telefono" placeholder="Ej. 782 123 4567" value="{{ old('telefono') }}">
                    </label>
                    
                    <label class="field-full">¿En qué podemos ayudarte?
                        <textarea name="mensaje" rows="4" placeholder="Describe tu duda o problema con el mayor detalle posible..." required>{{ old('mensaje') }}</textarea>
                    </label>
                    
                    <button class="btn-primary" type="submit">Enviar mensaje</button>
                </form>
            </div>

            <div class="privacy-wrapper">
                <div class="card privacy-card">
                    <p class="eyebrow">TUS DATOS, CON CUIDADO</p>
                    <h2>Aviso de Privacidad</h2>
                    <p>Los datos personales que compartas con Transportes EJE del Golfo se utilizarán únicamente para gestionar tus viajes, atender tus solicitudes y brindarte nuestros servicios. No compartimos tu información con terceros para fines comerciales.</p>
                    <a class="text-link" href="{{ route('privacidad') }}">Consulta el aviso de privacidad completo →</a>
                </div>
            </div>

        </section>
    </section>
@endsection
@extends('layouts.app')

@section('content')
    <section class="container content-section policy-copy">
        <p class="eyebrow">INFORMACIÓN LEGAL</p>
        <h1>Términos y condiciones</h1>
        <p class="section-intro">Texto de referencia para el proyecto; deberá revisarse antes de publicarse como documento legal.</p>

        <article class="card">
            <h2>Uso del sitio</h2>
            <p>La información de rutas y horarios publicada en este sitio es ilustrativa mientras se completa su conexión con los sistemas operativos de Transportes EJE del Golfo.</p>
            <h2>Disponibilidad del servicio</h2>
            <p>Los horarios, tarifas y lugares están sujetos a confirmación. Antes de viajar, verifica la información de tu corrida con los canales oficiales de atención.</p>
            <h2>Contacto</h2>
            <p>Para dudas sobre estos términos o los servicios, visita nuestra sección de <a class="text-link" href="{{ route('ayuda') }}">Ayuda</a>.</p>
        </article>
    </section>
@endsection
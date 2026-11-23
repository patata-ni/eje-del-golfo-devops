@extends('layouts.admin')

@section('content')
    <div class="dashboard-header">
        <h1>Mensajes de Clientes</h1>
        <p>Consultas enviadas desde el formulario de contacto del sitio web.</p>
    </div>

    <div class="dashboard-panel">
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            @forelse($mensajes as $msg)
                <div style="padding: 1.5rem; border: 1px solid #eee; border-left: 4px solid var(--verde-esmeralda); border-radius: 8px; background: #fff;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.8rem; border-bottom: 1px solid #f0f0f0; padding-bottom: 0.5rem;">
                        <strong style="color: var(--verde-bosque); font-size: 1.1rem;">{{ $msg->titulo }}</strong>
                        <span style="font-size: 0.85rem; color: #999;">{{ \Carbon\Carbon::parse($msg->fecha)->format('d/m/Y H:i') }}</span>
                    </div>
                    <p style="color: #444; font-size: 0.95rem; line-height: 1.6;">{{ $msg->mensaje }}</p>
                </div>
            @empty
                <div style="text-align: center; padding: 4rem; color: #999;">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">📬</div>
                    <p>No hay mensajes de clientes por el momento.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection
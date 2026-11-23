@extends('layouts.admin')

@section('content')
    <div class="dashboard-header">
        <h1>Notificaciones</h1>
        <p>Mensajes y avisos recientes del sistema y del taller.</p>
    </div>

    <div class="dashboard-panel">
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            @forelse($notificaciones as $noti)
                <div style="padding: 1rem; border: 1px solid #eee; border-left: 4px solid {{ $noti->leida ? '#ccc' : 'var(--verde-esmeralda)' }}; border-radius: 6px; background: {{ $noti->leida ? '#f9f9f9' : '#fff' }};">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <strong style="color: var(--verde-bosque);">{{ $noti->titulo }}</strong>
                        <span style="font-size: 0.8rem; color: #999;">{{ \Carbon\Carbon::parse($noti->fecha)->diffForHumans() }}</span>
                    </div>
                    <p style="color: #555; font-size: 0.95rem;">{{ $noti->mensaje }}</p>
                </div>
            @empty
                <div style="text-align: center; padding: 3rem; color: #999;">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">🔔</div>
                    <p>No tienes notificaciones por el momento.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection
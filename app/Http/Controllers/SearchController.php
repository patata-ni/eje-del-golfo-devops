<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Departure;
use App\Models\Route;

class SearchController extends Controller
{
    public function results(Request $request)
    {
        $request->validate([
            'origen' => ['nullable', 'string', 'max:150'],
            'destino' => ['nullable', 'string', 'max:150'],
        ]);

        return redirect()->route('rutas', $request->only(['origen', 'destino']));
    }

    public function routes(Request $request)
    {
        $query = Departure::with(['schedule.route', 'unit', 'driver.personal'])
            ->whereIn('estado', ['programada', 'en_curso']);

        if ($request->filled('origen')) {
            $query->whereHas('schedule.route', function ($q) use ($request) {
                $q->where('origen', 'LIKE', $request->origen . '%');
            });
        }

        if ($request->filled('destino')) {
            $query->whereHas('schedule.route', function ($q) use ($request) {
                $q->where('destino', 'LIKE', $request->destino . '%');
            });
        }

        $salidasDB = $query->get();

        $corridas = $salidasDB->map(function ($salida) {
            $ruta = $salida->schedule->route;
            $unidad = $salida->unit;
            
            $minutosTotales = $ruta->duracion_minutos ?? 0;
            $horas = floor($minutosTotales / 60);
            $minutosRestantes = $minutosTotales % 60;
            
            $textoDuracion = '';
            if ($horas > 0) $textoDuracion .= $horas . 'h ';
            if ($minutosRestantes > 0 || $horas == 0) $textoDuracion .= $minutosRestantes . 'min';

            // --- OBTENER PARADAS DINÁMICAS ---
            $paradas = $this->obtenerParadas($ruta->origen, $ruta->destino);

            return [
                'id' => $salida->id_salida,
                'origen' => $ruta->origen ?? 'Origen',
                'destino' => $ruta->destino ?? 'Destino',
                'hora_salida' => \Carbon\Carbon::parse($salida->schedule->hora_salida)->format('H:i'),
                'hora_llegada' => $this->calcularHoraLlegada($salida->schedule->hora_salida, $ruta->duracion_minutos),
                'duracion' => trim($textoDuracion),
                'unidad' => $unidad->numero_unidad ?? 'Sin unidad',
                'asientos' => $unidad->capacidad ?? 0,
                'precio' => $ruta->precio ?? 120,
                'tipo' => 'DIRECTO',
                'paradas' => $paradas, 
                'mapa' => [
                    'origen' => $this->getCoordenadas($ruta->origen),
                    'destino' => $this->getCoordenadas($ruta->destino),
                    'waypoints' => $this->obtenerWaypointsCosta($ruta->origen, $ruta->destino),
                    'nombres_paradas' => $paradas, // <-- Enviamos los nombres al mapa
                ],
            ];
        });

        return view('rutas', compact('corridas'));
    }

    // Catálogo de paradas basado en tus imágenes
    private function obtenerParadas($origen, $destino)
    {
        $o = strtolower($origen);
        $d = strtolower($destino);

        // Poza Rica <-> Papantla
        if ((str_contains($o, 'poza') && str_contains($d, 'papantla')) || (str_contains($o, 'papantla') && str_contains($d, 'poza'))) {
            return ['3 Flechas', 'Turbinas campo 2', 'Colonia Miguel Hidalgo', 'Rancho Del Sol', 'Reforma Escolín', 'Rancho Nuevo', 'La Ordeña', 'Campo 20', 'Cerro Grande'];
        }

        // Poza Rica <-> Gutiérrez Zamora
        if ((str_contains($o, 'poza') && str_contains($d, 'gutierrez')) || (str_contains($o, 'gutierrez') && str_contains($d, 'poza'))) {
            return ['3 Flechas', 'Turbinas campo 2', 'Colonia Miguel Hidalgo', 'Rancho Del Sol', 'Reforma Escolín', 'Rancho Nuevo', 'La Ordeña', 'Campo 20', 'Cerro Grande', 'Papantla', 'Venustiano Carranza', 'Totomoxtle', 'El palmar', 'El cedro', 'San Carlos', 'San Pablo', 'La Galeana', 'El Coco'];
        }

        // Poza Rica <-> Martínez de la Torre
        if ((str_contains($o, 'poza') && str_contains($d, 'martinez')) || (str_contains($o, 'martinez') && str_contains($d, 'poza'))) {
            return ['3 Flechas', 'Turbinas campo 2', 'Colonia Miguel Hidalgo', 'Rancho Del Sol', 'Reforma Escolín', 'Rancho Nuevo', 'La Ordeña', 'Campo 20', 'Cerro Grande', 'Papantla', 'Venustiano Carranza', 'Totomoxtle', 'El palmar', 'El cedro', 'San Carlos', 'San Pablo', 'La Galeana', 'El Coco', 'Gutiérrez Zamora', 'Tecolutla', 'Casitas', 'Nautla', 'San Rafael'];
        }

        //Poza Rica <-> Tecolutla
        if ((str_contains($o, 'poza') && str_contains($d, 'tecolutla')) || (str_contains($o, 'tecolutla') && str_contains($d, 'poza'))) {
            return ['3 Flechas', 'Turbinas campo 2', 'Colonia Miguel Hidalgo', 'Rancho Del Sol', 'Reforma Escolín', 'Rancho Nuevo', 'La Ordeña', 'Campo 20', 'Cerro Grande', 'Papantla', 'Venustiano Carranza', 'Totomoxtle', 'El palmar', 'El cedro', 'San Carlos', 'San Pablo', 'La Galeana', 'El Coco', 'Gutiérrez Zamora'];
        }   

        // Papantla <-> Gutiérrez Zamora
        if ((str_contains($o, 'papantla') && str_contains($d, 'gutierrez')) || (str_contains($o, 'gutierrez') && str_contains($d, 'papantla'))) {
            return ['Venustiano Carranza', 'Totomoxtle', 'El palmar', 'El cedro', 'San Carlos', 'San Pablo', 'La Galeana', 'El Coco'];
        }

        //Papantla <-> Martínez de la Torre
        if ((str_contains($o, 'papantla') && str_contains($d, 'martinez')) || (str_contains($o, 'martinez') && str_contains($d, 'papantla'))) {
            return ['Venustiano Carranza', 'Totomoxtle', 'El palmar', 'El cedro', 'San Carlos', 'San Pablo', 'La Galeana', 'El Coco', 'Gutiérrez Zamora', 'Tecolutla', 'Casitas', 'Nautla', 'San Rafael'];
        }

        //Papantla <-> Tecolutla
        if ((str_contains($o, 'papantla') && str_contains($d, 'tecolutla')) || (str_contains($o, 'tecolutla') && str_contains($d, 'papantla'))) {
            return ['Venustiano Carranza', 'Totomoxtle', 'El palmar', 'El cedro', 'San Carlos', 'San Pablo', 'La Galeana', 'El Coco', 'Gutiérrez Zamora'];
        }

        return []; // Si no coincide con ninguna ruta conocida
    }

    private function getCoordenadas($ciudad)
    {
        $ciudad = strtolower($ciudad);
        $coordenadas = [
            'poza rica' => ['lat' => 20.5333, 'lng' => -97.4500],
            'papantla' => ['lat' => 20.4469, 'lng' => -97.3214],
            'martinez' => ['lat' => 20.0667, 'lng' => -97.0500],
            'gutierrez' => ['lat' => 20.4500, 'lng' => -97.0833],
            'tecolutla' => ['lat' => 20.4800, 'lng' => -97.0000],
            'tuxpan' => ['lat' => 20.9500, 'lng' => -97.4000],
            'casitas' => ['lat' => 20.2386, 'lng' => -96.7960],
            'nautla' => ['lat' => 20.2167, 'lng' => -96.7833],
            'san rafael' => ['lat' => 20.1933, 'lng' => -96.8747],
        ];

        foreach ($coordenadas as $nombre => $coords) {
            if (str_contains($ciudad, $nombre)) {
                return $coords;
            }
        }
        return ['lat' => 21.0000, 'lng' => -97.0000]; 
    }

    private function obtenerWaypointsCosta($origen, $destino)
    {
        $origen = strtolower($origen);
        $destino = strtolower($destino);
        
        if (str_contains($destino, 'martinez') && (str_contains($origen, 'poza') || str_contains($origen, 'papantla'))) {
            return [
                ['lat' => 20.4500, 'lng' => -97.0833], // Gutiérrez Zamora
                ['lat' => 20.4800, 'lng' => -97.0000], // Tecolutla
                ['lat' => 20.2386, 'lng' => -96.7960], // Casitas
                ['lat' => 20.2167, 'lng' => -96.7833], // Nautla
                ['lat' => 20.1933, 'lng' => -96.8747], // San Rafael
            ];
        }
        return [];
    }

    private function calcularHoraLlegada($horaSalida, $duracionMinutos)
    {
        if (!$horaSalida || !$duracionMinutos) return '--:--';
        $hora = \Carbon\Carbon::parse($horaSalida);
        return $hora->addMinutes($duracionMinutos)->format('H:i');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class VersionController extends Controller
{
    public function __invoke()
    {
        try {
            DB::select('SELECT 1');
        } catch (\Throwable $exception) {
            return response()->json(['app' => 'Eje del Golfo', 'status' => 'database_unavailable'], 503);
        }

        $file = storage_path('app/build-version.json');
        $version = is_file($file) ? json_decode(file_get_contents($file), true) : ['commit' => 'local'];

        return response()->json(['app' => 'Eje del Golfo', 'status' => 'ok', ...$version])->header('Cache-Control', 'no-store');
    }
}

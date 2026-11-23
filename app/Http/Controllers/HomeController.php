<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Route;

class HomeController extends Controller
{
    public function index()
    {
        $rutasPopulares = Route::where('estado', 'activa')->take(3)->get();
        
        return view('home', compact('rutasPopulares'));
    }
}
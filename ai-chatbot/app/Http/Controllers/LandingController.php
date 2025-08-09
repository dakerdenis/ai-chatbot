<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class LandingController extends Controller
{
    public function index() { return view('landing'); }

    public function widget(Request $request) {
        $path = resource_path('js/widget.js');
        if (!file_exists($path)) {
            return Response::make('// widget missing', 200, ['Content-Type' => 'application/javascript']);
        }
        return Response::make(file_get_contents($path), 200, ['Content-Type' => 'application/javascript']);
    }
}

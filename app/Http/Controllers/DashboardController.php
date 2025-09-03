<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        //https://api.openweathermap.org/data/2.5/weather?q={city name}&appid={API key}
        //?q={city name}&appid={API key}

        $value = Cache::remember('waether', now()->addHour(), function () {
   $response = Http::get('https://api.openweathermap.org/data/2.5/weather', [
            'q'=> 'Kuressaare',
            'appid'=> config('services.weather.key'),
            'units'=> 'metric',

        ]);

        return $response->json();
});

        
        dd($value);
        return Inertia::render('Dashboard');
    }
}

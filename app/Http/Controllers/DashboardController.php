<?php

namespace App\Http\Controllers;

use App\Models\Marker;
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
        $city = $request->input('city', 'Kuressaare');
        $cacheKey = 'weather_' . $city;

        $weather = Cache::remember($cacheKey, now()->addHour(), function () use ($city) {
            $response = Http::get('https://api.openweathermap.org/data/2.5/weather', [
                'q' => $city,
                'appid' => config('services.weather.key'),
                'units' => 'metric',
            ]);
            $data = $response->json();
            if (isset($data['cod']) && $data['cod'] >= 400) {
                return [];
            }
            return $data ?? [];
        });

        $markers = Marker::orderByDesc('id')->get()->map(fn ($m) => [
            'id' => $m->id,
            'name' => $m->name,
            'lat' => $m->latitude,
            'lng' => $m->longitude,
            'description' => $m->description,
            'added' => $m->added,
            'edited' => $m->edited,
        ]);

        return Inertia::render('Dashboard', [
            'weather' => $weather,
            'markers' => $markers,
            'city' => $city,
        ]);
    }
}

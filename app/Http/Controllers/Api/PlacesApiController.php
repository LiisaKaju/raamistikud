<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;

class PlacesApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category' => 'nullable|string|max:120',
            'q' => 'nullable|string|max:120',
            'limit' => 'nullable|integer|min:1|max:50',
            'lat' => 'nullable|numeric|between:-90,90',
            'lon' => 'nullable|numeric|between:-180,180',
            'radius' => 'nullable|integer|min:100|max:50000',
            'lang' => 'nullable|string|max:8',
        ]);

        $apiKey = (string) config('services.geoapify.key');
        if ($apiKey === '') {
            return response()->json([
                'error' => 'Geoapify API key is missing. Set GEOAPIFY_API_KEY in .env.',
            ], 500);
        }

        $category = $validated['category'] ?? 'natural';
        $limit = (int) ($validated['limit'] ?? 20);
        $lang = $validated['lang'] ?? 'et';

        $query = [
            'categories' => $category,
            'limit' => $limit,
            'lang' => $lang,
            'apiKey' => $apiKey,
        ];

        if (! empty($validated['q'])) {
            $query['name'] = $validated['q'];
        }

        if (isset($validated['lat'], $validated['lon'])) {
            $radius = (int) ($validated['radius'] ?? 10000);
            $query['filter'] = sprintf('circle:%s,%s,%d', $validated['lon'], $validated['lat'], $radius);
            $query['bias'] = sprintf('proximity:%s,%s', $validated['lon'], $validated['lat']);
        } else {
            // Geoapify places endpoint expects spatial context; default to Estonia bbox.
            $query['filter'] = 'rect:21.5,57.5,28.3,59.9';
        }

        $cacheKey = 'geoapify_places:'.md5(json_encode($query));

        try {
            $data = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($query): array {
                $response = Http::timeout(15)->get('https://api.geoapify.com/v2/places', $query);
                $response->throw();

                return $response->json();
            });
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'error' => 'Geoapify request failed.',
                'message' => $exception->getMessage(),
            ], 502);
        }

        return response()->json([
            'meta' => [
                'source' => 'geoapify',
                'cached_for_seconds' => 600,
                'filters' => [
                    'category' => $category,
                    'q' => $validated['q'] ?? null,
                    'limit' => $limit,
                    'lat' => $validated['lat'] ?? null,
                    'lon' => $validated['lon'] ?? null,
                    'radius' => $validated['radius'] ?? null,
                    'lang' => $lang,
                ],
            ],
            'data' => $data['features'] ?? [],
        ]);
    }
}

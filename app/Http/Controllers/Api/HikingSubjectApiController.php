<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MyFavoriteSubject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HikingSubjectApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $q = (string) $request->string('q');
        $difficulty = (string) $request->string('difficulty');
        $sort = (string) $request->string('sort', 'latest');
        $limit = max(1, min((int) $request->integer('limit', 20), 100));

        $version = (int) Cache::get('hiking_subjects_cache_version', 1);
        $cacheKey = 'hiking_subjects:'.$version.':'.md5((string) $request->fullUrl());

        $payload = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($q, $difficulty, $sort, $limit): array {
            $query = MyFavoriteSubject::query()->with('user:id,name');

            if ($q !== '') {
                $query->where(function ($builder) use ($q): void {
                    $builder
                        ->where('title', 'like', '%'.$q.'%')
                        ->orWhere('location', 'like', '%'.$q.'%')
                        ->orWhere('description', 'like', '%'.$q.'%');
                });
            }

            if ($difficulty !== '') {
                $query->where('difficulty', $difficulty);
            }

            match ($sort) {
                'distance_asc' => $query->orderBy('distance_km'),
                'distance_desc' => $query->orderByDesc('distance_km'),
                'title_asc' => $query->orderBy('title'),
                default => $query->latest(),
            };

            $items = $query->limit($limit)->get()->map(function (MyFavoriteSubject $item): array {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'image' => $item->image,
                    'description' => $item->description,
                    'location' => $item->location,
                    'difficulty' => $item->difficulty,
                    'distance_km' => (float) $item->distance_km,
                    'author' => $item->user?->name,
                    'created_at' => $item->created_at?->toISOString(),
                ];
            })->values()->all();

            return [
                'meta' => [
                    'cached_until' => now()->addMinutes(10)->toISOString(),
                    'filters' => compact('q', 'difficulty', 'sort', 'limit'),
                ],
                'data' => $items,
            ];
        });

        return response()->json($payload);
    }
}

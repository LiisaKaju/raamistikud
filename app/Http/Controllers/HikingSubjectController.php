<?php

namespace App\Http\Controllers;

use App\Models\MyFavoriteSubject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class HikingSubjectController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = [
            'q' => (string) $request->string('q'),
            'difficulty' => (string) $request->string('difficulty'),
            'sort' => (string) $request->string('sort', 'latest'),
        ];

        $query = MyFavoriteSubject::query()->with('user:id,name');

        if ($filters['q'] !== '') {
            $query->where(function ($builder) use ($filters): void {
                $builder
                    ->where('title', 'like', '%'.$filters['q'].'%')
                    ->orWhere('location', 'like', '%'.$filters['q'].'%');
            });
        }

        if ($filters['difficulty'] !== '') {
            $query->where('difficulty', $filters['difficulty']);
        }

        match ($filters['sort']) {
            'distance_asc' => $query->orderBy('distance_km'),
            'distance_desc' => $query->orderByDesc('distance_km'),
            'title_asc' => $query->orderBy('title'),
            default => $query->latest(),
        };

        return Inertia::render('hiking/Index', [
            'items' => $query->paginate(24)->withQueryString(),
            'filters' => $filters,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|url|max:2048',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'difficulty' => 'required|string|in:easy,medium,hard',
            'distance_km' => 'required|numeric|min:0.5|max:500',
        ]);

        MyFavoriteSubject::create([
            ...$validated,
            'user_id' => (int) $request->user()->id,
        ]);

        $this->bumpApiCacheVersion();

        return redirect()->route('hiking.index')->with('success', 'Matka kirje lisatud.');
    }

    private function bumpApiCacheVersion(): void
    {
        $current = (int) Cache::get('hiking_subjects_cache_version', 1);
        Cache::forever('hiking_subjects_cache_version', $current + 1);
    }
}

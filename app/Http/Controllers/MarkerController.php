<?php

namespace App\Http\Controllers;

use App\Models\Marker;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MarkerController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            Marker::orderByDesc('id')->get()
        );
    }

    public function store(Request $request)
    {
        $request->merge([
            'latitude' => $request->input('lat') ?? $request->input('latitude'),
            'longitude' => $request->input('lng') ?? $request->input('longitude'),
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'description' => 'nullable|string',
        ]);

        Marker::create([
            ...$validated,
            'added' => now(),
            'edited' => now(),
        ]);

        return redirect()->route('dashboard');
    }

    public function update(Request $request, Marker $marker)
    {
        $request->merge([
            'latitude' => $request->input('lat') ?? $request->input('latitude'),
            'longitude' => $request->input('lng') ?? $request->input('longitude'),
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'description' => 'nullable|string',
        ]);

        $marker->update([...$validated, 'edited' => now()]);

        return redirect()->route('dashboard');
    }

    public function destroy(Marker $marker): Response
    {
        $marker->delete();

        return response()->noContent();
    }
}

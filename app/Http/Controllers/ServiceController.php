<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use Illuminate\Support\Str; // Import de Str

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $services = Service::paginate(4);
        return view('admin.services.index', compact('services'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            // 'identifier' => 'required|string|max:255|unique:services,identifier',
            'description' => 'nullable|string',
        ]);

          // Générer dynamiquement l'identifiant
        $validated['identifier'] = Str::slug($validated['name']) . '-' . uniqid();
        Service::create($validated);

        return redirect()->route('admin.services.index')->with('success', 'Service de traduction ajouté avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Si le nom a changé, régénérer l'identifiant
        if ($request->name !== $service->name) {
            $validated['identifier'] = Str::slug($validated['name']) . '-' . uniqid();
        }

        // Mettre à jour le service avec les données validées
        $service->update($validated);

        return redirect()->route('admin.services.index')->with('success', 'Service de traduction mis à jour avec succès.');
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()->route('admin.services.index')->with('success', 'Service supprimé avec succès.');
    }
}

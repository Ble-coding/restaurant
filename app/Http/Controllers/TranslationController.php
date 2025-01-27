<?php

namespace App\Http\Controllers;

use App\Models\TranslationSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\Service;
use Illuminate\Validation\Rule;


class TranslationController extends Controller
{
     /**
     * Affiche la page des paramètres de traduction.
     */
    public function index()
    {
        $services = Service::all();
        // Pré-remplir la langue cible par défaut
        // Définir la langue cible par défaut en fonction de la source
        $defaultSourceLang = old('source_lang', 'FR'); // Langue source par défaut
        $defaultTargetLang = TranslationSetting::getTargetLang($defaultSourceLang);
        $settings = TranslationSetting::with('service')->paginate(4);
        return view('admin.translation.index', compact('settings','services','defaultSourceLang','defaultTargetLang'));
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Enregistre ou met à jour les paramètres de traduction.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'api_key' => 'required|string|max:255|unique:translation_settings,api_key,NULL,id,service_id,' . $request->service_id,
            'service_id' => 'required|exists:services,id', // Vérifie que le service existe dans la table services
            'source_lang' => 'required|string|in:FR,EN',
        ]);

        // Définir automatiquement la langue cible
        $validated['target_lang'] = TranslationSetting::getTargetLang($validated['source_lang']);

        // Création d'un nouveau paramètre de traduction
        TranslationSetting::create($validated);

        return redirect()->route('admin.translations.index')
            ->with('success', 'Paramètres de traduction ajoutés avec succès.');
    }

    /**
     * Traduit un texte donné en fonction des paramètres configurés.
     */
    public function translate(Request $request)
    {
        $validated = $request->validate([
            'text' => 'required|string', // Texte à traduire
        ]);

        // Récupération des paramètres de traduction de l'utilisateur
        $settings = TranslationSetting::where('user_id', Auth::id())->first();

        if (!$settings) {
            return back()->withErrors(['error' => 'Veuillez configurer vos paramètres de traduction.']);
        }

        // Récupération du service correspondant
        $service = Service::find($settings->service_id);

        if (!$service) {
            return back()->withErrors(['error' => 'Service de traduction non trouvé.']);
        }

        // Configuration de l'URL et des paramètres en fonction du service sélectionné
        $url = '';
        $payload = [];

        if ($service->identifier === 'deepl') {
            $url = 'https://api-free.deepl.com/v2/translate';
            $payload = [
                'auth_key' => $settings->api_key,
                'text' => $validated['text'],
                'source_lang' => $settings->source_lang,
                'target_lang' => $settings->target_lang,
            ];
        } elseif ($service->identifier === 'google') {
            $url = 'https://translation.googleapis.com/language/translate/v2';
            $payload = [
                'key' => $settings->api_key,
                'q' => $validated['text'],
                'source' => strtolower($settings->source_lang),
                'target' => strtolower($settings->target_lang),
                'format' => 'text',
            ];
        } else {
            return back()->withErrors(['error' => 'Type de service de traduction non pris en charge.']);
        }

        // Envoi de la requête HTTP
        $response = Http::post($url, $payload);

        if ($response->failed()) {
            return back()->withErrors(['error' => 'Erreur lors de la traduction : ' . $response->json('message')]);
        }

        // Extraction du texte traduit
        $translatedText = $response->json()['translations'][0]['text']
            ?? $response->json()['data']['translations'][0]['translatedText']
            ?? null;

        if (!$translatedText) {
            return back()->withErrors(['error' => 'Une erreur s\'est produite lors de la traduction.']);
        }

        return view('admin.translation.index', compact('translatedText'));
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
    public function edit(TranslationSetting $translation)
    {
        $services = Service::all();
        $defaultSourceLang = $translation->source_lang ?? 'FR';
        $defaultTargetLang = TranslationSetting::getTargetLang($defaultSourceLang);

        return view('admin.translation.edit', compact('translation', 'services', 'defaultSourceLang', 'defaultTargetLang'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TranslationSetting $translation)
    {
        if (!$translation) {
            abort(404, 'Enregistrement introuvable');
        }

        $validated = $request->validate([
            'api_key' => [
                'required',
                'string',
                'max:255',
                Rule::unique('translation_settings', 'api_key')
                    ->where(function ($query) use ($request) {
                        $query->where('service_id', $request->service_id);
                    })
                    ->ignore($translation->id),
            ],
            'service_id' => 'required|exists:services,id',
            'source_lang' => 'required|string|in:FR,EN',
        ]);

        $validated['target_lang'] = TranslationSetting::getTargetLang($validated['source_lang']);
        $translation->update($validated);

        return redirect()->route('admin.translations.index')
            ->with('success', 'Paramètres de traduction mis à jour avec succès.');
    }



    // public function update(Request $request, TranslationSetting $setting)
    // {
    //     $validated = $request->validate([
    //         'api_key' => 'required|string|max:255',
    //         'service_id' => 'required|exists:services,id',
    //         'source_lang' => 'required|string|in:FR,EN',
    //     ]);

    //     $validated['target_lang'] = TranslationSetting::getTargetLang($validated['source_lang']);

    //     // Debug : données validées
    //     dd('Données validées : ', $validated);

    //     $setting->update($validated);

    //     // Debug : données après mise à jour
    //     dd('Données mises à jour : ', $setting->toArray());

    //     return redirect()->route('admin.translations.index')
    //         ->with('success', 'Paramètres de traduction mis à jour avec succès.');
    // }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TranslationSetting $translation)
    {
        $translation->delete();
        return redirect()->route('admin.translations.index')->with('success', 'Configuration supprimée avec succès.');
    }
}

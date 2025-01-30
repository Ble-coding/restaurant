<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Stichoza\GoogleTranslate\GoogleTranslate;
use App\Models\Permission;


class TranslatePermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // protected $signature = 'app:translate-permissions';
    protected $signature = 'permissions:translate';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Traduire automatiquement les permissions et remplir les nouvelles colonnes de traduction';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $translator = new GoogleTranslate();

        $permissions = Permission::all();
        foreach ($permissions as $permission) {
            // Traduction du name
            $translator->setSource('fr')->setTarget('en');
            $permission->name_en = $translator->translate($permission->name);

            $translator->setSource('en')->setTarget('fr');
            $permission->name_fr = $translator->translate($permission->name_en);

            // Traduction de la description (translation_fr → translation_en)
            $translator->setSource('fr')->setTarget('en');
            $permission->translation_en = $translator->translate($permission->translation_fr);

            // Sauvegarde des modifications
            $permission->save();

        }
        $this->info('Permissions traduites et mises à jour avec succès !');

    }
}

<?php

namespace App\Livewire;

use Livewire\Component;

class DefinitionOfDone extends Component
{
    public $criteria = [];

    public function mount()
    {
        $this->criteria = [
            'Codequalität' => [
                'Der Code ist funktional und erfüllt die Akzeptanzkriterien.',
                'Der Code ist sauber, verständlich, gut strukturiert und folgt den PSR-Standards sowie den Laravel Best Practices.',
                'Die Laravel-Helperfunktionen sind den PHP-Funktionen vorzuziehen.',
                'Konstanten sind in config-Dateien definiert und nicht im Code hardcodiert 💀.',
            ],
            'Testing' => [
                'Es existieren Unit und Integrationstests für alle Funktionalitäten.',
                'Alle Tests laufen erfolgreich.',
                'Die Testabdeckung beträgt mindestens 95% und wird nicht verringert.',
                'Zur Verifizierung der Funktionalität wurden manuelle Tests im Browser durchgeführt.',
            ],
            'Dokumentation' => [
                'Der Code ist an notwendigen Stellen mit aussagekräftigen Kommentaren versehen.',
                'Bei Bedarf wurde die README-Datei aktualisiert.',
                'Bei Architekturänderungen wurde die entsprechende Dokumentation aktualisiert.',
            ],
            'Security' => [
                'Es wurden Sicherheitsaspekte wie Validierung, Authentifizierung und Autorisierung berücksichtigt.',
                'Eingaben werden validiert (z.B. gegen SQL-Injection, XSS).',
                'Sensible Daten werden verschlüsselt gespeichert.',
            ],
            'Deployment' => [
                'Wenn nötig wurde die CI/CD Pipeline angepasst.',
                'Die automatische CI/CD Pipeline wurde erfolreich durchlaufen.',
                'Die Migrationen und Seeder wurden ausgeführt.',
                'Die .env.example-Datei ist aktuell und beinhaltet alle benötigten Umgebungsvariablen.',
            ],
            'Abnahme' => [
                'Die User Story wurde von allen Teammitgliedern abgenommen.',
                'Ein Code Review wurde durchgeführt und alle Anmerkungen wurden adressiert.',
            ],
        ];
    }
}

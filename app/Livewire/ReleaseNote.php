<?php

namespace App\Livewire;

use Livewire\Component;

class ReleaseNote extends Component
{
    public $notes = [];

    public function mount()
    {
        $this->notes = [
            'Neue Features' => [
                'User Management',
                'Automatische Extrahierung der Bestandteile Anrede, Titel, Geschlecht, Vorname, Nachname und Sprache aus der Eingabe',
                'Manuelle Anpassung der Bestandteile, bei der auch die Briefanrede angepasst wird',
                'Hinzufügen neuer Titel',
                'Automatische Generierung einer Briefanrede',
                'Speichern und Abrufen von Briefanreden',
                'Zentrale Verwaltung der Konfiguration',
            ],
        ];
    }
}

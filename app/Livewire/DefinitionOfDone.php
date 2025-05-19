<?php

namespace App\Livewire;

use Livewire\Component;

class DefinitionOfDone extends Component
{
    public $criteria = [];

    public function mount()
    {
        $this->criteria = [
            'Die Eingabe wird zuverlässig analysiert und in die Bestandteile Anrede, Titel, Geschlecht, Vorname, Nachname und Sprache zerlegt. Dabei werden die Top 20 Titel erkannt. Wenn keine Sprache zugeordnet werden kann, wird DE verwendet.',
            'Die extrahierten Informationen sind korrekt und vollständig.',
            'Sonderfälle wie Doppelnamen oder fehlende Titel werden korrekt behandelt oder entsprechend gekennzeichnet.',
        ];
    }
}

<?php

namespace App\Livewire;

use Livewire\Component;

class UserStories extends Component
{
    public $stories = [];

    public function mount()
    {
        $this->stories = [
            [
                'title' => 'Automatische Extraktion',
                'description' => 'Als Sachbearbeiter möchte ich, dass die Bestandteile Anrede, Titel, Geschlecht, Vorname, Nachname und Sprache automatisch korrekt aus der Eingabe ausgelesen und angezeigt werden, damit ich manuelle Eingaben vermeiden und Zeit sparen kann.',
                'criteria' => [
                    'Wenn ich eine Visitenkarte einlese, wird diese zuverlässig analysiert und in die Felder Anrede, Titel, Geschlecht, Vorname, Nachname und Sprache mit den entsprechenden Informationen gefüllt werden. Dabei werden die Top 20 Titel erkannt. Wenn keine Sprache zugeordnet werden kann, wird DE verwendet.',
                    'Selbst bei unvollständigen Eingaben wird versucht ein sinnvolles Ergebnis zu liefern gegebenenfalls mit konfigurierbaren Standardwerten.',
                    'Sonderfälle wie Doppelnamen, Adelstitel oder fehlende Titel werden korrekt behandelt.',
                    'Wenn die Nutzereingabe nur ein Wort enthält, wird dieses standardmäßig als Nachname zugeordnet.',
                    'Wenn keine Anrede gegeben ist, wird das Geschlecht anhand des Vornamens bestimmt und die Anrede entsprechend gesetzt.',
                    'Wenn ein Wort mit einem Punkt endet, wird es als Titel erkannt und entsprechend zugeordnet.',
                ],
                'priority' => 'hoch',
            ],
            [
                'title' => 'Generierung Briefanrede',
                'description' => 'Als Sachbearbeiter möchte ich, dass eine korrekte Briefanrede zu meiner Eingabe generiert wird, damit ich diese nicht manuell formulieren muss.',
                'criteria' => [
                    'Die Briefanrede enthält eine passende Grußform sowie den korrekt formatierten Nachnamen.',
                    'Sonderfälle wie Doppelnamen, fehlende Titel oder unübliche Anreden werden berücksichtigt und korrekt formatiert.',
                ],
                'priority' => 'hoch',
            ],
            [
                'title' => 'Titelverwaltung',
                'description' => 'Als Sachbearbeiter möchte ich neue Titel zur Liste der erkennbaren Titel hinzufügen können, damit diese bei der automatischen Extraktion ebenfalls berücksichtigt werden.',
                'criteria' => [
                    'Ich kann neue Titel in das System hinzufügen.',
                    'Neu hinzugefügte Titel werden bei zukünftigen Eingaben auch automatisch erkannt und korrekt zugeordnet.',
                ],
                'priority' => 'mittel',
            ],
            [
                'title' => 'Manuelle Anpassung',
                'description' => 'Als Sachbearbeiter möchte ich nachdem die Bestandteile Anrede, Titel, Geschlecht, Vorname, Nachname und Sprache automatisch extrahiert wurden diese auch manuell anpassen können, um Fehlzuordnungen verbessern zu können.',
                'criteria' => [
                    'Ich kann manuell die Bestandteile zuordnen.',
                    'Die manuelle Eingabe überschreibt die automatische Erkennung, wenn vorhanden.',
                ],
                'priority' => 'mittel',
            ],
            [
                'title' => 'Persistenz',
                'description' => 'Als Sachbearbeiter möchte ich, dass meine Eingaben und die dazugehörigen Ergebnisse während der Sitzung gespeichert und abrufbar sind, damit ich nachvollziehen kann, welche Daten bereits verarbeitet wurden.',
                'criteria' => [
                    'Ich kann eine generierte Briefanrede abspeichern.',
                    'Ich kann jederzeit die bisherigen gespeicherten Eingaben einsehen.',
                ],
                'priority' => 'mittel',
            ],
        ];
    }
}

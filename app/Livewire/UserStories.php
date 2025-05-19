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
                    'Die Eingabe wird zuverlässig analysiert und in die Bestandteile Anrede, Titel, Geschlecht, Vorname, Nachname und Sprache zerlegt. Dabei werden die Top 20 Titel erkannt. Wenn keine Sprache zugeordnet werden kann, wird DE verwendet.',
                    'Die extrahierten Informationen sind korrekt und vollständig.',
                    'Sonderfälle wie Doppelnamen oder fehlende Titel werden korrekt behandelt oder entsprechend gekennzeichnet.',
                ],
            ],
            [
                'title' => 'Generierung Briefanrede',
                'description' => 'Als Sachbearbeiter möchte ich, dass eine korrekte Briefanrede zu meiner Eingabe generiert wird, damit ich diese nicht manuell formulieren muss.',
                'criteria' => [
                    'Die Briefanrede enthält eine passende Grußform sowie den korrekt formatierten Namen.',
                    'Sonderfälle wie Doppelnamen, fehlende Titel oder unübliche Anreden werden korrekt formatiert.',
                ],
            ],
            [
                'title' => 'Titelverwaltung',
                'description' => 'Als Sachbearbeiter möchte ich neue Titel zur Liste der erkennbaren Titel hinzufügen können, damit diese bei der automatischen Extraktion ebenfalls berücksichtigt werden.',
                'criteria' => [
                    'Der Nutzer kann neue Titel in das System hinzufügen.',
                    'Neu hinzugefügte Titel werden bei zukünftigen Eingaben auch automatisch erkannt und korrekt zugeordnet.',
                ],
            ],
            [
                'title' => 'Manuelle Extraktion',
                'description' => 'Als Sachbearbeiter möchte ich die Bestandteile Anrede, Titel, Geschlecht, Vorname, Nachname und Sprache auch manuell extrahieren können, damit die Zuordnung auch erfolgen kann wenn die automatische Zuordnung scheitert.',
                'criteria' => [
                    'Der Nutzer kann manuell die Bestandteile zuordnen.',
                    'Die manuelle Eingabe überschreibt die automatische Erkennung, wenn vorhanden.',
                ],
            ],
            [
                'title' => 'Ein-Wort-Zuordnung',
                'description' => 'Als Sachbearbeiter möchte ich, dass eine Eingabe, die nur aus einem einzigen Wort besteht, automatisch als Nachname interpretiert wird, damit auch unvollständige oder knappe Eingaben sinnvoll verarbeitet werden können.',
                'criteria' => [
                    'Wenn die Nutzereingabe nur ein Wort enthält, wird dieses standardmäßig als Nachname zugeordnet.',
                    'Es erfolgt keine automatische Zuweisung zu Vorname, Titel oder Anrede bei einer Ein-Wort-Eingabe.',
                ],
            ],
            [
                'title' => 'Sitzungsspeicherung',
                'description' => 'Als Sachbearbeiter möchte ich, dass meine Eingaben und die dazugehörigen Ergebnisse während der Sitzung gespeichert und abrufbar sind, damit ich nachvollziehen kann, welche Daten bereits verarbeitet wurden.',
                'criteria' => [
                    'Alle Nutzereingaben sowie die daraus extrahierten Informationen werden für die Dauer der Sitzung gespeichert.',
                    'Der Nutzer kann während der Sitzung jederzeit die bisherigen Eingaben und deren Ergebnisse einsehen.',
                    'Die Anzeige der Historie ist übersichtlich und nach Zeit oder Reihenfolge geordnet.',
                ],
            ],
        ];
    }
}

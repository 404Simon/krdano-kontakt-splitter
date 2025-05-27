<div>
    <flux:heading size="xl">Design</flux:heading>

    <div class="max-w-3xl px-4 mt-4">
        <flux:heading class="mt-3" size="lg">
            Erster Entwurf
        </flux:heading>
        <flux:text class="mt-1">
            Im ersten Anlauf wurde die komplette Extraktion mit KI realisiert. Diese Entscheidung wurde revidiert, um
            Kosten und Latenz zu vermeiden. Außerdem gab es beim Einsatz der KI Nebeneffekte, wie z.B. die
            Vervollständigung des Vornamens basierend auf dem Nachname für populäre Namen wie z.B. Merkel. Siehe
            <flux:link target="blank"
                href="https://github.com/404Simon/krdano-kontakt-splitter/commit/a5227c657bf2db6e7c91b034503157d5578a5c8b">
                Commit</flux:link>.
        </flux:text>
        <flux:heading class="mt-3" size="lg">
            Aktuelle Architektur
        </flux:heading>
        <flux:text class="mt-1">
            Die Erkennung erfolgt durch String-Parsing. Die Hauptkomponente des Frontends ist der <code>KontaktParser</code>, welcher
            sowohl auf den <code>KontaktParserService</code> als auch auf den <code>LetterSalutationService</code> zugreift. Der
            <code>KontaktparserService</code> nutzt den neuen Pipeline-Ansatz aus Laravel 12, um die verschiedenen Schritte der
            Extraktion zu realisieren. Die Extraktion der einzelnen Bestandteile wurde jeweils innerhalb einer
            Invocable-Klasse realisiert, welche eine Pipe-Stage darstellt. Dadurch können die einzelnen Schritte besser
            getestet und gewartet werden. Der <code>LetterSalutationService</code> ist für die Generierung der Briefanrede zuständig.
            Dabei werden die extrahierten Informationen verwendet.
        </flux:text>
        <figure class="mt-8 w-xl mx-auto">
            <img src="{{ asset('assets/Architektur_dark.drawio.svg') }}" alt="Design Image" class="hidden dark:block">
            <img src="{{ asset('assets/Architektur_light.drawio.svg') }}" alt="Design Image" class="block dark:hidden">
            <figcaption class="text-center mt-2">
                <flux:text>
                    Architekturdiagramm der aktuellen Implementierung
                </flux:text>
            </figcaption>
        </figure>
        <flux:text class="mt-4">
            Es gibt vier verschiedene Pipe-Klasssen. Bei dem <code>SalutationExtractor</code> wird die Anrede extrahiert, wenn
            vorhanden. Ist eine vorhanden, wird daraus auch die Sprache ermittelt. Der <code>TitleExtractor</code> sucht in der
            Eingabe nach Titeln - Standard- sowie benutzerdefinerte. Wenn ein Wort mit einem Punkt endet, wird angenommen, dass 
            es sich um einen Titel handelt. Bei dem <code>NameExtractor</code> wird
            der Vor- und Nachname extrahiert. Dabei wird das letzte Wort als Nachname automatisch angenommen, außer es
            werden Einleitungen für einen Adelstitel gefunden. Der <code>GenderExtractor</code> ermittelt das Geschlecht basierend
            auf dem Vornamen. Dazu wird eine <flux:link target="blank" href="https://github.com/tuqqu/gender-detector">
                Library
            </flux:link> verwendet.
        </flux:text>
        <flux:text class="mt-4">
            Es wird immer versucht, eine sinnvolle Ausgabe zu generieren, selbst wenn der eingegebene unstrukturierte
            Text fehlerhaft oder unvollständig ist. Im Notfall werden Defaults verwendet, welche manuell angepasst
            werden können. Anstelle die Werte in den Klassen hard 💀 zu kodieren, werden sie in einer zentralen <flux:link
                target="blank"
                href="https://github.com/404Simon/krdano-kontakt-splitter/blob/main/config/languages.php">
                Konfigurationsdatei</flux:link> abgespeichert. Laravel bietet über die Config-Facade oder die
            config-Helperfunktion Zugriff auf diese Konfiguration.
        </flux:text>
        <flux:text class="mt-4">
            Zuerst wird eine Ausgabe für die unstrukturierte Eingabe generiert, anschließend können die automatisch
            extrahierten Werte bei Bedarf manuell angepasst werden. Die Anpassungen überschreiben die automatisch
            ermittelten Werte.
        </flux:text>
        <flux:text class="mt-4">
            Es gibt ein Usermanagement, um den Zugriff auf die Anwendung zu regulieren.
            Jeder Nutzer kann eigene Titel verwalten. Außerdem kann jeder Nutzer extrahierte Werte mitsamt der
            Briefanrede speichern und erneut einsehen.
        </flux:text>
        <flux:heading class="mt-3" size="lg">
            Techstack
        </flux:heading>
        <flux:text class="mt-4">
            Die Anwendung ist in Laravel, einem PHP-Framework, geschrieben.
            Die Benutzeroberfläche wurde mit Livewire als Single Page Application (SPA) konzipiert, um eine schnelle und
            reaktive Benutzererfahrung zu bieten. Es werden Tailwind CSS und FluxUI-Komponenten für ein simples und
            responsives Design verwendet. Die Geschäftslogik ist in Service gekapselt, um eine klare Trennung von Logik
            und Präsentation zu gewährleisten. Dadurch wird die Einbindung in andere Projekte erleichtert. Fürs Testing
            wird PHPUnit verwendet. Dabei liegt der Fokus darauf, die Geschäftslogik mittels Unit- und Feature-Tests zu
            überprüfen.
        </flux:text>
    </div>
</div>

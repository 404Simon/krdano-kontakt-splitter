<div>
    <flux:heading size="xl">Release Note</flux:heading>

    <div class="max-w-3xl px-4">
        @foreach ($notes as $title => $noteList)
            <div class="max-w-3xl">
                <flux:heading class="mt-3" size="lg">
                    {{ $title }} </flux:heading>
                <ol class="list-disc pl-6 space-y-2 mt-1">
                    @foreach ($noteList as $note)
                        <li>
                            <flux:text>{{ $note }}</flux:text>
                        </li>
                    @endforeach
                </ol>
            </div>
        @endforeach
        <flux:heading class="mt-3" size="lg">
            Anleitung
        </flux:heading>
        <flux:text class="mt-1">
            Nach der Anmeldung wird die Splitter-Seite gezeigt. Dort kann die unstrukturierte Eingabe in das Textfeld eingegeben werden. Bei jeder Änderung wird die Extraktion neu gestartet. Die Felder können manuell angepasst werden, wodurch die Briefanrede automatisch angepasst wird. Bei Bedarf kann diese Eingabe mitsamt Briefanrede abgespeichert werden. Diese kann in der Seitennavigation unter "Anreden" eingesehen werden. Dort kann diese auch gelöscht werden. 
        </flux:text>
        <flux:text class="mt-1">
            Bei "Titel" kann ein neuer Titel hinzugefügt werden, der noch nicht in den Standardtiteln enthalten sein darf. Die angelegten Titel werden mitsamt der Standardtiteln bei der Titelextraktion berücksichtigt.
        </flux:text>
        <flux:heading class="mt-3" size="lg">
            Tests
        </flux:heading>
        <flux:text class="mt-1">
            Die Tests befinden sich in dem Ordner <flux:link href="https://github.com/404Simon/krdano-kontakt-splitter/tree/main/tests" target="blank">tests</flux:link>, die Coverage ist <flux:link href="https://coverage.krdano.wittmann-simon.de/index.html" target="blank">hier</flux:link> einsehbar.
        </flux:text>
    </div>
</div>

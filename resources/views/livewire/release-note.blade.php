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
            Tests
        </flux:heading>
        <flux:text class="mt-1">
            Die Tests befinden sich in dem Ordner <flux:link href="https://github.com/404Simon/krdano-kontakt-splitter/tree/main/tests">tests</flux:link>, die Coverage ist <flux:link href="https://coverage.krdano.wittmann-simon.de/index.html">hier</flux:link> einsehbar.
        </flux:text>
    </div>
</div>

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
    </div>
</div>

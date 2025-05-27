<div>
    <flux:heading size="xl">Definition of Done</flux:heading>

    <div class="max-w-3xl px-4">
        @foreach ($criteria as $title => $criteriaList)
            <div class="max-w-3xl">
                <flux:heading class="mt-3" size="lg">
                    {{ $title }} </flux:heading>
                <ol class="list-disc pl-6 space-y-2 mt-1">
                    @foreach ($criteriaList as $dod)
                        <li>
                            <flux:text>{{ $dod }}</flux:text>
                        </li>
                    @endforeach
                </ol>
            </div>
        @endforeach
    </div>
</div>

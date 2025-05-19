<div>
    <flux:heading size="xl">User Stories</flux:heading>

    <div class="space-y-6">
        @foreach ($stories as $index => $story)
            <div class="max-w-3xl px-4">
                <flux:heading wire:model="story.title" class="mt-3" size="lg">
                    {{ "Story $index: " . $story['title'] }} </flux:heading>
                <flux:text class="mt-1" variant="strong">
                    {{ $story['description'] }}
                </flux:text>
                <flux:heading class="mt-4">Akzeptanzkriterien</flux:heading>
                <ol class="list-disc pl-6 space-y-2 mt-1">
                    @foreach ($story['criteria'] as $criterion)
                        <li>
                            <flux:text>{{ $criterion }}</flux:text>
                        </li>
                    @endforeach
                </ol>
            </div>
        @endforeach
    </div>
</div>

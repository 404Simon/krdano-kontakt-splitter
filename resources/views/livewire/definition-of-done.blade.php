<div>
    <flux:heading size="xl">Definition of Done</flux:heading>

    <div class="max-w-3xl px-4">
        <ol class="list-disc pl-6 space-y-2 mt-1">
            @foreach ($criteria as $criterion)
                <li>
                    <flux:text>{{ $criterion }}</flux:text>
                </li>
            @endforeach
        </ol>
    </div>
</div>

<div>
    <flux:heading size="xl">Unterstützte Titel</flux:heading>
    @php($defaults = config('languages.defaultSupportedTitles', []))
    <flux:text class="mt-4">
        Das System unterstützt von Haus aus bereits die Titel
        {{ implode(', ', $defaults) }}.
    </flux:text>
    <div class="overflow-x-auto mt-4 max-w-lg">
        @if ($this->titles->isEmpty())
            <flux:text variant="strong">Es wurden noch keine zusätzlich unterstützte Titel hinterlegt.
            </flux:text>
        @else
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Anrede</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($this->titles as $title)
                        <tr wire:key="{{ $title->id }}" class="hover:bg-gray-100 dark:hover:bg-gray-900">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-200">
                                {{ $title->title }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <x-button type="button" wire:confirm="Are you sure you want to delete this?"
                                    wire:click="delete({{ $title->id }})" size="sm" variant="danger">
                                    Löschen
                                </x-button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <flux:modal.trigger name="new-title">
        <flux:button variant="primary" class="mt-4">Titel hinzufügen</flux:button>
    </flux:modal.trigger>

    <flux:modal name="new-title" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Titel hinzufügen</flux:heading>
            </div>
            <form wire:submit="saveTitle">
                <flux:input wire:model.defer="newTitle" label="Titel" placeholder="Dr." />
                <div class="flex">
                    <flux:spacer />

                    <flux:button class="mt-2" type="submit" variant="primary">hinzufügen</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>

<div class="p-4 space-y-4 mx-4 md:mx-30 my-10">
    <div>
        <form wire:submit="submit" class="flex items-end space-x-2">
            <div class="flex-1">
                <x-input class="text-black w-full" label="Unstructured Input" hint="Insert your unstructured input"
                    wire:model.defer="unstructured" />
            </div>
            <x-button type="submit">Analysieren</x-button>
        </form>
    </div>
    @isset($structured)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            {{-- left column: raw data --}}
            <div class="space-y-1">
                @isset($structured['salutation'])
                    <div class="text-gray-700 dark:text-gray-200">
                        Anrede: <span class="font-semibold">{{ $structured['salutation'] }}</span>
                    </div>
                @endisset
                @isset($structured['title'])
                    <div class="text-gray-700 dark:text-gray-200">
                        Titel: <span class="font-semibold">{{ $structured['title'] }}</span>
                    </div>
                @endisset
                @isset($structured['gender'])
                    <div class="text-gray-700 dark:text-gray-200">
                        Geschlecht: <span class="font-semibold">{{ $structured['gender'] }}</span>
                    </div>
                @endisset
                @isset($structured['firstname'])
                    <div class="text-gray-700 dark:text-gray-200">
                        Vorname: <span class="font-semibold">{{ $structured['firstname'] }}</span>
                    </div>
                @endisset
                @isset($structured['lastname'])
                    <div class="text-gray-700 dark:text-gray-200">
                        Nachname: <span class="font-semibold">{{ $structured['lastname'] }}</span>
                    </div>
                @endisset
                @isset($structured['language'])
                    <div class="text-gray-700 dark:text-gray-200">
                        Sprache: <span class="font-semibold">{{ $structured['language'] }}</span>
                    </div>
                @endisset
            </div>

            {{-- right column: formatted display --}}
            <div class="space-y-1">
                <div class="text-sm text-gray-600 dark:text-gray-400">Briefanrede:</div>
                <div class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $structured['letter_salutation'] ?? '–' }}</div>
            </div>
            <x-button wire:click="reevaluateUsingAI" type="button">Use AI</x-button>
            <x-button wire:click="save" type="button">Speichern</x-button>
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif
        </div>
    @endisset
</div>

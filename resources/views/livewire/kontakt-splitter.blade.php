<div class="p-4 space-y-4 mx-4 md:mx-20 my-10">
    <div class="flex items-end space-x-2">
        <div class="flex-1">
            <x-input label="Unstrukturierter Input" wire:model.live="unstructured" autofocus />
        </div>
        <div>
            <x-button icon="sparkles" wire:click="reevaluateUsingAI" type="button" :disabled="!$this->unstructured">
                Use AI
            </x-button>
        </div>
    </div>
    @isset($structured)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-2">
                <div>
                    <flux:input wire:model.debounce.500ms="structured.salutation" :label="__('Anrede')" type="text" />
                </div>
                <div>
                    <flux:input wire:model.debounce.500ms="structured.title" :label="__('Titel')" type="text" />
                </div>
                <div>
                    <flux:select wire:model="structured.gender" :placeholder="__('Geschlecht')" :label="__('Geschlecht')">
                        <flux:select.option value="male">Mann</flux:select.option>
                        <flux:select.option value="female">Frau</flux:select.option>
                    </flux:select>
                </div>
            </div>
            <div class="space-y-2">
                <div>
                    <flux:input wire:model.debounce.500ms="structured.firstname" :label="__('Vorname')" type="text" />
                </div>
                <div>
                    <flux:input wire:model.live.debounce.500ms="structured.lastname" :label="__('Nachname')"
                        type="text" />
                </div>
                <div>
                    <flux:select wire:model="structured.language" :placeholder="__('Sprache')" :label="__('Sprache')">
                        <flux:select.option value="DE">Deutsch</flux:select.option>
                        <flux:select.option value="EN">Englisch</flux:select.option>
                        <flux:select.option value="ES">Spanisch</flux:select.option>
                        <flux:select.option value="IT">Italienisch</flux:select.option>
                        <flux:select.option value="FR">Französisch</flux:select.option>
                    </flux:select>
                </div>
            </div>
        </div>
        <flux:separator />
        <flux:textarea wire:model="letterSalutation" :label="__('Briefanrede')" rows="auto" />
        <x-button icon="circle-stack" wire:click="save" type="button">Speichern</x-button>
        @if (session('status'))
            <flux:callout variant="secondary" icon="information-circle" heading="{{ session('status') }}" />
        @endif
    @endisset
</div>

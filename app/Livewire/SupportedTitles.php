<?php

namespace App\Livewire;

use App\Models\SupportedTitle;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;

class SupportedTitles extends Component
{
    protected function rules(): array
    {
        $defaultTitles = Config::get('languages.defaultSupportedTitles');

        return [
            'newTitle' => [
                'required',
                'min:2',
                'max:32',
                Rule::unique('supported_titles', 'title')
                    ->where(fn ($query) => $query->where('user_id', Auth::id())),
                Rule::notIn($defaultTitles),
            ],
        ];
    }

    protected function messages(): array
    {
        return [
            'newTitle.required' => 'Bitte geben Sie einen Titel ein.',
            'newTitle.min' => 'Der Titel muss mindestens :min Zeichen lang sein.',
            'newTitle.max' => 'Der Titel darf nicht länger als :max Zeichen sein.',
            'newTitle.unique' => 'Dieser Titel wurde bereits hinzugefügt.',
            'newTitle.not_in' => 'Dieser Titel ist bereits standardmäßig hinterlegt.',
        ];
    }

    public string $newTitle;

    #[Computed]
    public function titles()
    {
        return Auth::user()->supportedTitles;
    }

    public function updatedNewTitle(string $value): void
    {
        // lowercase
        $value = Str::lower($value);

        // strip any trailing dots
        if (Str::endsWith($value, '.')) {
            $value = rtrim($value, '.');
        }

        $this->newTitle = $value;
    }

    public function saveTitle()
    {
        $this->validate();

        Auth::user()->supportedTitles()->create(['title' => $this->pull('newTitle')]);
        Flux::modal('new-title')->close();
    }

    public function delete(SupportedTitle $title)
    {
        if (Auth::user()->id === $title->user_id) {
            $title->delete();
        }
    }
}

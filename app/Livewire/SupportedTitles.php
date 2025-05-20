<?php

namespace App\Livewire;

use App\Models\SupportedTitle;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;

class SupportedTitles extends Component
{
    protected function rules(): array
    {
        return [
            'newTitle' => [
                'required',
                'min:2',
                'max:32',
                Rule::unique('supported_titles', 'title')
                    ->where(fn ($query) => $query->where('user_id', Auth::id())),
            ],
        ];
    }

    public string $newTitle;

    #[Computed]
    public function titles()
    {
        return Auth::user()->supportedTitles;
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

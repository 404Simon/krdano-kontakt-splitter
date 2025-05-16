<?php

namespace App\Livewire;

use App\Models\SavedInput;
use Illuminate\Support\Collection;
use Livewire\Component;

class SavedInputs extends Component
{
    public Collection $savedInputs;

    public function mount()
    {
        $this->savedInputs = auth()->user()->savedInputs;
    }

    public function delete(SavedInput $input)
    {
        if ($input->user_id === auth()->user()->id) {
            $input->delete();
            $this->savedInputs = auth()->user()->savedInputs;
        }
    }
}

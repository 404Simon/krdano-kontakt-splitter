<?php

namespace App\Livewire;

use App\Models\SavedInput;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class SavedInputs extends Component
{
    #[Computed]
    public function savedInputs()
    {
        return Auth::user()->savedInputs;
    }

    public function delete(SavedInput $input)
    {
        if ($input->user_id === Auth::user()->id) {
            $input->delete();
        }
    }
}

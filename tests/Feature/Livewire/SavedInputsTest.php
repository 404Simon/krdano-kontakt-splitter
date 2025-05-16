<?php

namespace Tests\Feature\Livewire;

use App\Livewire\SavedInputs;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class SavedInputsTest extends TestCase
{
    public function test_renders_successfully()
    {
        Livewire::test(SavedInputs::class)
            ->assertStatus(200);
    }
}

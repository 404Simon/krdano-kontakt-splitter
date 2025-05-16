<?php

namespace Tests\Feature\Livewire;

use App\Livewire\KontaktSplitter;
use Livewire\Livewire;
use Tests\TestCase;

class KontaktSplitterTest extends TestCase
{
    public function test_renders_successfully()
    {
        Livewire::test(KontaktSplitter::class)
            ->assertStatus(200);
    }
}

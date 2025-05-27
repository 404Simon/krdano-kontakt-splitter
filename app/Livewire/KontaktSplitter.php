<?php

namespace App\Livewire;

use App\Services\KontaktParserService;
use App\Services\LetterSalutationService;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class KontaktSplitter extends Component
{
    public string $unstructured = '';

    public string $letterSalutation = '';

    public ?array $structured = null;

    protected array $rules = [
        'unstructured' => [
            'max:300',
        ],
    ];

    public function save(): void
    {
        $this->validate([
            'letterSalutation' => ['required', 'string', 'min:5', 'max:192'],
            'structured.salutation' => ['nullable', 'string', 'max:16'],
            'structured.firstname' => ['nullable', 'string', 'max:128'],
            'structured.lastname' => ['nullable', 'string', 'max:128'],
            'structured.title' => ['nullable', 'string', 'max:64'],
            'structured.gender' => ['nullable', 'string', 'in:male,female'],
            'structured.language' => ['nullable', 'string', 'in:DE,FR,IT,ES,EN'],
        ], [], [
            'letterSalutation' => 'Letter Salutation',
        ]);
        Auth::user()
            ->savedInputs()
            ->create(Arr::add($this->structured, 'letter_salutation', $this->letterSalutation));

        session()->flash('status', 'Briefanrede wurde gespeichert!');
    }

    public function generateLetterSalutation()
    {
        $this->letterSalutation = LetterSalutationService::generate($this->structured);
    }

    public function updatedStructured($value, $key)
    {
        if ($this->structured) {
            if ($key === 'gender') {
                if (isset($this->structured['language'])) {
                    $salutations = config('languages.salutation')[$this->structured['language']];
                } else {
                    $salutations = config('languages.salutation')[config('languages.default_language')];
                }
                $this->structured['salutation'] = $salutations[$value];
            }
            $this->generateLetterSalutation();
        }
    }

    public function updatedUnstructured(KontaktParserService $parserService)
    {
        $this->validateOnly('unstructured');
        if (! $this->unstructured) {
            $this->structured = null;

            return;
        }
        try {
            $this->structured = $parserService->extractDetails($this->unstructured);
            $this->generateLetterSalutation();
        } catch (Exception $th) {
            $this->structured = null;
        }
    }
}

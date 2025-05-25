<?php

namespace App\Services\Extractors;

use Closure;
use GenderDetector\Gender;
use GenderDetector\GenderDetector;

class GenderExtractor
{
    public function __invoke(array $data, Closure $next): array
    {
        // If gender is already set, skip detection
        if (filled($data['gender'] ?? null)) {
            return $next($data);
        }

        // Use firstname, or fallback to lastname
        $name = $data['firstname'] ?? $data['lastname'] ?? null;

        $data['gender'] = $this->detectGender($name);

        return $next($data);
    }

    protected function detectGender(?string $name): string
    {
        // Default to male
        if (blank($name)) {
            return 'male';
        }

        $detected = app(GenderDetector::class)->getGender($name);

        return in_array($detected, [Gender::Female, Gender::MostlyFemale], true)
            ? 'female'
            : 'male';
    }
}

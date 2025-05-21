<?php

namespace App\Services\Extractors;

use Closure;
use GenderDetector\Gender;
use GenderDetector\GenderDetector;

class GenderExtractor
{
    public function __invoke(array $data, Closure $next): array
    {
        // If gender is already set from salutation, keep it
        if ($data['gender'] !== null) {
            return $next($data);
        }

        // If we have a first name, use gender detector
        if ($data['firstname']) {
            $detectedGender = (app(GenderDetector::class))->getGender($data['firstname']);
            if (in_array($detectedGender, [Gender::Female, Gender::MostlyFemale])) {
                $data['gender'] = 'female';
            } else {
                $data['gender'] = 'male';
            }
        } else {
            // Default to male if no first name
            $data['gender'] = 'male';
        }

        return $next($data);
    }
}

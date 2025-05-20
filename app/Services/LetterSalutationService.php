<?php

namespace App\Services;

class LetterSalutationService
{
    /*
    * Generates the letter salutation based on the structured data
    */
    public static function generate(array $structured): string
    {
        $languageGreeting = config('languages.greetings');
        $defaultLanguage = config('languages.default_language');
        if ($structured) {
            if (isset($structured['language']) and isset($languageGreeting[$structured['language']])) {
                $allGreetings = $languageGreeting[$structured['language']];
            } else {
                $allGreetings = $languageGreeting[$defaultLanguage];
            }
            if (isset($structured['gender'])) {
                if ($structured['gender'] == 'male') {
                    $greeting = $allGreetings[0];
                } else {
                    $greeting = $allGreetings[1];
                }
            } else {
                $greeting = $allGreetings[2];
            }
            if (isset($structured['salutation'])) {
                if ($greeting != '') {
                    $greeting = $greeting.' '.$structured['salutation'];
                } else {
                    $greeting = $structured['salutation'];
                }
            }
            if (isset($structured['title'])) {
                $greeting = $greeting.' '.$structured['title'];
            }
            if (isset($structured['lastname'])) {
                $greeting = $greeting.' '.$structured['lastname'];
            }

            return $greeting;
        }

        return $languageGreeting[$defaultLanguage][2];
    }
}

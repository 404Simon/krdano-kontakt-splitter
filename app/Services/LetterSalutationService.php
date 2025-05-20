<?php

namespace App\Services;

class LetterSalutationService
{
    public static array $languageGreeting = [
        'DE' => ['Sehr geehrter', 'Sehr geehrte', 'Sehr geehrte Damen und Herren'],
        'EN' => ['Dear', 'Dear', 'Dear Sirs'],
        'IT' => ['Egregio', 'Gentile', 'Egregi Signori'],
        'FR' => ['', '', 'Messiersdames'],
        'ES' => ['Estimado', 'Estimada', 'Estimados Señores y Señoras'],
    ];

    // Default country is germany
    public static string $defaultLanguage = 'DE';

    /*
    * Generates the letter salutation based on the structured data
    */
    public static function generate(array $structured): string
    {
        if ($structured) {
            if (isset($structured['language']) and isset(self::$languageGreeting[$structured['language']])) {
                $allGreetings = self::$languageGreeting[$structured['language']];
            } else {
                $allGreetings = self::$languageGreeting[self::$defaultLanguage];
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

        return self::$languageGreeting[self::$defaultLanguage][2];
    }
}

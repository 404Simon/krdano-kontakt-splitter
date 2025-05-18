<?php

namespace App\Services;

class LetterSalutationService
{
    /*
    * Generates the letter salutation based on the structured data when lastname and salutation are set
    */
    public static function generate(array $structured): string
    {
        if ($structured and isset($structured['lastname']) and isset($structured['salutation'])) {
            if (isset($structured['language'])) {
                if ($structured['language'] === 'DE') {
                    if (isset($structured['gender'])) {
                        if ($structured['gender'] == 'male') {
                            $greeting = 'Sehr geehrter';
                        } else {
                            $greeting = 'Sehr geehrte';
                        }
                    } else {
                        $greeting = 'Sehr geehrte Damen und Herren';
                    }
                } elseif ($structured['language'] === 'EN') {
                    if (isset($structured['gender'])) {
                        if ($structured['gender'] == 'male') {
                            $greeting = 'Dear Mr.';
                        } else {
                            $greeting = 'Dear Mrs.';
                        }
                    } else {
                        $greeting = 'Dear Sirs';
                    }
                } elseif ($structured['language'] === 'IT') {
                    if (isset($structured['gender'])) {
                        if ($structured['gender'] == 'male') {
                            $greeting = 'Egregio Signor';
                        } else {
                            $greeting = 'Gentile Signora';
                        }
                    } else {
                        $greeting = 'Egregi Signori';
                    }
                } elseif ($structured['language'] === 'FR') {
                    if (isset($structured['gender'])) {
                        if ($structured['gender'] == 'male') {
                            $greeting = 'Monsieur';
                        } else {
                            $greeting = 'Madame';
                        }
                    } else {
                        $greeting = 'Messiersdames';
                    }
                } elseif ($structured['language'] === 'ES') {
                    if (isset($structured['gender'])) {
                        if ($structured['gender'] == 'male') {
                            $greeting = 'Estimado';
                        } else {
                            $greeting = 'Estimada';
                        }
                    } else {
                        $greeting = 'Estimados Señores y Señoras';
                    }
                }
            } else {
                // Default country is germany
                if (isset($structured['gender'])) {
                    if ($structured['gender'] == 'male') {
                        $greeting = 'Sehr geehrter';
                    } else {
                        $greeting = 'Sehr geehrte';
                    }
                } else {
                    $greeting = 'Sehr geehrte Damen und Herren';
                }

            }
            if (isset($structured['title'])) {
                return "$greeting ".$structured['salutation'].' '.$structured['title'].' '.$structured['lastname'];
            } else {
                return "$greeting ".$structured['salutation'].' '.$structured['lastname'];
            }
        }

        return 'Sehr geehrte Damen und Herren';
    }
}

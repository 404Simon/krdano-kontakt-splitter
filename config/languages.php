<?php

return [

    // Greeting depending on the language
    'greetings' => [
        'DE' => ['Sehr geehrter', 'Sehr geehrte', 'Sehr geehrte Damen und Herren'],
        'EN' => ['Dear', 'Dear', 'Dear Sirs'],
        'IT' => ['Egregio', 'Gentile', 'Egregi Signori'],
        'FR' => ['', '', 'Messiersdames'],
        'ES' => ['Estimado', 'Estimada', 'Estimados Señores y Señoras'],
    ],

    // Salutation depending on the language
    'salutation' => [
        'DE' => ['male' => 'Herr', 'female' => 'Frau'],
        'EN' => ['male' => 'Mr.', 'female' => 'Mrs.'],
        'IT' => ['male' => 'Signor', 'female' => 'Signora'],
        'FR' => ['male' => 'Monsieur', 'female' => 'Madame'],
        'ES' => ['male' => 'Señor', 'female' => 'Señora'],
    ],

    // Default language
    'default_language' => 'DE',

];

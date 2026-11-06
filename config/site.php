<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Business details
    |--------------------------------------------------------------------------
    |
    | Contact details shown in the utility bar, header, footer and contact
    | blocks. Override them per environment from the .env file.
    |
    */

    'name' => env('SITE_NAME', 'Opportunity Consultant'),
    'tagline' => 'Study Abroad & Internship Consultant',

    'phone' => env('SITE_PHONE', '+919876543210'),
    'phone_display' => env('SITE_PHONE_DISPLAY', '+91 98765 43210'),
    'whatsapp' => env('SITE_WHATSAPP', '919876543210'),
    'email' => env('SITE_EMAIL', 'hello@opportunityconsultant.in'),

    'address' => 'Office 204, Sai Arcade, Station Road, Virar West, Palghar 401303',
    'address_short' => 'Sai Arcade · Virar West',
    'hours' => 'Mon–Sat, 10:00 am – 7:00 pm',
    'directions_url' => env('SITE_DIRECTIONS_URL', 'https://maps.google.com/?q=Sai+Arcade+Station+Road+Virar+West'),

    'socials' => [
        'Instagram' => env('SITE_INSTAGRAM', '#'),
        'YouTube' => env('SITE_YOUTUBE', '#'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Free counselling form options
    |--------------------------------------------------------------------------
    |
    | Shared by the multi-step eligibility form and its validation rules.
    |
    */

    'counselling' => [

        'levels' => [
            'ug' => ['label' => "Bachelor's degree", 'sub' => 'After Class 12', 'short' => "Bachelor's", 'phrase' => "bachelor's"],
            'pg' => ['label' => "Master's degree", 'sub' => 'After graduation', 'short' => "Master's", 'phrase' => "master's"],
            'dip' => ['label' => 'Diploma or certificate', 'sub' => 'Shorter, career-focused', 'short' => 'Diploma', 'phrase' => 'diploma'],
            'phd' => ['label' => 'PhD or research', 'sub' => "After a master's", 'short' => 'PhD', 'phrase' => 'PhD'],
            'int' => ['label' => 'Internship abroad', 'sub' => 'Real work experience overseas', 'short' => 'Internship', 'phrase' => 'internship'],
            'ns' => ['label' => 'Not sure yet', 'sub' => 'Help me decide', 'short' => 'Undecided', 'phrase' => 'study and internship'],
        ],

        'countries' => [
            'GB' => ['code' => 'GB', 'label' => 'United Kingdom'],
            'US' => ['code' => 'US', 'label' => 'USA'],
            'CA' => ['code' => 'CA', 'label' => 'Canada'],
            'AU' => ['code' => 'AU', 'label' => 'Australia'],
            'DE' => ['code' => 'DE', 'label' => 'Germany'],
            'IE' => ['code' => 'IE', 'label' => 'Ireland'],
            'AE' => ['code' => 'AE', 'label' => 'Dubai'],
            'MU' => ['code' => 'MU', 'label' => 'Mauritius'],
            'XX' => ['code' => '???', 'label' => 'Not sure yet'],
        ],

        'intakes' => [
            '6m' => ['label' => 'Within 6 months', 'sub' => 'The very next intake', 'short' => '< 6 months'],
            '12m' => ['label' => 'In 6–12 months', 'sub' => 'The ideal time to start', 'short' => '6–12 months'],
            '1y' => ['label' => 'In a year or more', 'sub' => 'Planning ahead', 'short' => '12+ months'],
            'exp' => ['label' => 'Just exploring', 'sub' => 'Still deciding', 'short' => 'Exploring'],
        ],

        'interests' => [
            'study' => 'Studying abroad',
            'internship' => 'An internship abroad',
            'both' => 'Both',
            'unsure' => 'Not sure yet',
        ],

    ],

];

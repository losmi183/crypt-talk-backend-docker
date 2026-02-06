<?php

return [
    /*
    |--------------------------------------------------------------------------
    | PBKDF2 Iterations
    |--------------------------------------------------------------------------
    |
    | Broj iteracija koji se koristi za derivaciju AES ključa iz lozinke.
    | Može se promeniti u .env fajlu.
    |
    */
    'pbkdf2_iterations' => env('PBKDF2_ITERATIONS', 100000),
];
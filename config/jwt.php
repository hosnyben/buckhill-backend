<?php

return [
    'issuer' => env('JWT_ISSUER', config('app.url')),
    'audience' => env('JWT_AUDIENCE', config('app.url')),
];

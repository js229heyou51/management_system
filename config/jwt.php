<?php
return [
    'secret' => env('JWT_SECRET', 'default-secret-change-it'),
    'algo'   => env('JWT_ALGO', 'HS256'),
    'expire' => env('JWT_EXPIRE', 7200),
];
<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\FortifyServiceProvider::class,
    App\Providers\HorizonServiceProvider::class,
    App\Providers\JetstreamServiceProvider::class,
    Tymon\JWTAuth\Providers\LaravelServiceProvider::class,
    OwenIt\Auditing\AuditingServiceProvider::class,
];

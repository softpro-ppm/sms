<?php

$providers = [
    App\Providers\AppServiceProvider::class,
    App\Providers\ViewComposerServiceProvider::class,
];

if (class_exists(\Laravel\Pail\PailServiceProvider::class)) {
    $providers[] = \Laravel\Pail\PailServiceProvider::class;
}

if (class_exists(\Laravel\Sail\SailServiceProvider::class)) {
    $providers[] = \Laravel\Sail\SailServiceProvider::class;
}

return $providers;

<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\RouteServiceProvider::class,

    // ✅ Add this for SweetAlert
    RealRashid\SweetAlert\SweetAlertServiceProvider::class,
];

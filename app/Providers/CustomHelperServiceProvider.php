<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class CustomHelperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        require_once app_path('Helpers/utils.php');
    }
}

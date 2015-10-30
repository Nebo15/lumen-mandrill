<?php
/**
 * Author: Paul Bardack paul.bardack@gmail.com http://paulbardack.com
 * Date: 30.10.15
 * Time: 16:05
 */

namespace App;

use App\Mandrill;
use Laravel\Lumen\Application;
use Illuminate\Support\ServiceProvider;

class MandrillServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('\App\Mandrill', function (Application $app) {
            # ToDo: throw exception whe MANDRILL_KEY is not set
            $key = env('MANDRILL_KEY', false);

            return new Mandrill(
                new \Drunken\Manager($app->make('db')->connection()->getMongoDB()),
                $app->make('log'),
                $key);
        });
    }
}

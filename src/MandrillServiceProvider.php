<?php
/**
 * Author: Paul Bardack paul.bardack@gmail.com http://paulbardack.com
 * Date: 30.10.15
 * Time: 16:05
 */

namespace App;

use Laravel\Lumen\Application;
use Illuminate\Support\ServiceProvider;

class MandrillServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('mandrill', function (Application $app) {
            $key = env('MANDRILL_KEY', false);
            if(!$key){
                throw new LumenMandrillException('set mandrill key for LumenMandrill');
            }

            return new Mandrill(
                new \Drunken\Manager($app->make('db')->connection()->getMongoDB()),
                $app->make('log'),
                $key);
        });
    }
}

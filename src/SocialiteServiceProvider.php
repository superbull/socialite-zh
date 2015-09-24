<?php
namespace Superbull\Socialite;

use Laravel\Socialite\SocialiteServiceProvider as LaravelSocialiteServiceProvider;

class SocialiteServiceProvider extends LaravelSocialiteServiceProvider
{
    public function register()
    {
        $this->app->bindShared('Laravel\Socialite\Contracts\Factory', function ($app) {
            return new SocialiteManager($app);
        });
    }
}
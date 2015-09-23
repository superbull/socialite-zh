<?php
namespace Superbull\Socialite;
use InvalidArgumentException;
use Laravel\Socialite\SocialiteManager as LaravelSocialiteManager;

class SocialiteManager extends LaravelSocialiteManager
{
    /**
     * Create an instance of the specified driver.
     *
     * @return \Laravel\Socialite\Two\AbstractProvider
     */
    protected function createWeiboDriver()
    {
        $config = $this->app['config']['services.weibo'];
        
        return $this->buildProvider(
            'Superbull\Socialite\Two\WeiboProvider', $config
        );
    }
}
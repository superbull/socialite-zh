<?php
namespace Superbull\Socialite\Two;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;
use Exception;

class WeiboProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('https://api.weibo.com/oauth2/authorize', $state);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return 'https://api.weibo.com/oauth2/access_token';
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $uid = $this->getUid($token);
        $userUrl = 'https://api.weibo.com/2/users/show.json';
        $response = $this->getHttpClient()->get($userUrl, ['query'=>[
            'access_token'=>$token,
            'uid'=>$uid,
        ]]);
        $user = json_decode($response->getBody(), true);
        if (in_array('email', $this->scopes) || in_array('all', $this->scopes)) {
            $user['email'] = $this->getEmailByToken($token);
        }
        return $user;
    }

    /**
     * Get the uid for the given access token.
     *
     * @param  string  $token
     * @return integer
     */
    protected function getUid($token)
    {
        $uidUrl = 'https://api.weibo.com/2/account/get_uid.json';
        $response = $this->getHttpClient()->get($uidUrl, ['query' => [
            'access_token'=>$token
        ]]);

        return (int) json_decode($response->getBody(), true)['uid'];
    }

    /**
     * Get the email for the given access token.
     *
     * @param  string  $token
     * @return string|null
     */
    protected function getEmailByToken($token)
    {
        $emailsUrl = 'https://api.weibo.com/2/account/profile/email.json';
        try {
            $response = $this->getHttpClient()->get($emailsUrl, ['query' => [
                'access_token'=>$token
            ]]);
        } catch (Exception $e) {
            return;
        }
        foreach (json_decode($response->getBody(), true) as $email) {
            return $email['email'];
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User)->setRaw($user)->map([
            'id'        => $user['idstr'], 
            'nickname'  => $user['screen_name'], 
            'name'      => $user['name'],
            'email'     => isset($user['email']) ? $user['email'] : null, 
            'avatar'    => $user['avatar_large'],
        ]);
    }

}
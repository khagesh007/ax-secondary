<?php

namespace App\Traits;

use App\Models\UserOAuthToken;
use Google\Client;

trait OAuthTrait
{

    const OAUTH2_TOKEN_URI = 'https://accounts.google.com/o/oauth2/token';

    public function getGoogleAccessToken($code)
    {
        $client = new Client();
        $client->setClientId(config("services.google.client_id"));
        $client->setClientSecret(config("services.google.client_secret"));
        $client->setRedirectUri(config("services.google.redirect"));
        return $client->fetchAccessTokenWithAuthCode($code);
    }

    public function regenerateGoogleAccessToken($accessToken)
    {
        $client = new Client();
        $client->setClientId(config("services.google.client_id"));
        $client->setClientSecret(config("services.google.client_secret"));
        $client->setAccessToken($accessToken);
        return $client->refreshToken($accessToken["refresh_token"]);
    }

    public function checkIfGoogleAccessTokenExipred($accessToken)
    {
        $client = new Client();
        $client->setClientId(config("services.google.client_id"));
        $client->setClientSecret(config("services.google.client_secret"));
        $client->setAccessToken($accessToken);
        return $client->isAccessTokenExpired();
    }

    public function updateUserOAuthToken($accessToken, $userId, $type = "google")
    {
        UserOAuthToken::updateOrCreate(
            ["user_id" => $userId, "type" => $type],
            ["token" => $accessToken]
        );
    }
}

<?php

include "config/credentials.php";
require "lib/apiclient/Google_Client.php";

class Client {
    
    /**
     * Create a Google client
     */

    static function Google() {
    
        $client = new Google_Client();
        $client->setClientId(CLIENT_ID);
        $client->setClientSecret(CLIENT_SECRET);
        $client->setRedirectUri(REDIRECT_URI);
        $client->setScopes(array(
          'https://www.googleapis.com/auth/drive',
          'https://www.googleapis.com/auth/userinfo.email',
          'https://www.googleapis.com/auth/userinfo.profile'));
        $client->setUseObjects(true);
    
        // if there is an existing session, set the access token
        if ($tokens = User::get('tokens')) {
            $client->setAccessToken($tokens);
        }
    
        return $client;
    }
}
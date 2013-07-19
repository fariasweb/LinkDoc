<?php

/**
 * Class Service 
 * 
 * 
 */

require 'lib/apiclient/contrib/Google_DriveService.php';
require 'lib/apiclient/contrib/Google_Oauth2Service.php';

class Service {

    /**
     * Return a new DriveService with the Google client
     */
     
    static function GoogleDrive(Google_Client &$client) {
        
        return new Google_DriveService($client);
    }
    
    /**
     * Return a new OAuth2Service for the Google Client
     */
     
     static function GoogleOauth2(Google_Client &$client) {
         
         return new Google_Oauth2Service($client);
     }

}
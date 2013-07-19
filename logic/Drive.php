<?php

/**
 * Class Drive
 * 
 * Logic to work with Google Drive
 */
 
require 'helpers/User.php';
require 'helpers/Client.php';
require 'helpers/Service.php';
 
 class Drive {
     
     private $client;
     
     function __construct() {
         $this->client = Client::Google();
     }
     
     // ----------------------------------------------------------------------------
     // Client funtions
     
     function authenticateClient() {
         $this->client->authenticate();
     }
     
     function createAuthUrl() {
         return $this->client->createAuthUrl();
     }
     
     // ----------------------------------------------------------------------------
     // Files funtions
     
     //TODO
     function getFile($file_id) {
         
         $file = Service::GoogleDrive($this->client)->files->get($file_id);
         
         $request = new Google_HttpRequest($file->downloadUrl);
         $response = $this->client->getIo()->authenticatedRequest($request);
         
         $file->content = $response->getResponseBody();
         
         //Parsear los datos para igualizar??
         
         return $file;
     }
 }

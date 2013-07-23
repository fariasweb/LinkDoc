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
     
     function getAccessToken() {
         return $this->client->getAccessToken();
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
     
     function setFile($name, $url = "") {
            
        //Crear el fichero    
        $file = new Google_DriveFile();
        if ($name == "") $name = "Untitle_url_".date("n_d_y")."url";
        $file->setTitle($name);
        //$file->setDescription($inputFile->description);
        $file->setMimeType("text/url");
        //$file->setMimeType("text/plain");
        
        // Set the parent folder.
        /*if ($inputFile->parentId != null) {
          $parentsCollectionData = new Google_DriveFileParentsCollection();
          $parentsCollectionData->setId($inputFile->parentId);
          $file->setParentsCollection(array($parentsCollectionData));
        }*/
        
        $createdFile = Service::GoogleDrive($this->client)->files->insert($file, array(
          'data' => "{url:$url, date:".time()."}",
          'mimeType' => "text/url",
        ));
        
        return $createdFile;
     }
 }

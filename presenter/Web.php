<?php

/**
 * Presentación para trabajar en la web
 */

class Web {
    
    public $debug = true;
    
    
    /**
     * Renders the given message as JSON and responds with the
     * given HTTP status code.
     */
    public function renderErr($app, $statusCode, $message) {
      echo json_encode(array( "code" =>$statusCode, "message" => $message ));
      $app->halt($statusCode, $message);
    }
    
    /**
     * Renders the given object as JSON.
     */
    public function render($message) {
      
      if (!$this->debug) {
      
          header('Cache-Control: no-cache, must-revalidate');
          header('Content-type: application/json');
          echo json_encode($message);
          
      } else {
          echo "<pre>";
          var_dump($message);
          echo "</pre>";
      }
    }
    
    
}

<?php

/**
 * Presentación para trabajar en la web
 */

class Web {
    
    /**
     * Renders the given message as JSON and responds with the
     * given HTTP status code.
     */
    public function renderErr($app, $statusCode, $message) {
      echo json_encode(array( "code" =>$statusCode, "message" => $message ));
      $app->halt($statusCode);
    }
    
    /**
     * Renders the given object as JSON.
     */
    public function render($obj) {
      echo json_encode($obj);
    }
    
    
}

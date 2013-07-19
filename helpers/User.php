<?php

/**
 * Class user
 * 
 * Control login user
 */

class User {
    
    
    /**
     * get_user
     * 
     * Devuelve todo el usuario
     */
    
    static public function get_user() {
        
        if (isset($_SESSION["user"])) {
            return $_SESSION["user"];
        }
        
        return NULL;
    } 
    
    /**
     * get
     * 
     * Devuelve un valor del usuarios
     */
    
    static public function get($param) {
        
        return (isset($_SESSION['user'][$param]) and $_SESSION['user'][$param]) ? $_SESSION['user'][$param] : NULL ;
    }
    
    /**
     * set_user
     * 
     * Sets the current user in $_SESSIOn
     */
     
    static public function create_user() {
        
        if (!isset($_SESSION['user'])) {
            $_SESSION["user"] = array(
                "id"     => NULL,
                "info"   => NULL,
                "tokens" => NULL
            );
        }
    }
    
    /**
     * set_tokens
     * 
     * Sets the tokens to user
     */
     
     static public function set_tokens($tokens) {

        User::create_user();
        $_SESSION['user']['tokens'] = $tokens;
     }
        
    /**
     * delete_user
     * 
     * Deletes the user in the session.
     */
     
     static public function delete_user() {
        
        $_SESSION["user"] = NULL;
     }
     
     /**
      * logged
      * 
      * Return if the user is logged in the platform
      */
      
      static public function logged() {
          return User::get('tokens');
      }
    
}

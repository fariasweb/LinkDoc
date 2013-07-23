<?php
/**
 * LinkDoc - Organize your URL in Google Drive
 *
 * @author Francisco Javier Arias <fariasweb@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

require 'lib/Slim/Slim.php';

// ----------------------------------------------------------------------------
// Slim init - Router

\Slim\Slim::registerAutoloader();
session_start();

// create a new Slim app.
$app = new \Slim\Slim(array(
  'templates.path' => './public',
));

// ----------------------------------------------------------------------------
// Logic - Api Access

require "logic/Drive.php";

// initialize a client with application credentials and required scopes.
$logic = new Drive(); //is 'global'

// ----------------------------------------------------------------------------
// Presenter - View
require 'presenter/Web.php';

$view = new Web();

// ----------------------------------------------------------------------------
// Route - Control functions

$authenticate = function () {
    return function() {
       
        if (!User::logged()) {
            $app = \Slim\Slim::getInstance();
            $app->flash('error', 'Login required');
            $app->redirect('/');
        }
    };
};

$queryParameters = function($params = array()) use (&$app, &$view) {
    return function () use($params,&$app, &$view){
        if (count($params)) {
            foreach ($params as &$param) {
                if (!$app->request()->get($param)) {
                    $view->renderErr($app, 400, 'Required parameter missing.');
                    $is_callable = false;
                }
            }
        }
    };
};


// ----------------------------------------------------------------------------
// Route - Routes

/**
 * Open a document
 * TODO: No tiene vista, solo abre y redireciona
 */
$app->get('/open', $authenticate(), 
//$queryParameters(array('file_id')), 
function() use ($app, $logic, $view) {

  $fileId = $app->request()->get('file_id');
  $fileId = "0B9eflbZYTz_JU2t3SDhTOWFrRlU"; //Test
  //$fileId = "0B9eflbZYTz_Jc0hQbnJ4QlFuajg"; //Test II
  
  try {
    
    $view->render($logic->getFile($fileId));
    
  } catch (Exception $ex) {
    //TODO: Gestion de error
    echo "MAL ".$ex->getMessage();
  }
});

/**
 * Gets the metadata and contents for the given file_id.
 */
$app->get('/doc', $authenticate(), function() use ($app, $logic, $view) {
    
  //Probar a guardar un docuemnto
  $file = $logic->setFile("Test_".time().".url", "www.google.com");
  
  $view->render($file);
  /*checkUserAuthentication($app);
  checkRequiredQueryParams($app, array('file_id'));
  $fileId = $app->request()->get('file_id');
  try {
    // Retrieve metadata for the file specified by $fileId.
    $file = $service->files->get($fileId);

    // Get the contents of the file.
    $request = new Google_HttpRequest($file->downloadUrl);
    $response = $client->getIo()->authenticatedRequest($request);
    $file->content = $response->getResponseBody();

    renderJson($app, $file);
  } catch (Exception $ex) {
    renderEx($app, $ex);
  }*/
});

/**
 * Creates a new file with the metadata and contents
 * in the request body. Requires login.
 * TODO: Tiene una vista especial muy muy reducida
 */
$app->post('/doc', function() use ($app, $logic) {
    
  /*checkUserAuthentication($app);
  $inputFile = json_decode($app->request()->getBody());
  try {
    $file = new Google_DriveFile();
    $file->setTitle($inputFile->title);
    $file->setDescription($inputFile->description);
    $file->setMimeType($mimeType);
    // Set the parent folder.
    if ($inputFile->parentId != null) {
      $parentsCollectionData = new Google_DriveFileParentsCollection();
      $parentsCollectionData->setId($inputFile->parentId);
      $file->setParentsCollection(array($parentsCollectionData));
    }
    $createdFile = $service->files->insert($file, array(
      'data' => $inputFile->content,
      'mimeType' => $mimeType,
    ));
    renderJson($app, $createdFile->id);
  } catch (Exception $ex) {
    renderEx($app, $ex);
  }*/
});

/**
 * Modifies an existing file given in the request body and responds
 * with the file id. Requires login.
 */
 
$app->put('/doc', function() use ($app, $logic) {
  checkUserAuthentication($app);
  $inputFile = json_decode($app->request()->getBody());
  $fileId = $inputFile->id;
  try {
    // Retrieve metadata for the file specified by $fileId and modify it with
    // the new changes.
    $file = $service->files->get($fileId);
    $file->setTitle($inputFile->title);
    $file->setDescription($inputFile->description);
    $file->getLabels()->setStarred($inputFile->labels->starred);

    // Update the existing file.
    $output = $service->files->update(
      $fileId, $file, array('data' => $inputFile->content)
    );

    renderJson($app, $output->id);
  } catch (Exception $ex) {
    renderEx($app, $ex);
  }
});
 
/**
 * Gets user profile. Requires login.
 */
/*$app->get('/user', function() use ($app, $client, $service) {
  checkUserAuthentication($app);
  $userinfoService = new Google_Oauth2Service($client);
  try {
    $user = $userinfoService->userinfo->get();
    renderJson($app, $user);
  } catch (Exception $ex) {
    renderEx($app, $ex);
  }
});

/**
 * Gets the information about the current user along with Drive API settings.
 * Requires login.
 */
/*$app->get('/about', function() use ($app, $client, $user, $service) {
    
  if ($code = $app->request()->get('code')) {
    // handle code, retrieve credentials.
    $client->authenticate();
    $tokens = $client->getAccessToken();
    set_user($tokens);
  }  
    
  if (!$user) {
      $app->redirect($client->createAuthUrl());
  } else { 
      try {
        $about = $service->about->get();
        renderJson($app, $about);
      } catch (Exception $ex) {
        renderEx($app, $ex);
      }
  }    
  
});

/**
 * The start page, also handles the OAuth2 callback.
 * Tiene una vista personalizada con todas las funciones posibles 
 */
$app->get('/', function() use ($app, $logic) {
    
    //Existe el parametro GET 'code' -> OAuth2 callback
    if ($code = $app->request()->get('code')) {
        //Conseguimos los tokens de acceso y guardamos en la session
        $logic->authenticateClient();
        User::set_tokens($logic->getAccessToken());
        $app->redirect('/'); //Para eliminar la cabezera y las variables GET
    }

    if (User::logged()) {
        //Tenemos acceso
        
        /*echo "<pre>";
        var_dump(json_decode(User::get('tokens')));
        echo "</pre>";*/     
        
        /*$userinfoService = Service::GoogleOauth2($client);      
        try {
            $user = $userinfoService->userinfo->get();
            echo "<pre>";
            var_dump($user);
            echo "</pre>";
            
        } catch (Exception $ex) {
            echo "ERROR";
            var_dump($ex->getMessage());
        }*/
        
        /*echo "<pre>";
        var_dump($logic->getFile("0B9eflbZYTz_JU2t3SDhTOWFrRlU"));
        echo "</pre>";*/
        
        $app->render('index.html');
        
    } else {
        //No logeado
        //TODO: pagina bonita vende motos
        $app->redirect($logic->createAuthUrl());
    }

  
});

$app->run();

<?php 
/**
 * This is a Anax pagecontroller.
 *
 */

include "config.php";
    $app->theme->addStylesheet('css/sticky-footer.css');
    $app->theme->addStylesheet('css/override.css');
    
$di->setShared('db', function() {
    $db = new \Mos\Database\CDatabaseBasic();
    $db->setOptions(require NAVIGARE_INSTALL_PATH . 'app/config/database_mysql.php');
    $db->connect();
    return $db;
}); 

$di->set('UsersController', function() use ($di) {
    $controller = new \TBJ\Navigare\Boats\Users\UsersController();
    $controller->setDI($di);
    return $controller;
});

$di->set('QuestionsController', function() use ($di) {
    $controller = new \TBJ\Navigare\Boats\Questions\QuestionsController();
    $controller->setDI($di);
    return $controller;
});    
$di->set('CommentsController', function() use ($di) {
    $controller = new \TBJ\Navigare\Boats\Comments\CommentsController();
    $controller->setDI($di);
    return $controller;
});

$di->set('TagsController', function() use ($di) {
    $controller = new \TBJ\Navigare\Boats\Tags\TagsController();
    $controller->setDI($di);
    return $controller;
});
$di->set('LoginController', function() use ($di) {
    $controller = new \TBJ\Navigare\Login\LoginController();
    $controller->setDI($di);
    return $controller;
});
$di->set('AnswersController', function() use ($di) {
    $controller = new \TBJ\Navigare\Boats\Answers\AnswersController();
    $controller->setDI($di);
    return $controller;
});
$di->set('InitdatabaseController', function() use ($di) {
    $controller = new \TBJ\Navigare\Boats\Initdatabase\InitdatabaseController();
    $controller->setDI($di);
    return $controller;
});


 
$app->router->add('', function() use ($app) {

    $latest_questions = $app->dispatcher->forward([
        'controller' => 'questions',
        'action'     => 'getlatest',       
    ]);
    
    $popular_tags = $app->dispatcher->forward([
        'controller' => 'tags',
        'action'     => 'popular',       
    ]);

    $active_users = $app->dispatcher->forward([
        'controller' => 'users',
        'action'     => 'mostactive',       
    ]);    

    $app->theme->addStylesheet('css/override.css'); 
    $app->theme->setTitle("Start");       
    $app->views->add('boats/start_page', [
        'latest_questions' => $latest_questions,  
        'popular_tags' => $popular_tags, 
        'active_users' => $active_users, 
    ]);      
     
   
});

$app->router->add('questions', function() use ($app) {
     $app->theme->setTitle("Frågor");  

    $app->dispatcher->forward([
        'controller' => 'questions',
        'action'     => 'list',       
    ]);


});

$app->router->add('tags', function() use ($app) {
    $app->theme->setTitle("Ämnen");  
    $app->theme->addStylesheet('css/override.css'); 
    $app->dispatcher->forward([
        'controller' => 'tags',
        'action'     => 'list',       
    ]);

  
});

$app->router->add('users', function() use ($app) {
     $app->theme->setTitle("Användare");  
   
    $app->dispatcher->forward([
        'controller' => 'users',
        'action'     => 'list',       
    ]);
 
});

$app->router->add('about', function() use ($app) {
     $app->theme->setTitle("Om oss");  
     $app->theme->addStylesheet('css/start.css'); 
     $content = $app->fileContent->get('about.html');          
     $app->views->add('boats/index', [        
        'content' => $content,
    ]);
     
/*    $app->dispatcher->forward([
        'controller' => 'questions',
        'action'     => 'view',       
    ]);

*/   
});



$app->router->add('source', function() use ($app) { 
    $app->theme->addStylesheet('css/source.css');
    $app->theme->setTitle("Källkod"); 
    $source = new \Mos\Source\CSource([
        'secure_dir' => '..', 
        'base_dir' => '..', 
        'add_ignore' => ['.htaccess'],
    ]);
 
    $app->views->add('boats/source', [
        'content' => $source->View(),
    ]);
    

 
});

$app->router->add('setup', function() use ($app) {    
    $app->theme->setTitle("Navigare Necesse Est");     
    $params=array();
    $params[] = require NAVIGARE_INSTALL_PATH . 'app/config/database_mysql.php';
    $app->dispatcher->forward([
        'controller' => 'initdatabase',
        'action'    => 'init',        
        'params'    => $params,
    ]);
            
        
    $url = $app->url->create('');
    $app->response->redirect($url);    

}); 
 
$app->router->handle();
$app->theme->render();

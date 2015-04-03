<?php
namespace TBJ\Navigare\Login;
 
/**
 * A controller for login events.
 *
 */
class LoginController implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;

    protected $form;
    protected $users;
    protected $userid;
    protected $useremail;
    /**
     * Constructor
     *
     */
    public function __construct() {
        $this->form = new \Mos\HTMLForm\CForm();
        }    
    
    public function initialize() {
        $this->users = new \TBJ\Navigare\Boats\Users\User();
        $this->users->setDI($this->di);   
    }        

    
    public function addForm(){
            $formular = $this->form->create([], [
        'acronym' => [
            'type'        => 'text',
            'class'       => 'form-control',            
            'label'       => 'Användarnamn:',
            'required'    => true,
            'validation'  => ['not_empty']
              ],    
        'password' => [
            'class'       => 'form-control',    
            'label'       => 'Lösenord:',                 
            'type'        => 'password',       
            'required'    => true,
            'validation'  => ['not_empty']
              ],                  
            'Spara' => [
                'class'       => 'btn btn-primary',                
                'type'      => 'submit',
                'callback'  => function ($formular) {
                    $this->form->saveInSession = false;
                    return true;
                }
            ],

        ]); 
    }    
    
    public function addDuplicateAcronym($acronym) {
        $user = $this->users->findAcronym($acronym);
        return isset($user) ? true : false;
    }      
    
    /**
     * View login form.
     * 
     */
    public function addAction() {
        $this->initialize();

        $this->addForm();
        $form = $this->form->getHTML();
        $status = $this->form->check(); 
      
        if ($status === true) {
            $login = $this->checkLogin();
            if($login === true){
                $this->setUserInSession();
            } else {
                $this->views->addString("<div class='alert alert-danger' role='alert'><span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span> <span class='sr-only'>Error:</span>Felaktig inloggning, försök igen.</div>");                 

            }            
        }        
        
        $this->theme->setTitle("Logga in"); 
        $this->views->add('login/login-form', [
            'form' => $form,
            'title' => 'Logga in',
        ]);
      
    }
    
    public function checkLogin(){
        $user = $this->users->findAcronym($this->form->value('acronym'));
        if($user){
            $info = $user->getProperties();
            $hash = $info['password'];
            $this->userid = $info['id'];
            $this->useremail = $info['email'];
            if(password_verify($this->form->value('password'), $hash)){
                return true;
            } else {
                return false;                     
            }
        }
    }
    
    /**
    * Save user in session.
    *
    * @return void
    *
    */
    public function setUserInSession(){
        $this->session->set('login',[
            'acronym' => $this->form->value('acronym'),            
            'id' => $this->userid,
            'email' => $this->useremail,
        ]);  

        if($this->session->has('url')) {
            $url = $this->session->get('url'); // holds url for last page
        } else { 
            $url = $this->url->asset(''); 
        }
        header("Location: $url");  
        
    }    

    
    /**
    * Logout.
    *
    * @return void
    *
    */
    public function logoutAction(){
        unset($_SESSION['login']);   
        if($this->session->has('url')) {
            $url = $this->session->get('url'); // holds url for last page
        } else { 
            $url = $this->url->asset(''); 
        }
        header("Location: $url");   
        
    }
}

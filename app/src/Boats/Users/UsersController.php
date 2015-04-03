<?php
namespace TBJ\Navigare\Boats\Users;
 
/**
 * A controller for users and admin related events.
 *
 */
class UsersController implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;

    protected $user;
    protected $questions;
    protected $comments;
    protected $answers; 
    protected $users;
    protected $form;
    protected $answered;
    protected $questioned;
    protected $comment;
    protected $question;
    protected $answer;
    protected $num_answers = 0;
    protected $num_questions = 0;
    protected $num_comments = 0;
    protected $reputation = 0;
    
/**
 * Initialize the controller.
 *
 * @return void
 */
    public function initialize() {
        $this->users = new \TBJ\Navigare\Boats\Users\User();
        $this->users->setDI($this->di);        
        $this->comments = new \TBJ\Navigare\Boats\Comments\CommentsController();
        $this->comments->setDI($this->di); 
        $this->comment = new \TBJ\Navigare\Boats\Comments\Comments();
        $this->comment->setDI($this->di);         
        $this->questions = new \TBJ\Navigare\Boats\Questions\QuestionsController();
        $this->questions->setDI($this->di); 
        $this->question = new \TBJ\Navigare\Boats\Questions\Questions();
        $this->question->setDI($this->di);      
        $this->answer = new \TBJ\Navigare\Boats\Answers\Answers();
        $this->answer->setDI($this->di);            
        $this->form = new \Mos\HTMLForm\CForm();
    }

/**
 * List all users.
 *
 * @return void
 */
public function listAction() {
 
    $all = $this->findAllNotDeleted();
    $this->theme->addStylesheet('css/override.css'); 
    $this->theme->setTitle("Användare");       
    $this->views->add('boats/list-all', [
        'users' => $all,
        'title' => "Användare",
        'active' => "active",
        'trashed' => null,
        'inactive' => null,        
    ]);
    
  
}

/**
 * List user with id.
 *
 * @param int $id of user to display
 *
 * @return void
 */
    public function idAction($id = null) { 
        $this->initialize();
        $this->theme->addStylesheet('css/override.css');  
        $this->theme->setTitle("Användarprofil");        
        $this->user = $this->users->find($id);
        $this->answered = $this->questions->findUserAnsweredQuestions("boats_answers.userid", $id); 
        $this->questioned = $this->questions->findUserQuestions("boats_user.id", $id);

    }
    
    
    public function answeredAction($id){
        $this->idAction($id);
        $this->repAction($id);        
        
        $this->views->add('boats/view_users', [
            'user'      => $this->user,
            'title'     => 'Användarprofil',
            'questions' => null,
            'answered'   => $this->answered,
            'num_answers'   => $this->num_answers,
            'num_questions'   => $this->num_questions,
            'num_comments'   => $this->num_comments,
            'reputation'   => $this->reputation, 
            'quest'     => null,
            'answ'      => 'active',
            
        ]);            
    }

    public function questionAction($id){
        $this->idAction($id);
        $this->repAction($id);
        
        $this->views->add('boats/view_users', [
            'user'      => $this->user,
            'title'     => 'Användarprofil',
            'questions' => $this->questioned,
            'num_answers'   => $this->num_answers,
            'num_questions'   => $this->num_questions,
            'num_comments'   => $this->num_comments, 
            'reputation'   => $this->reputation, 
            'answered'   => null,
            'quest'     => 'active',
            'answ'      => null,
            
        ]);            
    }   
    
    public function repAction($id){
        $num_answers = $this->users->findNumberOfAnswers($id);
        $num_questions = $this->users->findNumberOfQuestions($id);
        $num_comments = $this->users->findNumberOfComments($id);
        if(isset($num_answers)){
            $this->num_answers = $num_answers[0]->Count;
            $rank_answers = $this->answer->findUserRank($id);
        }
                if(isset($num_questions)){
            $this->num_questions = $num_questions[0]->Count;
            $rank_questions = $this->question->findUserRank($id);
        }
                if(isset($num_comments)){
            $this->num_comments = $num_comments[0]->Count;
            $rank_comments = $this->comment->findUserRank($id);
        }
        $sum_answers = $rank_answers[0]->summa ? $rank_answers[0]->summa : null;
        $sum_questions = $rank_questions[0]->summa ? $rank_questions[0]->summa : null;
        $sum_comments = $rank_comments[0]->summa ? $rank_comments[0]->summa : null;
        

        $count_answers = $rank_answers[0]->antal ? $rank_answers[0]->antal : null;
        $count_questions = $rank_questions[0]->antal ? $rank_questions[0]->antal : null;
        $count_comments = $rank_comments[0]->antal ? $rank_comments[0]->antal : null;
        // rykte = summan av alla röstbetyg + summan av antalet röster 
        // gånger antalet inlägg (där svar är dubbelt värda, 
        // frågor ger 1 poäng och kommentarer ger 0,5) och sedan alltihop
        // delat med tio
        $this->reputation = round(($sum_answers + $sum_comments + $sum_questions) + ($count_answers + $count_questions + $count_comments) * ( ($this->num_answers * 2) + $this->num_questions + ($this->num_comments)/2) / 10);
        
    }
    
/**
 * Add new user.
 *
 * @param string $acronym of user to add.
 *
 * @return void
 */
public function addAction() {
    $this->initialize();
    $formular = $this->form->create([], [
        'name' => [
            'type'        => 'text',
            'class'       => 'form-control',      
            'label'       => 'Namn:',
            'required'    => true,
            'validation'  => ['not_empty'],
        ],
        'acronym' => [
            'type'        => 'text',
            'class'       => 'form-control',            
            'label'       => 'Användarnamn:',
            'required'    => true,
            'validation'  => ['not_empty','custom_test' => 
                             ['message' => '<div class="alert alert-danger" role="alert">
                             <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                             <span class="sr-only">Error:</span>
                             Det användarnamnet finns redan
                             </div>',
                             'test' => array($this, 'addDuplicateAcronym')
                    ]
                    ]
              ],
        'email' => [
            'type'        => 'email',
            'class'       => 'form-control',            
            'label'       => 'E-post:',            
            'required'    => true,
            'validation'  => ['not_empty', 'email_adress', 'custom_test'      
                          => ['message' => '<div class="alert alert-danger" role="alert">
                          <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                          <span class="sr-only">Error:</span>
                          Den e-postdressen används redan.
                          </div>', 'test' => array($this, 'addDuplicateEmail')
                    ]
                    ],
        ],
        'password' => [
            'type'        => 'password',
            'class'       => 'form-control',            
            'label'       => 'Lösenord:',            
            'required'    => true,
            'validation'  => ['not_empty'],
        ],        
        'Spara' => [
            'type'      => 'submit',
            'class'     => 'btn btn-primary',            
            'callback'  => function ($formular) {
                $this->form->saveInSession = false;
                return true;
            }
        ],

    ]);   
    
        // Kolla rad 400 i CForm.. Check if a form was submitted and perform validation...
    $form = $this->form->getHTML();           
    $status = $this->form->check();

    if ($status === true) {

        $now = date('Y-m-d H:i:s');        
        $this->users->save([
            'acronym' => $this->form->value('acronym'),
            'email' => $this->form->value('email'),
            'name' => $this->form->value('name'),
            'password' => password_hash($this->form->value('password'), PASSWORD_DEFAULT),
            'created' => $now,
            'active' => $now,
        ]);

        $url = $this->url->create('users/question/' . $this->users->id);

// läs här om $this->response: http://dbwebb.se/kunskap/anvand-cform-tillsammans-med-anax-mvc
         $this->response->redirect($url);

        } else if ($status === false) {
            $this->form->AddOutput("<h3>Kontrollera felmeddelandena ovan.</h3>");
            $form = $this->form->getHTML(); 
        }
             
        $this->theme->setTitle('Medlemsregistrering');         

        $this->views->add('boats/add_users', [
            'content' => $form,  
            'title' => 'Medlemsregistrering',
        ]);         
        $list = $this->url->create('list');
        $this->views->addString("<p><a href='"."$list"."'><span class='glyphicon glyphicon-eye-open'></span> Visa alla</a></p>", "triptych-1");          
    }
    

    
/**
 * Update user.
 *
 * @param string $acronym of user to update.
 *
 * @return void
 */
public function updateAction($id = null) {
    $this->initialize();
    $login = $this->session->get('login');
    $user = $this->users->find($id);
    $info = $user->getProperties();
    $id = isset($info['id']) ? $info['id'] : null; 
    $name = isset($info['name']) ? $info['name'] : null; 
    $acronym = isset($info['acronym']) ? $info['acronym'] : null;     
    $email = isset($info['email']) ? $info['email'] : null; 

    if($login['acronym'] != 'admin' && $acronym != $login['acronym']){
        $this->redirect();
    } else {    
        $formular = $this->form->create([], [
            'name' => [
                'class'       => 'form-control',
                'placeholder' => 'Sök',
                'type'        => 'text',
                'label'       => 'Namn:',
                'value'       => $name,
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
            'acronym' => [
                'type'        => 'text',
                'class'       => 'form-control',            
                'label'       => 'Användarnamn:',
                'value'       => $acronym,
                'required'    => true,
                'validation'  => ['not_empty','custom_test' => 
                                 ['message' => '<div class="alert alert-danger" role="alert">
                                 <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                                 <span class="sr-only">Error:</span>
                                 Det användarnamnet finns redan
                                 </div>',
                                 'test' => array($this, 'updateDuplicateAcronym')
                        ]
                        ]
                  ],
            'email' => [
                'type'        => 'email',
                'class'       => 'form-control', 
                'value'       => $email,
                'label'       => 'E-post:',            
                'required'    => true,
                'validation'  => ['not_empty', 'email_adress', 'custom_test'      
                              => ['message' => '<div class="alert alert-danger" role="alert">
                              <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                              <span class="sr-only">Error:</span>
                              Den e-postdressen används redan.
                              </div>', 'test' => array($this, 'updateDuplicateEmail')
                        ]
                        ],
            ],
            'password' => [
                'class'       => 'form-control',        
                'type'        => 'password',
                'label'       => 'Nytt lösenord:',            
                'required'    => false,
                'validation'  => ['pass'],
            ],        
            'Spara' => [
                'type'      => 'submit',
                'class'     => 'btn btn-primary',
                'callback'  => function ($formular) {
                    $this->form->saveInSession = false;
                    return true;
                }
            ],

        ]);   
    
        // Kolla rad 400 i CForm.. Check if a form was submitted and perform validation...
        $form = $this->form->getHTML();        
        $status = $this->form->check();

        if ($status === true) {
            if(!empty($this->form->value('password'))){
                $this->users->save([
                    'id' => $id,
                    'password' => password_hash($this->form->value('password'), PASSWORD_DEFAULT),
                ]);
            }
            $active = date('Y-m-d H:i:s');        
            $updated = date('Y-m-d H:i:s');
            $this->users->save([
                'id' => $id,
                'acronym' => $this->form->value('acronym'),
                'email' => $this->form->value('email'),
                'name' => $this->form->value('name'),
                'active' => $active,
                'updated' => $updated,
            ]);
            
            if($login['acronym'] != 'admin'){
                $this->session->set('login',[
                    'acronym' => $this->form->value('acronym'),            
                    'id' => $id,
                    'email' => $this->form->value('email'),
                ]);
            }
            $url = $this->url->create('users/question/' . $this->users->id);

// läs här om $this->response: http://dbwebb.se/kunskap/anvand-cform-tillsammans-med-anax-mvc
            $this->response->redirect($url);

            } else if ($status === false) {
                $this->form->AddOutput("<h3>Ett fel uppstod. Kontrollera felmeddelandena ovan.</h3>");
                $form = $this->form->getHTML(); 
            }
        
 
            $this->theme->setTitle('Användare');  
            $this->views->add('boats/add_users', [
                'content' => $form,
                'title' => 'Uppdatera medlem'
            ]);        

            $list = $this->url->create('list');
            $this->views->addString("<p><a href='"."$list"."'><i class='fa fa-eye'></i> Visa alla</a></p>", "triptych-1");  
        }
    }
    
    private function redirect(){
        if($this->session->has('url')) {
        $url = $this->session->get('url'); // holds url for last page
        } else { 
            $url = $this->url->asset('users/list'); 
        }
        header("Location: $url");  
    }    
   
    public function addDuplicateAcronym($acronym) {
        $user = $this->users->findAcronym($acronym);
        return empty($user);
    }    
    public function addDuplicateEmail($email) {
        $user = $this->users->findEmail($email);
        return empty($user);
    }
    public function updateDuplicateAcronym($acronym) {
        $login = $this->session->get('login');
        $res = $this->users->findAcronym($acronym);
        if(!empty($res)){
            $user = $res->getProperties();
            if($login['acronym'] == $user['acronym'] || $login['acronym'] == 'admin'){
                return true;
            } else {
                return false;
            }
        }
    }    
    public function updateDuplicateEmail($email) {
        $login = $this->session->get('login');
        $res = $this->users->findEmail($email);
        if(!empty($res)){
            $user = $res->getProperties();
            if($login['acronym'] == $user['acronym'] || $login['acronym'] == 'admin'){
                return true;
            } else {
                return false;
            }
        }
    }    

    /**
    * Delete user.
    *
    * @param integer $id of user to delete.
    *
    * @return void
    */
    public function deleteAction($id = null) {
 
        $res = $this->users->delete($id);
 
        $url = $this->url->create('users/list');
        $this->response->redirect($url);
    }

/**
 * Delete (soft) user.
 *
 * @param integer $id of user to delete.
 *
 * @return void
 */
    public function softDeleteAction($id = null){
        $login = $this->session->get('login');
        $active = $this->checkIfActive($id);
        if($login['acronym'] != 'admin'){
            $this->redirect();
        } else {
            if($active == false){
                $now = gmdate('Y-m-d H:i:s');
                $user = $this->users->find($id); 
                $user->deleted = $now;
                $user->save();
                $url = $this->di->url->create($_SERVER['HTTP_REFERER']);
                $this->response->redirect($url);
            } else {
                $error = "Den här användaren har frågor, svar eller kommentarer kopplade till sig och kan därför inte tas bort.";
                $this->session->set('error',$error);
            }
                $url = $this->di->url->create('users');
                $this->response->redirect($url);                
        }
    }
    
    // kolla om user är aktiv före softdelete
    public function checkIfActive($id){
        $questions = $this->question->findUserQuestions("boats_questions.userid", $id);
        $comments = $this->comment->findUserComments("boats_comments.userid", $id);
        $answers = $this->answer->findUserAnswers("boats_answers.userid", $id);
        return(!empty($questions || $comments || $answers));
    }

/**
 * List all active and not deleted users.
 *
 * @return void
 */
public function findAllNotDeleted() {   
    $all = $this->users->query()
        ->where('active IS NOT NULL')
        ->andWhere('deleted is NULL')
        ->execute();
        return $all;
}

    public function ActiveAction(){
    $all = $this->findAllNotDeleted();
    $this->theme->setTitle("Aktiva användare");  
    $this->views->add('boats/list-all', [
        'users' => $all,
        'title' => "Aktiva användare",
    ]);
    
//    $trash = $this->trashcanAction();
//    $this->views->addString("<h1>Papperskorgen</h1>" . $trash, 'sidebar');       
}

/**
 * List all active and not deleted users.
 *
 * @return void
 */
    public function inactiveAction() {
        $login = $this->session->get('login');
        if($login['acronym'] != 'admin'){
            $this->redirect();
        } else {  
            $all = $this->users->query()
                ->where('active IS NULL')
                ->andWhere('deleted is NULL')
                ->execute();
        
            $this->theme->addStylesheet('css/override.css');  
            $this->theme->setTitle("Inaktiva användare");   
            $this->views->add('boats/list-all', [
                'users' => $all,
                'title' => "Inaktiva användare",
                'active' => null,
                'trashed' => null,
                'inactive' => 'active',   
           ]);
        }
    }

/**
 * deactivate a user.
 *
 * @return void
 */
    public function deactivateAction($id=null) { 
        $login = $this->session->get('login');
        if($login['acronym'] != 'admin'){
            $this->redirect();
        } else {          
            $this->users->save([
                'id'          => $id,
                'active'    => null,
            ]);
            $url = $this->di->url->create($_SERVER['HTTP_REFERER']);
            $this->response->redirect($url);
        }
    }

/**
 * activate a user.
 *
 * @return void
 */
    public function activateAction($id=null) {   
        $login = $this->session->get('login');
        if($login['acronym'] != 'admin'){
            $this->redirect();
        } else {          
            $now = date('Y-m-d H:i:s'); 
            $this->users->save([
                'id'          => $id,
                'active'    => $now,
            ]);
            $url = $this->di->url->create($_SERVER['HTTP_REFERER']);
            $this->response->redirect($url);
        }
    }

/**
 * List all soft deleted users.
 *
 * @return void
 */
    public function trashcanAction() {
                $login = $this->session->get('login');
        if($login['acronym'] != 'admin'){
            $this->redirect();
        } else {  
            $all = $this->users->query()
                ->where('deleted is NOT NULL')
                ->execute();

                $this->theme->addStylesheet('css/override.css'); 
                $this->theme->setTitle("Papperskorgen");       
                $this->views->add('boats/list-all', [
                    'users' => $all,
                    'title' => "Papperskorgen",
                    'active' => null,
                    'trashed' => "active",
                    'inactive' => null,        
            ]);
        }
    }

    public function restoreAction($id=null) {
        $login = $this->session->get('login');
        if($login['acronym'] != 'admin'){
            $this->redirect();
        } else {          
            $now = date('Y-m-d H:i:s');    
            $this->users->save([
                'id'          => $id,
                'deleted'    => null,
            ]);
            $url = $this->di->url->create($_SERVER['HTTP_REFERER']);
            $this->response->redirect($url);
        }
    }

    public function deleteTrashAction() {
        $login = $this->session->get('login');
        if($login['acronym'] != 'admin'){
            $this->redirect();
        } else {   
            $res = $this->users->query()
                ->where('deleted is NOT NULL')
                ->execute();
                if(isset($res)){     
                    foreach($res as $id) {
                        $this->users->delete($id->id);
                }
                    $url = $this->di->url->create('users/list');
                    $this->response->redirect($url);
            }
        }
    }
    
    public function mostActiveAction(){
        $users = $this->users->findActiveUser(); 

        $html = "";
        foreach ($users as $user) {
            $info = $user->getProperties();  
            $gravatar = get_gravatar($info['email']);
            $acronym = $info['acronym'];
            $number = $info['Count'];
            $html .= "<a href='{$this->url->create('users/question')}/{$info['userid']}'>
            <button class='btn btn-default btn-lg btn-block'>
            $gravatar <strong>$acronym</strong>
             <span>har totalt gjort $number inlägg</span>
            </button></a><br>";
        }                    
        return $html;
    }
}

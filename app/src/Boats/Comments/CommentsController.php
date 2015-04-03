<?php

namespace TBJ\Navigare\Boats\Comments;

/**
 * To attach comments-flow to a page or some content.
 *
 */
class CommentsController implements \Anax\DI\IInjectionAware {
    use \Anax\DI\TInjectable;
    
    protected $db;
    protected $comments;
    protected $questions;
    
    public function __construct(){
        $this->form = new \Mos\HTMLForm\CForm();
    }    
    
    /**
 * Initialize the controller.
 *
 * @return void
 */
    public function initialize() {
        $this->comments = new \TBJ\Navigare\Boats\Comments\Comments();
        $this->comments->setDI($this->di);
        $this->questions = new \TBJ\Navigare\Boats\Questions\Questions();
        $this->questions->setDI($this->di);
    }
    
    // sanitize user input
    public function strip_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = strip_tags($data);
        return $data;
    }
    /**
     * Add a comment to a question.
     *
     * @return void
     */
    public function addAction() {
        $this->initialize();  
        
        $questionid = $this->request->getPost('questionid');
        $answerid = $this->request->getPost('answerid');
        $userid = $this->request->getPost('userid');
        $login = $this->session->get('login');        
        if($userid != $login['id']){
            $this->redirect();
        } else {
            $comment = $this->strip_input($this->request->getPost('comment'));
            $now = date('Y-m-d H:i:s');        
            $this->comments->add([
                'comment' => $comment,
                'questionid' => $questionid,
                'answerid' => $answerid,            
                'userid' => $userid,
                'created' => $now,          
            ]);  

            $this->redirect(); 
            
        }
    }
    
    
    
    private function redirect(){
        if($this->session->has('url')) {
        $url = $this->session->get('url'); // holds url for last page
        } else { 
            $url = $this->url->asset('questions/list'); 
        }
        header("Location: $url");  
    }
        
    

    public function findComments($type, $id) {
        $this->initialize();          
        $comments = $this->comments->findAllComments($type, $id);
        return $comments;
    }
    
    public function findAnswerComments($type, $id) {
        $this->initialize();          
        $comments = $this->comments->findAllAnswerComments($type, $id);
        return $comments;
    }    
    
    public function findAnswerId($questionid) {
        $this->initialize();          
        $id = $this->comments->findAnswersId($questionid);
        return $id;
    }    
    
    public function createForm($input, $commentid, $questionid, $answerid){
            $comment = $this->strip_input($input);
            $formular = $this->form->create([], [
            'comment' => [
                'class'       => 'form-control',    
                'label'       => 'Kommentar:',   
                'value'       => $comment,
                'type'        => 'textarea',       
                'required'    => true,
                'validation'  => ['not_empty'],
                ],    
            'commentid' => [
                'type'       => 'hidden',      
                'value'       => $commentid,
                ], 
            'questionid' => [
                'type'       => 'hidden',      
                'value'       => $questionid,
                ],    
            'answerid' => [
                'type'       => 'hidden',      
                'value'       => $answerid,
                ],                 
/*            'rank' => [              
                'type'        => 'hidden',
                'value'       => $rank,                
                ],*/                 
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
    
    public function editAction($commentid, $questionid, $acronym){
        $this->initialize();  
        $answerid = $this->request->getGet('answerid');
        $login = $this->session->get('login');
        $prop = $this->comments->findComment($commentid); 
        $comment = $prop[0]->getProperties();
        if($login['acronym'] != 'admin' && $comment['userid'] != $login['id']){
            $this->redirect();
        } else {
            $res = $this->questions->findQuestion('boats_questions.id', $questionid);
            $question = $res->getProperties();

            $this->createForm($comment['comment'],$commentid,$questionid,$answerid);
        
            $form = $this->form->getHTML();
            $status = $this->form->check();   
            if ($status === true) {
                $this->update($commentid);
            } else if ($status === false) {
                $this->form->AddOutput("<h3>Ett fel uppstod. Kontrollera felmeddelandena ovan.</h3>");
                $form = $this->form->getHTML();           
            }        
            $question_md = $this->textFilter->doFilter($question['question'], 'shortcode, markdown');            
            $this->theme->setTitle("Frågor");         
            $this->views->add('comments/comment-form', [
                'form' => $form,
                'title' => 'Redigera kommentar',
                'question' => $question_md,    
                'question_title' => $question['title'],         
                'email' => $question['email'],
                'acronym' => $question['acronym'],
            ]);
        }
    }
    // röstning på frågekommentar    
    public function voteAction(){
        $this->initialize(); 
        $login = $this->session->get('login');
        $commentid = $this->request->getPost('commentid');
        $vote = $this->request->getPost('vote');
        $res = $this->comments->findComment($commentid);
        $comment = $res[0]->getProperties();
        $votedby = $this->comments->findVotes($login['id']);
        $voteres = null;
        foreach($votedby as $val){
            $voteres = $val->commentid == $commentid ? true : null;
            if($voteres == true){
                break;
            }
        }
        // om votedby eller voteres är NULL har inloggad användare inte röstat
        if(!isset($votedby) || $voteres == null){
            if($login['acronym'] == 'admin' || $login['id'] != $comment['userid']){        
            if($vote == 'up'){
            $this->comments->add([
                        'id' => $commentid,
                        'rank' => $comment['rank'] + 1,          
                    ]); 
                // save whom voted on which answer in separate table
                $this->comments->vote(['commentid', 'votedby'], [$commentid, $login['id']]);             
                }
                if($vote == 'down'){
                    $this->comments->add([
                        'id' => $commentid,
                        'rank' => $comment['rank'] - 1,            
                    ]); 
                    $this->comments->vote(['commentid', 'votedby'], [$commentid, $login['id']]);                        
                }
            }
        }
        $this->redirect();
    }
    
    // röstning på svarskommentar
    public function voteCommentAction(){
        $this->initialize(); 
        $login = $this->session->get('login');
        $commentid = $this->request->getPost('commentid');
        $vote = $this->request->getPost('vote');
        $res = $this->comments->findComment($commentid);
        $comment = $res[0]->getProperties();
        $votedby = $this->comments->findVotes($login['id']);
        $voteres = null;
        foreach($votedby as $val){
            $voteres = $val->commentid_a == $commentid ? true : null;
            if($voteres == true){
                break;
            }
        }
        // om votedby eller voteres är NULL har inloggad användare inte röstat
        if(!isset($votedby) || $voteres == null){
            if($login['acronym'] == 'admin' || $login['id'] != $comment['userid']){        
            if($vote == 'up'){
            $this->comments->add([
                        'id' => $commentid,
                        'rank' => $comment['rank'] + 1,          
                    ]); 
                // save whom voted on which answer in separate table
                $this->comments->vote(['commentid_a', 'votedby'], [$commentid, $login['id']]);             
                }
                if($vote == 'down'){
                    $this->comments->add([
                        'id' => $commentid,
                        'rank' => $comment['rank'] - 1,            
                    ]); 
                    $this->comments->vote(['commentid_a', 'votedby'], [$commentid, $login['id']]);                        
                }
            }
        }
        $this->redirect();
    }    
    
        public function update($id) {

        $now = date('Y-m-d H:i:s');   
                    
        $this->comments->add([
            'id' => $id,
            'comment' => $this->form->value('comment'),
           /* 'rank' => $this->form->value('rank'),*/
            'updated' => $now,          
        ]);  

        $this->redirect();
    }
        /**
    * Delete (soft) comment.
    *
    *
    * @return void
    */
    public function softDeleteAction($commentid) {  
        $res = $this->comments->findComment($commentid);
        $comment = $res[0]->getProperties();
        $login = $this->session->get('login');
        if($login['acronym'] != 'admin' && $comment['userid'] != $login['id']){
            $this->redirect();
        } else {  
            $now = gmdate('Y-m-d H:i:s');
            $params['id'] = $commentid;
            $params['deleted'] = $now;
            $this->comments->updateComment($params);
            $url = $this->di->url->create($_SERVER['HTTP_REFERER']);
            $this->response->redirect($url);
        }
    }
}

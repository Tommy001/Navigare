<?php

namespace TBJ\Navigare\Boats\Questions;

/**
 * To attach questions-flow to a page or some content.
 *
 */
class QuestionsController implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;
    
    protected $form;
    protected $db;
    protected $questions;
    protected $comments;
    protected $comment;
    protected $answers;
    protected $answer;
    protected $user;
    protected $tags;
    protected $tag;
    
    /**
 * Initialize the controller.
 *
 * @return void
 */
    public function initialize() {
        $this->user = new \TBJ\Navigare\Boats\Users\UsersController();
        $this->tags = new \TBJ\Navigare\Boats\Tags\TagsController();
        $this->tags->setDI($this->di);   
        $this->tag = new \TBJ\Navigare\Boats\Tags\Tags();
        $this->tag->setDI($this->di);           
        $this->answers = new \TBJ\Navigare\Boats\Answers\AnswersController();
        $this->answers->setDI($this->di);   
        $this->answer = new \TBJ\Navigare\Boats\Answers\Answers();
        $this->answer->setDI($this->di);           
        $this->comments = new \TBJ\Navigare\Boats\Comments\CommentsController();
        $this->comments->setDI($this->di);  
        $this->comment = new \TBJ\Navigare\Boats\Comments\Comments();
        $this->comment->setDI($this->di);          
        
        $this->questions = new \TBJ\Navigare\Boats\Questions\Questions();
        $this->questions->setDI($this->di);
        $this->form = new \Mos\HTMLForm\CForm();
    }
        
    
    public function strip_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = strip_tags($data);
        return $data;
    }
    
    /**
    * List user with id.
    *
    * @param int $id of user to display
    *
    * @return void
    */
    public function idAction($questionid=null, $error=null) {
        if(!isset($questionid)){
            $questionid = $this->request->getPost('questionid');
        }
        $tags = $this->tags->findTags('boats_tags2question.question_id',$questionid);
        $andwhere = 'boats_answers.deleted is NULL';
        $answers = $this->answers->findAnswers("boats_answers.questionid", $questionid, $andwhere);
        $comments_q = $this->comments->findComments("boats_comments.questionid", $questionid);
        $comments_a = $this->comments->findAnswerComments("boats_comments.questionid", $questionid);
        $this->theme->addStylesheet('css/override.css');  
        $this->theme->setTitle("Frågor");      
        $question = $this->questions->findQuestion("boats_questions.id", $questionid);
        $answerspanel = $this->answers->getAnswerPanel($answers, $comments_a, $questionid, $question);    
        $res = $this->questions->findRank($questionid);
        $rank = $res->rank;
        $this->theme->setTitle("Frågor"); 
        $this->views->add('questions/view_questions', [
            'question'  => $question,
            'title'     => 'Fråga',
            'comments_q'  => $comments_q,
            'answerspanel'  => $answerspanel,            
            'answers'   => $answers,
            'tags'      => $tags,
            'error'     => $error,
            'rank'     => $rank,         
        ]);
    }

/**
 * List all questions.
 *
 * @return void
 */
    public function listAction() {
 
        $questions = $this->questions->findAllNotDeletedQuestions();
        $this->theme->addStylesheet('css/override.css'); 
        $this->theme->setTitle("Frågor");       
        $this->views->add('questions/list-all-questions', [
            'questions' => $questions,
            'title' => "Frågor",
            'active' => "active",
            'trashed' => null,  
            'noanswer' => null,  
            'searchresult' => null,
        ]);   
    }
    
    // get latest questions to display on start page
    public function getLatestAction(){
        $questions = $this->questions->findAllNotDeletedQuestions(3);
        $html = "";
        foreach ($questions as $val) {
            $info = $val->getProperties();
            $text = $this->textFilter->doFilter($info['question'], 'shortcode, markdown');  
            $pos=strpos($text, ' ', 50);
            $short = substr($text,0,$pos ); 
            //$question = wordwrap($question, 50);
            //$question = explode("\n", $question);
            $question = $short . '... Se frågan >>';
            
            $html .= "
                <a href='{$this->url->create('questions/id')}/{$info['id']}'> 
                <button style='margin-bottom:4px;white-space: normal;' class='btn btn-default btn-lg btn-block'>
            $question</button></a><br>";
        }

            
        return $html;
    }

    public function createAddForm($userid, $options, $question, $checked, $title, $questionid){
   //         $this->initialize();

   //exit;
            $formular = $this->form->create([], [
            'title' => [
                'class'       => 'form-control',    
                'label'       => 'Rubrik:',  
                'value'       => $title,                
                'type'        => 'text',       
                'required'    => true,
                'validation'  => ['not_empty'],
                ],    
            'question' => [
                'class'       => 'form-control',    
                'label'       => 'Fråga:',                 
                'type'        => 'textarea',
                'value'       => $question,
                'required'    => true,
                'validation'  => ['not_empty'],
                ], 
            'tags' => [    
                'type'        => 'checkbox-multiple',              
                'label'       => 'Välj ett eller flera ämnen:',                   
                'values'      => $options, 
                'checked'     => $checked,
                ],                            
            'userid' => [              
                'type'        => 'hidden',
                'value'       => $userid,                
                ], 
            'questionid' => [              
                'type'        => 'hidden',
                'value'       => $questionid,                
                ],                 
            'Spara' => [
                'class'       => 'btn btn-primary',                
                'type'        => 'submit',
                'callback'    => function ($formular) {
                    $this->form->saveInSession = false;
                    return true;
                }
            ],

        ]);            
    }
    
    private function redirect(){
        if($this->session->has('url')) {
        $url = $this->session->get('url'); // holds url for last page
        } else { 
            $url = $this->url->asset('questions/list'); 
        }
        header("Location: $url");  
    }

    
    /**
     * View all questions.
     * @return void
     */
    public function addAction() {
        $this->initialize();  
        $userid = $this->request->getGet('userid');
        $login = $this->session->get('login');
        
        if($userid == $login['id']) {
            $res = $this->tags->findAllActiveTags();
            $options = array();
            foreach($res as $val){
                $name = $val->getProperties();
                $options[] = $name['name'];
            }

            $this->createAddForm($userid, $options, $question=null, $checked=null, $title=null, $questionid=null);
            $form = $this->form->getHTML();
            $status = $this->form->check(); 
            $tagscheck = $this->checkTags();
            if ($status === true && $tagscheck === true) {

                $this->saveAction();
                $form = $this->form->getHTML();
            } else if ($status === false || $tagscheck === false) {
                $this->form->AddOutput("
                                <div class='pull-right' style='color:red;'><span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span>
                                <span class='sr-only'>Error:</span>
                                Ett fel uppstod. Har du valt för många ämnen? Högst tre är möjliga.</div>
                                ");
                $form = $this->form->getHTML();           
            
            }        
        
            $this->theme->setTitle("Frågor"); 
            $this->views->add('questions/add_question', [
                'form' => $form,
                'title' => 'Ställ en fråga',
            ]);
        } else {
            $this->redirect();
        }
    }


    /**
     * Add or update a comment.
     *
     * @return void
     */
    public function saveAction($question_id=null) {
            
        $now = date('Y-m-d H:i:s');   
  
        // tillåter inte att användare märker upp titeln med MD
        $title = $this->textFilter->doFilter($this->form->value('title'), 'shortcode, markdown');
        $title = $this->strip_input($title);        
        
        // om $id är satt så är det uppdatering, annars ny post
        // vid uppdatering tas rader bort i kopplingstabellen
        // är det "ny fråga" hämtas id från den nya frågan        
        if(isset($question_id)){
            $this->questions->add([
                'id' => $question_id,                
                'title' => $title,            
                'question' => $this->form->value('question'),        
                'updated' => $now,       
            ]);              
            $this->tags->deleteRelations($question_id);
        } else {
            $this->questions->add([
                'title' => $title,            
                'question' => $this->form->value('question'),
                'userid' => $this->form->value('userid'),        
                'created' => $now,       
            ]);              
            $question_id = $this->questions->db->lastInsertId();
        }
        
        // lägg till rader i kopplingstabellen
        $tags = $this->request->getPost('tags');        
        foreach($tags as $tag){
            $tag_id = $this->questions->findTagId($tag);

            $res = $this->questions->saveTags2Question(array(          
                'question_id', 'tag_id'), array($question_id,
                $tag_id->id));
        }

        $this->redirect();

    }
    
    public function checkTags(){
        $tags = $this->request->getPost('tags'); 
        if(isset($tags)){
            $i=0;
            foreach($tags as $tag){
                $i++;
            }
            if($i >= 4){
                return false;
            } else {
                return true;
            }
        }
    }
    
    public function editAction($questionid){
        $this->initialize();  
        $res = $this->tags->findAllActiveTags();
        $options = array();
        foreach($res as $val){
            $name = $val->getProperties();
            $options[] = $name['name'];
        }
        $res = $this->questions->findQuestion('boats_questions.id', $questionid);
        $question = $res->getProperties();
        $checked = $this->tags->findChecked('boats_tags2question.question_id',$questionid);
        $this->createAddForm($userid=null, $options, $question['question'], $checked, $question['title'], $questionid);     
        $form = $this->form->getHTML();
        $status = $this->form->check();   
        $tagscheck = $this->checkTags();
        if ($status === true && $tagscheck === true) {
            $this->saveAction($questionid);
            $form = $this->form->getHTML();
        } else if ($status === false || $tagscheck === false) {
            $this->form->AddOutput("
                            <div class='pull-right' style='color:red;'><span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span>
                            <span class='sr-only'>Error:</span>
                            Ett fel uppstod. Har du valt för många ämnen? Högst tre är möjliga.</div>
                            ");
            $form = $this->form->getHTML();           
        }        
        $this->theme->setTitle("Frågor"); 
        $this->views->add('questions/add_question', [
            'form' => $form,
            'title' => 'Redigera fråga',
            'question' => $question['question'],
            'question_title' => $question['title'],
            'email' => $question['email'],
            'acronym' => $question['acronym'],
        ]);         
        
    }    
    
    
    public function voteAction(){
        $this->initialize(); 
        $login = $this->session->get('login');
        $questionid = $this->request->getPost('questionid');
        $vote = $this->request->getPost('vote');
        $res = $this->questions->findQuestionById($questionid);
        $question = $res[0]->getProperties();
        $votedby = $this->questions->findVotes($login['id']);
        $voteres = null;
        foreach($votedby as $val){
            $voteres = $val->questionid == $questionid ? true : null;
            if($voteres == true){
                break;
            }
        }
        // inloggad användare har inte röstat om votedby är en tom array 
        // eller om voteres är NULL
        if(!isset($votedby) || $voteres == null){
            if($login['acronym'] == 'admin' || $login['id'] != $question['userid']){        
            if($vote == 'up'){
            $this->questions->add([
                        'id' => $questionid,
                        'rank' => $question['rank'] + 1,          
                    ]); 
                // save whom voted on which answer in separate table
                $this->questions->vote(['questionid', 'votedby'], [$questionid, $login['id']]);             
                }
                if($vote == 'down'){
                    $this->questions->add([
                        'id' => $questionid,
                        'rank' => $question['rank'] - 1,            
                    ]); 
                    $this->questions->vote(['questionid', 'votedby'], [$questionid, $login['id']]);                        
                }
            }
        }
        $this->redirect();
    }
    
    public function findUserQuestions($type,$id){
        $this->initialize();  
        $questions = $this->questions->findAllUserQuestions($type,$id);
        return $questions;
    }
    
    public function findUserAnsweredQuestions($type,$id){
        $this->initialize();  
        $questions = $this->questions->findAllAnsweredUserQuestions($type,$id);
        return $questions;
    }
    
    public function searchAction(){
        $this->initialize();  
        $searchtext = $this->request->getPost('search'); 
        if(isset($searchtext)){
            $this->session->set('searchtext',$searchtext);
        } else {
            $searchtext = $this->session->get('searchtext');
        }
        $questions = $this->questions->searchInDatabase($searchtext);
        $this->theme->setTitle("Frågor"); 
        $this->views->add('questions/list-all-questions', [
            'questions' => $questions,
            'title' => "Frågor",
            'active' => null,
            'trashed' => null,  
            'noanswer' => null,  
            'searchresult' => 'active',
        ]);        
    } 
    
    public function noAnswersAction(){
        $this->initialize();  
        $questions = $this->questions->noAnswers();  
        $this->theme->setTitle("Frågor"); 
        $this->views->add('questions/list-all-questions', [
            'questions' => $questions,
            'title' => "Frågor",
            'active' => null,
            'trashed' => null,  
            'noanswer' => 'active',  
            'searchresult' => null,
        ]);        
    }    
    public function softDeletedAction(){
        $this->initialize();  
        $questions = $this->questions->softDeletedQuestions();  
        $this->theme->setTitle("Frågor"); 
        $this->views->add('questions/list-all-questions', [
            'questions' => $questions,
            'title' => "Frågor",
            'active' => null,
            'trashed' => 'active',  
            'noanswer' => null,  
            'searchresult' => null,
        ]);        
    }     
    
    public function restoreAction($id){
        $this->initialize(); 
        $params['id'] = $id;        
        $params['deleted'] = null;
        $this->questions->updateQuestion($params);  
        $this->listAction();
    }     
    
    
    public function softDeleteAction($questionid, $userid) {        
        $login = $this->session->get('login'); 


        if($login['acronym'] != 'admin' && $userid != $login['id']){
            $this->redirect();
        } else {
            $now = gmdate('Y-m-d H:i:s');
            $andwhere = 'deleted is NULL';
            $comment = $this->comment->findQuestionComment($questionid, $andwhere);
            if(empty($comment)){
                $params['id'] = $questionid;
                $params['deleted'] = $now;
                $this->questions->updateQuestion($params);
                unset($_SESSION['error']);
            } else {
                $error = "Den här frågan har svar eller kommentarer kopplade till sig och kan därför inte tas bort.";
                $this->session->set('error',$error);
                $url = $this->di->url->create('questions/list');
                $this->response->redirect($url);                
            }
            $andwhere = 'boats_answers.deleted is NULL';
            $answer = $this->answers->findAnswers('boats_answers.questionid', $questionid, $andwhere);
            if(empty($answer)){
                $params['id'] = $questionid;
                $params['deleted'] = $now;
                $this->questions->updateQuestion($params);
                unset($_SESSION['error']);
            } else {
                $error = "Den här frågan har ett svar kopplat till sig och kan därför inte tas bort.";
                $this->session->set('error',$error);
                $url = $this->di->url->create('questions/list');
                $this->response->redirect($url);                
            }
            $url = $this->di->url->create('questions/list');
            $this->response->redirect($url);
        }
    }
    
    // delete question permanently and also delete all related
    // answers and comments
    public function deleteAction($questionid, $deleteAll=null){
        $this->initialize(); 
        $andwhere = 'boats_comments.deleted is NOT NULL';
        $comments = $this->comment->findQuestionComment($questionid, $andwhere);
        foreach($comments as $val){
            $comment = $val->getProperties();
            $this->comment->deleteComment($comment['id']);
        }
        $andwhere = 'boats_answers.deleted is NOT NULL';
        $answers = $this->answers->findAnswers('boats_answers.questionid', $questionid, $andwhere);        
        foreach($answers as $val){
            $answer = $val->getProperties();
            $this->answer->deleteAnswer($answer['id']);
        }
        
        $this->tag->deleteTag2Questions($questionid);  
        $this->questions->deleteQuestion($questionid); 
        if($deleteAll == null){
            $this->listAction();
        }
    }   
    
    // empties trashcan and all related comments anc answers
    public function deleteTrashAction(){
        $softisar = $this->questions->softDeletedQuestions();
        foreach($softisar as $res){
            $softis = $res->getProperties();
            $this->deleteAction($softis['id'], $deleteAll=true);
        }
    $this->listAction();
    } 
}

<?php

namespace TBJ\Navigare\Boats\Answers;

/**
 * To attach comments-flow to a page or some content.
 *
 */
class AnswersController implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;
    
    protected $db;
    protected $answers;
    protected $form;
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
        $this->answers = new \TBJ\Navigare\Boats\Answers\Answers();
        $this->answers->setDI($this->di);
        $this->questions = new \TBJ\Navigare\Boats\Questions\Questions();
        $this->questions->setDI($this->di);
        $this->comments = new \TBJ\Navigare\Boats\Comments\Comments();
        $this->comments->setDI($this->di);        
    }
    
    // sanitize user input
    public function strip_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = strip_tags($data);
        return $data;
    }    
        
    public function createAddForm($questionid, $userid, $answer=null,$answerid=null){
        
            $formular = $this->form->create([], [
            'answer' => [
                'class'       => 'form-control',    
                'label'       => 'Svar:',   
                'value'       => $answer,
                'type'        => 'textarea',       
                'required'    => true,
                'validation'  => ['not_empty'],
                ],    
            'questionid' => [
                'type'        => 'hidden',
                'value'       => $questionid,                
                ],  
            'answerid' => [
                'type'        => 'hidden',
                'value'       => $answerid,                
                ],                   
            'userid' => [              
                'type'        => 'hidden',
                'value'       => $userid,                
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
    
    
    /**
     * Add an answer.
     *
     * @return void
     */
    public function addAction($questionid, $userid) {
        $this->initialize();  
        $login = $this->session->get('login');
        
        if($userid == $login['id']) {
        
            $res = $this->questions->findQuestion('boats_questions.id', $questionid);
            $question = $res->getProperties();
            $this->createAddForm($questionid,$userid);
            $form = $this->form->getHTML();
            $status = $this->form->check();   
            if ($status === true) {
                $this->saveAction();

            } else if ($status === false) {
                $this->form->AddOutput("<h3>Ett fel uppstod. Kontrollera felmeddelandena ovan.</h3>");
                $form = $this->form->getHTML();           
            
            }        
            $question_md = $this->textFilter->doFilter($question['question'],'shortcode, markdown');        
            $this->theme->setTitle("Frågor"); 
            $this->views->add('answers/answer-form', [
                'form' => $form,
                'title' => 'Svara på en fråga',
                'question' => $question_md,
                'question_title' => $question['title'],
                'email' => $question['email'],
                'acronym' => $question['acronym'],
                'userid' => $question['userid'],                
            ]); 
        } else {
                $this->redirect();
        }        
    }
    

    /**
     * Save an answer.
     *
     * @return void
     */
    public function saveAction() {

        $now = date('Y-m-d H:i:s');        
        $this->answers->add([
            'answer' => $this->strip_input($this->form->value('answer')),
            'questionid' => $this->form->value('questionid'),
            'userid' => $this->form->value('userid'),
           /* 'rank' => $this->form->value('rank'),*/
            'created' => $now,          
        ]);  
        
        $this->redirect();         
    }
    /**
     * Add an answer.
     *
     * @return void
     */
    public function editAction($answerid, $questionid) {

        $this->initialize();       
        $prop = $this->answers->findAnswer($answerid); 
        $answer = $prop[0]->getProperties();
        $login = $this->session->get('login');
      
        if($login['acronym'] != 'admin' && $answer['userid'] != $login['id']){
            $this->redirect();
        } else {   

            $res = $this->questions->findQuestion('boats_questions.id', $questionid);
            $question = $res->getProperties();
            $this->createAddForm($questionid, $userid=null, $answer['answer'],$answerid);
            $form = $this->form->getHTML();
            $status = $this->form->check();   
            if ($status === true) {
                $this->update($answerid);
            } else if ($status === false) {
                $this->form->AddOutput("<h3>Ett fel uppstod. Kontrollera felmeddelandena ovan.</h3>");
                $form = $this->form->getHTML();           
            } 
            $question_md = $this->textFilter->doFilter($question['question'],'shortcode, markdown');              
            $this->theme->setTitle('Frågor');         
            $this->views->add('answers/answer-form', [
                'form' => $form,
                'title' => 'Redigera svar',
                'question' => $question_md,
                'question_title' => $question['title'],
                'email' => $question['email'],
                'acronym' => $question['acronym'],
                'userid' => $question['userid'],
            ]);   
        }
    }
    
        public function update($id) {

        $now = date('Y-m-d H:i:s');        
        $this->answers->add([
            'id' => $id,
            'answer' => $this->form->value('answer'),
           /* 'rank' => $this->form->value('rank'),*/
            'updated' => $now,          
        ]);  

        $this->redirect();
    }
    
    private function redirect(){
        if($this->session->has('url')) {
        $url = $this->session->get('url'); // holds url for last page
        } else { 
            $url = $this->url->asset('questions/list'); 
        }
        header("Location: $url");  
    }
    

    public function findAnswers($type, $id, $andwhere) {
        $this->initialize();          
        $answers = $this->answers->findAllAnswers($type, $id, $andwhere);
        return $answers;
    }
    
    public function okAction(){
        $now = date('Y-m-d H:i:s');
        $login = $this->session->get('login');  
        
        $questionid = $this->request->getPost('questionid');
        $question = $this->questions->findSpecificQuestion($questionid);
        
        $answersid = $this->request->getPost('answerid');
        $res_a = $this->answers->findAnswer($answersid);
        $answer = $res_a[0]->getProperties();
        
        if($login['acronym'] == 'admin' || $login['acronym'] == $question->acronym){
        
            if(isset($answer['accepted'])) {
            $this->answers->add([
                    'id' => $answersid,
                    'accepted' => null,          
                ]); 
            } else {
                $this->answers->add([
                    'id' => $answersid,
                    'accepted' => $now,          
                ]);
            }
        }
        $this->redirect();
    }
    
    public function voteAction(){
        $this->initialize(); 
        $login = $this->session->get('login');
        $answersid = $this->request->getPost('answerid');
        $vote = $this->request->getPost('vote');
        $res_a = $this->answers->findAnswer($answersid);
        $answer = $res_a[0]->getProperties();
        $votedby = $this->answers->findVotes($login['id']);
        $voteres = null;
        foreach($votedby as $val){
            $voteres = $val->answerid == $answersid ? true : null;
            if($voteres == true){
                break;
            }
        }
        // om index answerid är NULL har inloggad användare inte röstat
        if(!isset($votedby) || $voteres == null){
            if($login['acronym'] == 'admin' || $login['id'] != $answer['userid']){        
            if($vote == 'up'){
            $this->answers->add([
                        'id' => $answersid,
                        'rank' => $answer['rank'] + 1,          
                    ]); 
                // save whom voted on which answer in separate table
                $this->answers->vote(['answerid', 'votedby'], [$answersid, $login['id']]);             
                }
                if($vote == 'down'){
                    $this->answers->add([
                        'id' => $answersid,
                        'rank' => $answer['rank'] - 1,            
                    ]); 
                    $this->answers->vote(['answerid', 'votedby'], [$answersid, $login['id']]);                        
                }
            }
        }
        $this->redirect();
    }

    // get whole answerpanel with answers and comments
    public function getAnswerPanel($answers, $comments, $questionid, $question){
        $login = $this->session->get('login');   
        $answerpanel = "";
        foreach ($answers as $answer) {                
            $answer = $answer->getProperties();
            if(isset($login)){
                $comment_form_a = "
                <div class='form-group'>
                <form action='{$this->url->create('comments/add')}' method='post'>
                <input type=hidden value='{$answer['id']}' name='answerid'>
                <input type=hidden value='$questionid' name='questionid'>
                <input type=hidden value='{$login['id']}' name='userid'>
                <input type='text' class='form-control' name='comment' placeholder='Lämna en kommentar'  required='required'>
                <button type='submit' class='btn btn-primary btn-sm'>Spara</button>
                </form></div>";
            } else {
                $comment_form_a = null;
            }
            $answer_md = $this->textFilter->doFilter($answer['answer'],'shortcode, markdown');
            if($login['acronym'] == $answer['acronym'] || $login['acronym'] == 'admin'){
                $for_user_only = "  
                <a href='{$this->url->create('answers/softDelete')}/{$answer['id']}/{$answer['userid']}'>
                <button style='float: right; padding: 5px;' type='submit' class='btn btn-default btn-sm'>
                <span class=\"glyphicon glyphicon-trash\"></span> Lägg i papperskorg</button></form>
                       
                <a href='{$this->url->create('answers/edit')}/{$answer['id']}/$questionid'>
                <button style='float: right; padding: 5px;' type='button' class='btn btn-default btn-sm'>
                <span class=\"glyphicon glyphicon-pencil\"></span> Ändra</button></a>";
            } else {
                $for_user_only = null;
            }
            if($answer['accepted'] !== NULL){
                $accepted = "accepted";
            } else {
                $accepted = "notaccepted";
            }
            $gravatar_a = get_gravatar($answer['email']); 
            $acronym_a = $answer['acronym'];
            $comments_on_answers = $this->getAnswerComments($answer['id'], $comments, $questionid, $question);
            $res = $this->answers->findRank($answer['id']);
            $rank = $res->rank;
            $vote_answer = "
            <form action='{$this->url->create('answers/vote')}' method='post'>
            <input type='hidden' value='{$answer['id']}' name='answerid'>
            <input type='hidden' value='up' name='vote'>
            <button type='submit'><span class='glyphicon glyphicon-triangle-top'></span></button></form>
            $rank
            <form action='{$this->url->create('answers/vote')}' method='post'>
            <input type='hidden' value='{$answer['id']}' name='answerid'>
            <input type='hidden' value='$questionid' name='questionid'>
            <input type='hidden' value='down' name='vote'>
            <button type='submit'><span class='glyphicon glyphicon-triangle-bottom'></span></button></form>";            
            $ok = "
            <form action='{$this->url->create('answers/ok')}' method='post'>
            <input type='hidden' value='{$answer['id']}' name='answerid'>
            <input type='hidden' value='$questionid' name='questionid'>
            <button type='submit'><span class='glyphicon glyphicon-ok'></span></button></form>";
            $answerpanel .= "  
            <div class='container'> 
                <div class='row'>         
                    <div class='panel panel-default panel-transparent'>      
                        <div class='panel-heading'>
                            
                        </div>   
                        <div class='panel-body'><table><tr>
                            <td class='vote text-center'>$vote_answer</td>
                            <td class='text-center'> 
                            <a  href='{$this->url->create('users/question')}/{$answer['userid']}'>$gravatar_a</a><br>$acronym_a</td>
                            <td style='padding-left:10px'>$answer_md</td>                  
                            </tr></table>
                        </div>        
                        <div class='panel-footer clearfix bottom-buffer'>
                            <div class='$accepted pull-left'>
                                $ok
                            </div>
                            
                            <div class='pull-right'>     
                                $for_user_only
                            </div>
                        </div> 
                        $comments_on_answers   
                        <div class='container'>
                            <div class='rows'>
                                <div class='col-md-12'>
                                    $comment_form_a    
                                </div>
                            </div>
                        </div>                        
                    </div>  
                </div>
            </div>";
        }
        return $answerpanel;
    }

    // for each answer, supply comments
    public function getAnswerComments($answerid, $comments_a, $questionid, $question){
        $question_info = $question->getProperties();
        $login = $this->session->get('login');
        $comments_on_answers = "";
        foreach ($comments_a as $comment) {
            $info_a = $comment->getProperties();
            if($info_a['answerid'] == $answerid){
                // om den som har skrivit kommentaren = den som är inloggad 
                if($info_a['acronym'] == $login['acronym'] || $login['acronym'] == 'admin'){
                    $user_only_a = "
                    <a href='{$this->url->create('comments/softDelete')}/{$info_a['id']}'>
                    <button style='float: right; padding: 5px;' type='button' class='btn btn-default btn-sm'>
                    <span class=\"glyphicon glyphicon-trash\"></span> Lägg i papperskorg</button></a>

                    <a  href='{$this->url->create('comments/edit')}/{$info_a['id']}/$questionid/{$question_info['acronym']}'
                    <button style ='float: right; padding: 5px;' type='button' class='btn btn-default btn-sm'>
                    <span class=\"glyphicon glyphicon-pencil\"></span> Ändra</button></a>";
                } else {
                    $user_only_a = null;
                }
                $comment_md = $this->textFilter->doFilter($info_a['comment'], 'shortcode, markdown');
                $gravatar = get_gravatar($info_a['email']); 
                $acronym = $info_a['acronym'];
                $res = $this->comments->findRank($info_a['id']);
                $rank_a = $res->rank;    
                $vote_comment_a = "
                <form action='{$this->url->create('comments/votecomment')}' method='post'>
                <input type='hidden' value='{$info_a['id']}' name='commentid'>
                <input type='hidden' value='up' name='vote'>
                <button type='submit'><span class='glyphicon glyphicon-triangle-top'></span></button></form>
                $rank_a
                <form action='{$this->url->create('comments/votecomment')}' method='post'>
                <input type='hidden' value='{$info_a['id']}' name='commentid'>
                <input type='hidden' value='down' name='vote'>
                <button type='submit'><span class='glyphicon glyphicon-triangle-bottom'></span></button></form>";                 
                $comments_on_answers .= "
                <div class='container'>
                <div class='rows'>
                <div class='col-md-12'>
                <div class='well well-sm clearfix'>
                <table><tr>
                <td class='vote text-center'>$vote_comment_a</td>
                <td style='padding-right:10px'><a  href='{$this->url->create('users/question')}/{$info_a['userid']}'>$gravatar</a><br>$acronym</td> <td>$comment_md</td>
                </tr></table>
                $user_only_a
                </div></div></div></div>";
             
            
            }
        }return $comments_on_answers;
    }    
    
    /**
    * Delete (soft) answer.
    *
    *
    * @return void
    */
    public function softDeleteAction($answerid, $userid) {
        $login = $this->session->get('login');        

        if($login['acronym'] != 'admin' && $userid != $login['id']){
            $this->redirect();
        } else {
            $andwhere = 'deleted is NULL';
            $now = gmdate('Y-m-d H:i:s');
            $comment = $this->comments->findAnswerComment($answerid, $andwhere);
            if(empty($comment)){
                $params['id'] = $answerid;
                $params['deleted'] = $now;
                $this->answers->updateAnswer($params);
                unset($_SESSION['error']);
            } else {
                $error = "Det här svaret har en kommentar kopplad till sig och kan därför inte tas bort.";
                $this->session->set('error',$error);
            }
            $url = $this->di->url->create($_SERVER['HTTP_REFERER']);
            $this->response->redirect($url);
        }
    }
}

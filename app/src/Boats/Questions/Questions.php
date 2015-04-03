<?php
namespace TBJ\Navigare\Boats\Questions;
 
/**
 * Model for questions.
 *
 */
class Questions extends \TBJ\Navigare\Model\CDatabaseModel {

    /**
     * Add a new question.
     *
     * @param array $question with all details.
     * 
     * @return void
     */
    public function add($question) {           
        $this->save($question);
    }
    public function saveTags2Question($columns,$values){
        $this->db->insert('tags2question',$columns);
        $res = $this->db->execute($values);
        return $res;
    }
    
    public function findTagId($name){
        $this->db->select('boats_tags.id')
            ->from('tags')
            ->where('boats_tags.name = ?');
        $this->db->execute([$name]);
        
        return $this->db->fetchOne();
    }
    
    public function findSpecificQuestion($id){
        $this->db->select('boats_user.acronym, boats_user.id')
            ->from('user')
            ->join('questions','boats_user.id = boats_questions.userid')
            ->where('boats_questions.id = ?');
        $this->db->execute([$id]);
        
        return $this->db->fetchOne();
    }    
    
    
    
    public function findAllNotDeletedQuestions($limit=100) {
          $this->db->select('boats_questions.id, boats_questions.userid, boats_questions.title, boats_questions.question, boats_user.email, boats_user.acronym, boats_user.name')
              ->from('questions')
              ->join('user','boats_user.id = boats_questions.userid')
              ->where('boats_questions.deleted IS NULL')
              ->orderBy('boats_questions.created desc')
              ->limit($limit)
              ->execute();
        $this->db->setFetchModeClass(__CLASS__);

        return $this->db->fetchAll();
    }  
    
    // ta fram alla frågor som en användare har ställt
    public function findRelatedQuestions($type,$id){ 
        $this->db->select('boats_questions.id, boats_questions.userid, boats_questions.title, boats_questions.question, boats_user.email, boats_user.acronym, boats_user.name, boats_tags2question.question_id')
             ->from('questions')
             ->join('tags2question','boats_questions.id=boats_tags2question.question_id')
             ->join('user','boats_questions.userid=boats_user.id')
             ->where($type . " = ?")
             ->andWhere('boats_questions.deleted is NULL');   
        $this->db->execute([$id]);  
        $this->db->setFetchModeClass(__CLASS__);

        return $this->db->fetchAll();
    }  


    /**
     * Find and return all questions.
     *
     * @return array with all questions.
     */
    public function findAllQuestions() {

        $this->db->select()
             ->from($this->getSource())  
             ->orderBy("id desc");
        $this->db->execute();  
        $this->db->setFetchModeClass(__CLASS__);
        return $this->db->fetchAll();

    }

    /**
     * Find and return questions from search.
     *
     * @return array with all questions found.
     */
    public function searchInDatabase($searchtext) {

        $this->db->select('boats_questions.id, boats_questions.userid, boats_questions.title, boats_questions.question, boats_user.email, boats_user.acronym, boats_user.name')
              ->from('questions')
              ->join('user','boats_user.id = boats_questions.userid')
             ->where("LENGTH('{$searchtext}') > 1")
             ->andWhere("boats_questions.question LIKE '%{$searchtext}%' OR title LIKE '%{$searchtext}%'")
             ->andWhere('boats_questions.deleted is NULL')
             ->orderBy("boats_questions.created desc");
        $this->db->execute();  
        $this->db->setFetchModeClass(__CLASS__);
        return $this->db->fetchAll();

    }
    
    /**
     * Find and return questions w/o answers.
     *
     * @return array with all questions found.
     */
    public function noAnswers() {

        $this->db->select('boats_questions.id, boats_questions.userid, boats_questions.title, boats_questions.question, boats_user.email, boats_user.acronym, boats_user.name')
              ->from('questions')
              ->join('user','boats_user.id = boats_questions.userid')
             ->where("boats_questions.id NOT IN 
                 (SELECT boats_answers.questionid FROM boats_answers)")
             ->andWhere('boats_questions.deleted is NULL')
             ->orderBy("boats_questions.created desc");
        $this->db->execute();  
        $this->db->setFetchModeClass(__CLASS__);
        return $this->db->fetchAll();

    }    
    
    
    /**
     * Find and return softdeleted questions
     *
     * @return array with all questions found.
     */
    public function softDeletedQuestions() {

        $this->db->select('boats_questions.id, boats_questions.userid, boats_questions.title, boats_questions.question, boats_user.email, boats_user.acronym, boats_user.name')
              ->from('questions')
              ->join('user','boats_user.id = boats_questions.userid')
             ->where('boats_questions.deleted is NOT NULL') 
             ->orderBy("boats_questions.deleted desc");
        $this->db->execute();  
        $this->db->setFetchModeClass(__CLASS__);
        return $this->db->fetchAll();

    }     
    


    // ta fram ett specifikt svar med user-info
    public function findQuestion($type, $id){ 
        $this->db->select('boats_questions.id, boats_questions.userid, boats_questions.title, boats_questions.question, boats_user.email, boats_user.acronym, boats_user.name')
             ->from('questions')
             ->join('user','boats_user.id=boats_questions.userid')
             ->where($type . " = ?")
             ->andWhere('boats_questions.deleted is NULL');
        $this->db->execute([$id]);  
        $this->db->setFetchModeClass(__CLASS__);

        return $this->db->fetchOne();
    }
    
    // ta fram alla frågor som en användare har ställt
    public function findAllUserQuestions($type, $id){ 
        $this->db->select('boats_questions.id as questionid, boats_questions.question, boats_questions.userid, boats_questions.title,boats_user.acronym,boats_user.email,boats_user.id')
             ->from('questions')
             ->join('user','boats_user.id=boats_questions.userid')
             ->where($type . " = ?")
             ->andWhere('boats_questions.deleted is NULL');   
        $this->db->execute([$id]);  
        $this->db->setFetchModeClass(__CLASS__);

        return $this->db->fetchAll();
    }  
    
    // ta fram alla frågor som en användare har besvarat
    public function findAllAnsweredUserQuestions($type, $id){ 
        $this->db->select('boats_questions.id as questionid, boats_questions.question, boats_questions.userid, boats_questions.title,boats_user.acronym,boats_user.email')
             ->from('questions')
             ->join('answers','boats_answers.questionid=boats_questions.id')
             ->join('user','boats_user.id=boats_questions.userid')
             ->where($type . " = ?")
             ->andWhere('boats_questions.deleted is NULL');   
        $this->db->execute([$id]);  
        $this->db->setFetchModeClass(__CLASS__);

        return $this->db->fetchAll();
    }     
        
 
    // uppdatera en fråga efter ändring
    public function updateQuestion($params){

           $this->save($params);
    }
    
    /**
    *Deletes a question permenantly
    **/

    public function deleteQuestion($id){
        $this->delete($id);

    }
    
    /**
     * Find and return all users questions.
     * @return array with all relevant questions.
     */
    public function findUserQuestions($type, $id) {

        $this->db->select()
             ->from('questions')        
             ->where($type . " = ?");              
        $this->db->execute([$id]);  
        $this->db->setFetchModeClass(__CLASS__);

        return $this->db->fetchAll();

    } 
    
    /**
     * Find and return rank for question.
     * @return object with rank.
     */
    public function findRank($id) {

        $this->db->select('boats_questions.rank')
             ->from('questions')        
             ->where('boats_questions.id' . " = ?");              
        $this->db->execute([$id]);  
        $this->db->setFetchModeClass(__CLASS__);

        return $this->db->fetchOne();
    }
    
    /**
     * Find and return rank for questions made by user.
     * @return object with rank.
     */
    public function findUserRank($userid) {

        $this->db->select('sum(boats_questions.rank) as "summa", count(boats_questions.rank) as "antal"')
             ->from('questions')        
             ->where('boats_questions.userid' . " = ?");              
        $this->db->execute([$userid]);  
        $this->db->setFetchModeClass(__CLASS__);

        return $this->db->fetchAll();
    }     
    
    // ta fram en specifik fråga
    public function findQuestionById($id){ 
            $res = $this->query()
                ->where("id = ?")  
                ->execute([$id]);  
            return $res;
    }    
    
    /**
     * Find and return votes made by user.
     * @return array with all votes and users.
     */
    public function findVotes($id) {

        $this->db->select()
             ->from('votes')        
             ->where('boats_votes.votedby' . " = ?");              
        $this->db->execute([$id]);  
        $this->db->setFetchModeClass(__CLASS__);

        return $this->db->fetchAll();
    }   

    public function vote($columns,$values){
        $this->db->insert('votes',$columns);
        $res = $this->db->execute($values);
        return $res;
    }      
}



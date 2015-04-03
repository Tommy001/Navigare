<?php
namespace TBJ\Navigare\Boats\Comments;
 
/**
 * Model for Comments.
 *
 */
class Comments extends \TBJ\Navigare\Model\CDatabaseModel {

    /**
     * Add a new comment.
     *
     * @param array $comment with all details.
     * 
     * @return void
     */
    public function add($comment) {
        $this->save($comment);
    }


    /**
     * Find and return all comments.
     * Check if it is a comment for an answer or a question
     * andWhere contains extra stmt to only fetch answer comments
     * @return array with all relevant comments.
     */
    public function findAllAnswerComments($type, $id) {

        $this->db->select('boats_user.acronym,boats_user.email,boats_comments.comment,boats_comments.id, boats_comments.answerid,boats_comments.userid')
             ->from('comments')
             ->join('user','boats_user.id=boats_comments.userid')
             ->where($type . " = ?") 
             ->andWhere('boats_comments.deleted is NULL')
             ->andWhere('boats_comments.questionid is NOT NULL')
             ->andWhere('boats_comments.answerid is NOT NULL'); 
        $this->db->execute([$id]);  
        $this->db->setFetchModeClass(__CLASS__);

        return $this->db->fetchAll();

    }
    
    /**
     * Find and return all question comments.
     * @return array with all relevant comments.
     */
    public function findAllComments($type, $id) {

        $this->db->select('boats_user.acronym,boats_user.email,boats_comments.comment,boats_comments.id, boats_comments.userid')
             ->from('comments')
             ->join('user','boats_user.id=boats_comments.userid')
             ->where($type . " = ?") 
             ->andWhere('boats_comments.deleted is NULL')
             ->andWhere('boats_comments.answerid is NULL');             
        $this->db->execute([$id]);  
        $this->db->setFetchModeClass(__CLASS__);

        return $this->db->fetchAll();

    }    
    
    /**
     * Find and return all users comments.
     * @return array with all relevant comments.
     */
    public function findUserComments($type, $id) {

        $this->db->select()
             ->from('comments')
             ->where($type . " = ?");              
        $this->db->execute([$id]);  
        $this->db->setFetchModeClass(__CLASS__);

        return $this->db->fetchAll();

    }    
    
    public function findAnswersId($id) {
        $this->db->select('boats_comments.answerid')
            ->from('comments')
            ->where('boats_comments.questionid = ?');
        $this->db->execute([$id]);

        return $this->db->fetchAll();
    }    
    
    public function updateComment($params){
           $this->save($params);
    }    


    /**
    *Deletes a comment permenantly
    **/

    public function deleteComment($id){
        $this->delete($id);

    }

    // har svaret en kommentar kopplad till sig?
    // anropas från AnswersController
    public function findAnswerComment($id, $andwhere){ 
            $res = $this->query()
                ->where("answerid = ?")
                ->andWhere($andwhere)
                ->execute([$id]);  
            return $res;
    }
    // har frågan en kommentar kopplad till sig?
    // anropas från QuestionsController
    public function findQuestionComment($id, $andwhere){ 
            $res = $this->query()
                ->where("questionid = ?")
                ->andWhere($andwhere)               
                ->execute([$id]);  
            return $res;
    }    
    
    public function findComment($id){ 
            $res = $this->query()
                ->where("id = ?")
                ->execute([$id]);  
            return $res;
    } 
    
    /**
     * Find and return rank for comment.
     * @return object with rank.
     */
    public function findRank($id) {

        $this->db->select('boats_comments.rank')
             ->from('comments')        
             ->where('boats_comments.id' . " = ?");              
        $this->db->execute([$id]);  
        $this->db->setFetchModeClass(__CLASS__);

        return $this->db->fetchOne();
    }
    
    /**
     * Find and return rank for comments made by user.
     * @return object with rank.
     */
    public function findUserRank($userid) {

        $this->db->select('sum(boats_comments.rank) as "summa", count(boats_comments.rank) as "antal"')
             ->from('comments')        
             ->where('boats_comments.userid' . " = ?");              
        $this->db->execute([$userid]);  
        $this->db->setFetchModeClass(__CLASS__);

        return $this->db->fetchAll();
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



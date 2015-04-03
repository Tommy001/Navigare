<?php
namespace TBJ\Navigare\Boats\Answers;
 
/**
 * Model for Answers.
 *
 */
class Answers extends \TBJ\Navigare\Model\CDatabaseModel {

    /**
     * Add a new answer.
     *
     * @param array $answer with all details.
     * 
     * @return void
     */
    public function add($answer)
    {
        $this->save($answer);
    }



    /**
     * Find and return all answers.
     *
     * @return array with all answers.
     */
    public function findAllAnswers($type, $id, $andwhere) {

        $this->db->select('boats_user.acronym,boats_user.email,boats_answers.answer,boats_answers.id,boats_answers.userid, boats_answers.accepted')
             ->from('answers')
             ->join('user','boats_user.id=boats_answers.userid')
             ->where($type . " = ?")   
             ->andWhere($andwhere);   
        $this->db->execute([$id]);  
        $this->db->setFetchModeClass(__CLASS__);

        return $this->db->fetchAll();

    }


    // ta fram ett specifikt svar
    public function findAnswer($id){ 
            $res = $this->query()
                ->where("id = ?")  
                ->execute([$id]);  
            return $res;
    }
        


    // spara ett svar efter ändring
    public function updateAnswer($params){

           $this->save($params);
    } 

    /**
    *Deletes an answer permenantly
    **/

    public function deleteAnswer($id){
        $this->delete($id);

    }    
    
    /**
     * Find and return all users answers.
     * @return array with all relevant answers.
     */
    public function findUserAnswers($type, $id) {

        $this->db->select()
             ->from('answers')        
             ->where($type . " = ?");              
        $this->db->execute([$id]);  
        $this->db->setFetchModeClass(__CLASS__);

        return $this->db->fetchAll();

    }
    
    public function vote($columns,$values){
        $this->db->insert('votes',$columns);
        $res = $this->db->execute($values);
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
    
    /**
     * Find and return rank for answer.
     * @return array with rank.
     */
    public function findRank($id) {

        $this->db->select('boats_answers.rank')
             ->from('answers')        
             ->where('boats_answers.id' . " = ?");              
        $this->db->execute([$id]);  
        $this->db->setFetchModeClass(__CLASS__);

        return $this->db->fetchOne();
    }
    
    /**
     * Find and return rank for answers made by specific user.
     * @return array with rank.
     */
    public function findUserRank($userid) {

        $this->db->select('sum(boats_answers.rank) as "summa", count(boats_answers.rank) as "antal"')
             ->from('answers')        
             ->where('boats_answers.userid' . " = ?");              
        $this->db->execute([$userid]);  
        $this->db->setFetchModeClass(__CLASS__);

        return $this->db->fetchAll();
    }     
}



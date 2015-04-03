<?php
namespace TBJ\Navigare\Boats\Users;
 
/**
 * Model for Users.
 *
 */
class User extends \TBJ\Navigare\Model\CDatabaseModel {
    /**
    * Find and return specific.
    * Från rad 70 i CDabaseModel, $id är ersatt med $acronym
    * 
    * @return this
    */
    public function findAcronym($acronym)
    {
      $this->db->select()
               ->from($this->getSource())
               ->where("acronym = ?");

      $this->db->execute([$acronym]);
      return $this->db->fetchInto($this);
    }    
    
    public function findEmail($email)
    {
      $this->db->select()
               ->from($this->getSource())
               ->where("email = ?");

      $this->db->execute([$email]);
      return $this->db->fetchInto($this);
    }
         
    
   public function findActiveUser(){

        $this->db->select('userid, sum(`COUNT(boats_comments.userid)`) as "Count", acronym, email')
             ->from('activeuser')
             ->groupBy('userid, acronym')
             ->orderBy('Count DESC')
             ->limit(4);
        $this->db->execute();  
        $this->db->setFetchModeClass(__CLASS__);

        return $this->db->fetchAll();
       
   }  
      public function findNumberOfComments($id){

        $this->db->select('COUNT(boats_comments.userid) as "Count"')
             ->from('comments')
             ->where('userid = ?')
             ->andWhere('deleted is NULL');
        $this->db->execute([$id]);  
        $this->db->setFetchModeClass(__CLASS__);

        return $this->db->fetchAll();
       
   } 
      public function findNumberOfQuestions($id){

        $this->db->select('COUNT(boats_questions.userid) as "Count"')
             ->from('questions')
             ->where('userid = ?')
             ->andWhere('deleted is NULL');
        $this->db->execute([$id]);   
        $this->db->setFetchModeClass(__CLASS__);

        return $this->db->fetchAll();
       
   } 
      public function findNumberOfAnswers($id){

        $this->db->select('COUNT(boats_answers.userid) as "Count"')
             ->from('answers')
             ->where('userid = ?')
             ->andWhere('deleted is NULL');
        $this->db->execute([$id]);  
        $this->db->setFetchModeClass(__CLASS__);

        return $this->db->fetchAll();
       
   } 
}



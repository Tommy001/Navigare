<?php
namespace TBJ\Navigare\Boats\Tags;
 
/**
 * Model for Tags.
 *
 */
class Tags extends \TBJ\Navigare\Model\CDatabaseModel {
    /**
    * Find and return specific.
    * Från rad 70 i CDabaseModel, $id är ersatt med $acronym
    * 
    * @return this
    */
    public function findTag($id)
    {
      $this->db->select()
               ->from($this->getSource())
               ->where("id = ?");

      $this->db->execute([$id]);
      return $this->db->fetchInto($this);
    }
    
    public function findTagName($name)
    {
      $this->db->select()
               ->from($this->getSource())
               ->where("name = ?");

      $this->db->execute([$name]);
      return $this->db->fetchInto($this);
    }      
    
   public function findPopularTags(){

        $this->db->select('boats_tags2question.tag_id, COUNT(1),boats_tags.name')
             ->from('tags2question')
             ->join('tags', 'boats_tags.id = boats_tags2question.tag_id')
             ->groupBy('boats_tags2question.tag_id')
             ->orderBy('COUNT(1) DESC')
             ->limit(5);
        $this->db->execute();  
        $this->db->setFetchModeClass(__CLASS__);

        return $this->db->fetchAll();
       
   }

    public function findAllNotDeletedTags() {
          $all = $this->query()
              ->where('deleted IS NULL')
              ->orderBy('name ASC')
              ->execute();
         return $all;
    }
    
    public function findTagNames(){
        $this->db->select('boats_tags.name')
             ->from('tags');
        $this->db->execute();  
        $this->db->setFetchModeClass(__CLASS__);

        return $this->db->fetchAll();
    }
    public function deleteTag2Questions($questionid){
         $this->db->delete('tags2question', 'question_id = ?');
         $this->db->execute([$questionid]);
    }
    
    public function findTagTitles($table,$id){
        $this->db->select('boats_tags.name, boats_tags.id')
             ->from('tags')
             ->join('tags2question','boats_tags.id=boats_tags2question.tag_id')
             ->where($table . " = ?");   
        $this->db->execute([$id]);  
        $this->db->setFetchModeClass(__CLASS__);

        return $this->db->fetchAll();
    }

}

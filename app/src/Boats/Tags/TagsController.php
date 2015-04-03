<?php
namespace TBJ\Navigare\Boats\Tags;
 
/**
 * A controller for tags related events.
 *
 */
class TagsController implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;
 
    protected $tags;
    protected $form;
    protected $questions;
/**
 * Initialize the controller.
 *
 * @return void
 */
public function initialize()
{
    $this->tags = new \TBJ\Navigare\Boats\Tags\Tags();
    $this->tags->setDI($this->di);
    $this->form = new \Mos\HTMLForm\CForm();
    $this->questions = new \TBJ\Navigare\Boats\Questions\Questions();
    $this->questions->setDI($this->di);    
}

/**
 * List all tags.
 *
 * @return void
 */
public function listAction() {
 
    $tags = $this->tags->findAllNotDeletedTags();
    $this->theme->addStylesheet('css/override.css'); 
    $this->theme->setTitle("Ämnen");       
    $this->views->add('boats/list-all-tags', [
        'tags' => $tags,
        'title' => "Ämnen",       
    ]);   
}

    /**
    * List tag with id and related questions.
    *
    * @param int $id of tag to display
    *
    * @return void
    */
    public function idAction($id = null) { 
        $this->initialize();
        if(!isset($id)){
                $id = $this->request->getPost('tagid');
        }
        $tag = $this->tags->find($id);
        if(isset($tag)){
            $questions = $this->questions->findRelatedQuestions('boats_tags2question.tag_id',$id);
            $this->theme->setTitle("Ämnen");   
            $this->views->add('boats/view_tags', [
                'tag' => $tag,
                'title' => 'Ämnen',
                'questions' => $questions,
            ]);
        } else {
            $this->redirect();
        }
    }

    public function findAllActiveTags(){
        $this->initialize();
        $res = $this->tags->findTagNames();
        return $res;
    }
    
    public function popularAction(){
        $pops = $this->tags->findPopularTags();
        $html = "";
        foreach ($pops as $pop) {
            $info = $pop->getProperties();
            $html .= "
            <form action='{$this->url->create('tags/id')}' method='post'> 
            <input type=hidden value='{$info['tag_id']}' name='tagid'>
            <button class='btn btn-default btn-lg btn-block'>
            <span class='pull-left glyphicon glyphicon-tags'></span>  
            <strong> {$info['name']}</strong>              
            </button></form><br>";
        }
            
        return $html;
    }
    
    public function deleteRelations($questionid){
        
         $this->tags->deleteTag2Questions($questionid);
    }
        

    public function findTags($table,$id){
        $this->initialize();
        $tags = $this->tags->findTagTitles($table,$id); 
        $html = "";
        foreach($tags as $val){
            $tag = $val->getProperties();
            $url = $this->url->create('tags/id');
            $html .= "
                <div class='pull-left'>
                <form action='$url' method='post'>
                <input type='hidden' value='{$tag['id']}' name='tagid'>
                <button class='btn btn-default'>
                <span class='glyphicon glyphicon-tags'> </span> 
                    {$tag['name']}              
                </button>
                </form>
                </div>";
        }
        return $html;
    }
    
    public function findChecked($table,$id){
        $this->initialize();        
        $tags = $this->tags->findTagTitles($table,$id);
        $checked = array();
        foreach($tags as $key => $tag){
            $res = $tag->getProperties();
            $checked[] = $res['name'];
        }
        return $checked;
    }

    
/**
 * Update user.
 *
 * @param string $acronym of user to update.
 *
 * @return void
 */
    public function createUpdateForm($name) {

        $form = $this->form->create([], [
            'name' => [
                'class'       => 'form-control',
                'type'        => 'text',
                'label'       => 'Ämnesområde:',
                'value'       => $name,
                'required'    => true,
                'validation'  => ['not_empty','custom_test' => 
                                 ['message' => '<div class="alert alert-danger" role="alert">
                                 <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                                 <span class="sr-only">Error:</span>
                                 Det ämnesområdet finns redan
                                 </div>',
                                 'test' => array($this, 'addDuplicateTag')
                       ]
                       ]
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
        return $form;
    }

    private function redirect(){
        if($this->session->has('url')) {
        $url = $this->session->get('url'); // holds url for last page
        } else { 
            $url = $this->url->asset('tags/list'); 
        }
        header("Location: $url");  
    }     

    public function updateAction($id=null) {
        $login = $this->session->get('login');
        if($login['acronym'] != 'admin'){
            $this->redirect();
        } else {
            $tag = $this->tags->findTag($id);
            $info = $tag->getProperties();
            $name = isset($info['name']) ? $info['name'] : null;
            $formular = $this->createUpdateForm($name);
            $form = $this->form->getHTML();          
            $status = $this->form->check();

            if ($status === true) {
                $this->tags->save([
                    'id' => $id,
                    'name' => $this->form->value('name'),               
                ]);

                $url = $this->url->create('tags/id/' . $this->tags->id);
                $this->response->redirect($url);

            } else if ($status === false) {
                $this->form->AddOutput("<h3 class='text-color'>Kontrollera felmeddelandena ovan.</h3>");
                $form = $this->form->getHTML(); 
            }
        
 
            $this->theme->setTitle('Uppdatera ämne');    
            $this->views->add('boats/add_tags', [
                'content' => $form,  
                'title' => 'Uppdatera ämne'
            ]);              
            $list = $this->url->create('list');
            $this->views->addString("<p><a href='"."$list"."'><i class='fa fa-eye'></i> Visa alla</a></p>", "triptych-1");          
        }
    }
    
  
   
    public function addDuplicateTag($tag) {

        $res = $this->tags->findTagName($tag);

        return empty($res);
    }    
}

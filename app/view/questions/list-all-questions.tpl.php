<?php
$url = $this->request->getCurrentUrl();
$this->session->set('url',$url);
$login = $this->session->get('login');
$error = $this->session->get('error');
$logged_in = $login['id'];
$trash = null;
$tags = new \TBJ\Navigare\Boats\Tags\TagsController();
$tags->setDI($this->di);  
if(isset($error)):?>
    <script>
    alert('<?php echo $error; ?>');
    </script>
    <?php 
    unset($_SESSION['error']);
endif;
?>
<div class="container">
    <div class="row">
        <h1 class='title-color'><?=$title?></h1>
        <div class='panel panel-default panel-transparent'>
            <div class='panel-body'>        
                <div>
                    <ul class="nav nav-tabs right-to-left">
                        <li role="presentation"  class="<?=$active?>">
                        <a href="<?=$this->url->create('questions/list')?>">
                        Aktiva</a>
                        </li>
                        <li role="presentation"  class="<?=$trashed?>">
                        <a href="<?=$this->url->create('questions/softdeleted')?>">
                        Papperskorgen</a>
                        </li>
                        <li role="presentation"  class="<?=$noanswer?>">
                        <a href="<?=$this->url->create('questions/noanswers')?>">
                        Obesvarade</a>
                        </li> 
                        <li role="presentation"  class="<?=$searchresult?>">
                        <a href="<?=$this->url->create('questions/search')?>">
                        Sökresultat</a>
                        </li>                         
                    </ul>
                </div>        
                <?php $url_delete_trash = $this->url->create('questions/deleteTrash'); 
                foreach ($questions as $question) {
                    $info = $question->getProperties();
                    $taggar = $tags->findTags('boats_tags2question.question_id',$info['id']);
                    $acronym_q = $info['acronym']; // den som har skrivit frågan
                    $gravatar = get_gravatar($info['email']);
                    $userid = $info['userid']; // den som har skrivit frågan
                    $url_user = $this->url->create('users/question/' . $userid);
                    $question_md = $this->textFilter->doFilter($info['question'], 'shortcode, markdown');        
                    $url_id = $this->url->create('questions/id/' . $info['id']);
                    $url_update = $this->url->create('questions/edit/' . $info['id']);
                    $url_delete = $this->url->create('questions/softDelete/' . $info['id'].'/'.$info['userid']);
                    $url_restore = $this->url->create('questions/restore/' . $info['id']); 
                    $url_empty = $this->url->create('questions/delete/' . $info['id']);    
                    $links = null;
                    if(isset($trashed) && $login['acronym'] == 'admin'){
                        $links = "
                        <div class='clearfix'>
                        <a href=\"$url_restore\">
                        <button style='float: right; padding: 5px;' type='button' class='btn btn-default btn-sm'>
                        <span class=\"glyphicon glyphicon-share-alt\"></span>
                        Ångra borttagning</button></a>
                        
                        <a href=\"$url_empty\">
                        <button style='float: right; padding: 5px; margin-right:5px;' type='button' class='btn btn-default btn-sm'>
                        <span class=\"glyphicon glyphicon-trash\"></span>
                        Ta bort definitivt</button></a>
                        </div>";
                        
                       $trash = "
                       <form action='$url_delete_trash' method='post'>
                       <input type=hidden value='{$info['id']}' name='questionid'>
                       <button type='submit' class='btn btn-primary btn-sm'><span class=\"glyphicon glyphicon-trash\"></span> Töm papperskorgen</button>
                       </form>";  
                    }
                    if(!isset($trashed)){
                        if($login['id'] == $userid || $login['acronym'] == 'admin'){
                            $links = "<div class='clearfix'> 
                            <a href='{$this->url->create('questions/softDelete')}/{$info['id']}/{$info['userid']}'>
                            <button style='float: right; padding: 5px;' type='submit' class='btn btn-default btn-sm'>
                            <span class=\"glyphicon glyphicon-trash\"></span> Lägg i papperskorg</button></form>
                            <a href='{$this->url->create('questions/edit')}/{$info['id']}'>
                            <button style='float: right; padding: 5px;' type='button' class='btn btn-default btn-sm'>
                            <span class=\"glyphicon glyphicon-pencil\"></span> Ändra</button></a>
                            </div>";
                        }
                    } 
                    if(!isset($trashed)) {
                        $heading = "<a href='$url_id'><h3>{$info['title']}</h3></a>";
                    } else {
                        $heading = "<h3>{$info['title']}</h3>";
                    } 
                    if(isset($answer)){
                        $answer = $info['answer'];
                    }           
    
                    echo <<<EOD
                    <div class="panel panel-default">
                        <div class='panel-heading'>
                            $heading
                            </div>
                        <div class='panel-body'><table><tr>    
                             <td style='padding-right:10px'><a href='$url_user'>$gravatar</a><br>$acronym_q</td>
                             <td>$question_md</td>
                             </tr></table>
                        </div>        
                        <div class='clearfix panel-footer bottom-buffer'> 
                            $taggar
                            <div class='pull-right'>
                                $links
                            </div>              
                        </div>
                    </div>
EOD;

               } ?>
               <div class='pull-right clear-both'>
                   <?php
                   if(isset($trashed)){
                       echo $trash;
                   }?>
              </div>    
         </div>        
    </div> 
</div>

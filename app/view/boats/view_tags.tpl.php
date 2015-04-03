<?php
$login = $this->session->get('login');
$error = $this->session->get('error');

$tags = new \TBJ\Navigare\Boats\Tags\TagsController();
$tags->setDI($this->di); 

$info = $tag->getProperties();
$name = isset($info['name']) ? $info['name'] : null; 
$id = isset($info['id']) ? $info['id'] : null;    
$keywords = isset($info['keywords']) ? $info['keywords'] : null;        

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
        <div class="col-md-4">
        <h1 class='title-color'><?=$title?></h1>
            <div class='panel panel-default panel-transparent'>      
                <div class='panel-body'>            
                <h2><?= "<button class='btn btn-default btn-lg'>
                <span class='glyphicon glyphicon-tags'> </span> 
                    {$info['name']}              
                </button>" ?></h2>
                <div class='pull-right'>
                    <a href='<?=$this->url->create('tags/list')?>'><button class='btn btn-primary btn-sm'>Visa alla</button></a>
                </div>
                <?php
                if($login['acronym'] == 'admin'):?>
                    <div class='pull-right'>
                        <a href='<?=$this->url->create('tags/update/'.$id)?>'><button style='margin-right:5px;' class='btn btn-primary btn-sm'><span class="glyphicon glyphicon-pencil"></span> Ändra</button></a>
                    </div>    
                <?php endif; ?>    
                </p>
                </div>
            </div>
        </div>
    </div> 
</div>        
        
<div class="container">
    <div class="row">
        <div class='title-color'><h2>Frågor som har ställts i det här ämnet</h2></div>
        <?php foreach ($questions as $question) {

        $info = $question->getProperties();
        $gravatar = get_gravatar($info['email']); 
        $acronym_q = $info['acronym']; // den som har skrivit frågan
        $taggar = $tags->findTags('boats_tags2question.question_id',$info['id']);  
        $acronym_q = $info['acronym']; // den som har skrivit frågan
        $question_md = $this->textFilter->doFilter($info['question'], 'shortcode, markdown');        
        $url_id = $this->url->create('questions/id/' . $info['question_id']);
        $url_update = $this->url->create('questions/update/' . $info['question_id']);
        $url_delete = $this->url->create('questions/softDelete/' . $info['question_id']);
        $url_restore = $this->url->create('questions/restore/' . $info['question_id']); 
        $url_empty = $this->url->create('questions/delete/' . $info['question_id']);    
        $links = null;

        // om den som är inloggad har skrivit frågan eller är admin
        if($login['acronym'] == $acronym_q || $login['acronym'] == 'admin'){
            $links = " 
            <a href='{$this->url->create('questions/softDelete')}/{$info['question_id']}/{$info['userid']}'>
            <button style='float: right; padding: 5px;' type='submit' class='btn btn-default btn-sm'>
            <span class=\"glyphicon glyphicon-trash\"></span> Lägg i papperskorg</button></form>
            <a href='{$this->url->create('questions/edit')}/{$info['question_id']}'>
            <button style='float: right; padding: 5px;' type='button' class='btn btn-default btn-sm'>
            <span class=\"glyphicon glyphicon-pencil\"></span> Ändra</button></a>";
        } else {
            $user_only_q = null;
        }    
        if(isset($answer)){
            $answer = $info['answer'];
        }           
    
    echo <<<EOD
        <div class='panel panel-default panel-transparent'>
           
            <div class='panel-heading'>
                <a class "rubrik" href='$url_id'><h3>{$info['title']}</h3></a> 
            </div>   
            <div class='panel-body'><table><tr>    
                <td style='padding-right:10px'><a  href='{$this->url->create('users/question')}/{$info['userid']}'>$gravatar</a><br>$acronym_q</td>
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
    <div class='pull-left clear-both'>
    <?php
    if(isset($trashed)){
        $links = "<a href=\"$url_delete_trash\">Töm papperskorgen</a>";   
        echo $links;
    }
    ?>
    </div> 
</div>

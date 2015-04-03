<?php
$url = $this->request->getCurrentUrl();
$this->session->set('url',$url);
$login = $this->session->get('login');
$status = null;
$info = $user->getProperties();
if($login['acronym'] != 'admin'){
    $status = "";
    if($info['active'] == null && $info['deleted'] == null) {
        $status = 'Inaktiv';
    } else if( $info['deleted'] != null ) {
        $status = 'Borttagen (' . $info['deleted'] . ')';
    } else {
        $status = 'Aktiv (' . $info['active'] . ')';
    }
}    
if($login['acronym'] == 'admin'){
    $status = "";
    if($info['active'] == null && $info['deleted'] == null) {
        $url = $this->url->create('users/activate/' . $info['id']);
        $status = 'Inaktiv | <a href="'.$url.'">Aktivera</a>';
    } else if( $info['deleted'] != null ) {
        $url = $this->url->create('users/restore/' . $info['id']);
        $status = 'Borttagen (' . $info['deleted'] . ') | <a href="'.$url.'">Återställ</a>';
    } else {
        $url = $this->url->create('users/deactivate/' . $info['id']);
        $status = 'Aktiv (' . $info['active'] . ') | <a href="'.$url.'">Inaktivera</a>';
    }
}
$url_update = null;
$editlink = null;
if($login['acronym'] == $info['acronym'] || $login['acronym'] == 'admin'){
    $url_update = $this->url->create('users/update/' . $info['id']);
    $editlink = "
    <div class='pull-right'><a href=\"$url_update\"><button type='button' class='btn btn-default'><span class=\"glyphicon glyphicon-pencil\"></span> Ändra profilen</button></a></div>";
}
$deletelink = null;
if($login['acronym'] == 'admin'){
    $url_delete = $this->url->create('users/softDelete/' . $info['id']);
    $deletelink = "
    <div style='clear:both; margin-top:5px' class='pull-right'><a href=\"$url_delete\"><button type='button' class='btn btn-default'><span class=\"glyphicon glyphicon-pencil\"></span> Ta bort</button></a></div>";
}
$name = isset($info['name']) ? $info['name'] : null; 
$acronym = isset($info['acronym']) ? $info['acronym'] : null;     
$email = isset($info['email']) ? $info['email'] : null; 
$phone = isset($info['phone']) ? $info['phone'] : null; 
$gravatar = get_gravatar_big($info['email']);
?>

<div class="container">
    <h1 class='title-color'><?=$title?></h1>
    <div class="row">
        <div class="col-md-6">
            <div class='panel panel-default panel-transparent'>      
                <div class='panel-body'> 
                    <?= $editlink?>
                    <?= $deletelink?>
                    <div style='clear:both;' class='pull-right'><h4>Meritpoäng: <?= $reputation ?></h4></div>
                    <h2>Medlem <?= $info['id'] ?>: <?= $info['name'] ?></h2>
                    <table><tr>
                    <td class='text-center'><?=$gravatar?><br>
                    <?= $acronym ?></td>
                    <td><ul>
                        <li>Användarnamn: <?= $acronym ?></li>
                        <li>Namn: <?= $name ?></li>
                        <li>Status: <?= $status ?></li>
                        <li>E-post: <?= $email ?></li>
                        <li>Skapad: <?= $info['created'] ?></li>
                        <li>Frågor: <?= $num_questions ?></li>
                        <li>Svar: <?= $num_answers ?></li>
                        <li>Kommentarer: <?= $num_comments ?></li>
                        
                    </ul></td>
                    </tr></table> 
                </div>
            </div>
        </div>
    </div>        
        <div class='panel panel-default panel-transparent'>      
            <div class='panel-body'> 
                <ul class="nav nav-tabs right-to-left">
                    <li role="presentation"  class="<?=$answ?>">
                        <a href="<?=$this->url->create('users/answered/'.$info['id'])?>">
                        Frågor som jag har svarat på</a>
                    </li>
                    <li role="presentation"  class="<?=$quest?>">
                        <a href="<?=$this->url->create('users/question/'.$info['id'])?>">
                        Frågor som jag har ställt</a>
                    </li>
                </ul>   
                <?php if(isset($questions)): ?>
                <h2>Ställda frågor</h2>
                <?php foreach($questions as $question){
                        $info = $question->getProperties();
                        $question = $this->textFilter->doFilter($info['question'],'shortcode, markdown');                   
                        $gravatar = get_gravatar($info['email']);
                        $acronym = $info['acronym'];
                        echo <<<EOD

                   <div class="panel panel-default">      
                       <div class='panel-heading'>
                            <a href='{$this->url->create('questions/id')}/{$info['questionid']}'><h2>{$info['title']}</h2></a>
                       </div>   
                       <div class='panel-body text-center'><table><tr>
                           <td style='padding-right:10px;'><a  href='{$this->url->create('users/question')}/{$info['userid']}'>$gravatar</a><br>$acronym</td>
                           <td>$question</td>                  
                       </tr></table></div>                           
                   </div>
EOD;
           }
           endif;
        
           if(isset($answered)):?>    
        
               <h2>Besvarade frågor</h2>
               <?php foreach($answered as $answer){
                   $info = $answer->getProperties();
                   $question = $this->textFilter->doFilter($info['question'],'shortcode, markdown');                   
                   $gravatar = get_gravatar($info['email']);
                   $acronym = $info['acronym'];

                   echo <<<EOD

                   <div class="panel panel-default">      
                       <div class='panel-heading'>
                            <a href='{$this->url->create('questions/id')}/{$info['questionid']}'><h2>{$info['title']}</h2></a>
                       </div>   
                       <div class='panel-body text-center'><table><tr>
                           <td style='padding-right:10px;'><a  href='{$this->url->create('users/question')}/{$info['userid']}'>$gravatar</a><br>$acronym</td>
                           <td>$question</td>                  
                       </tr></table></div>                           
                   </div>
EOD;
        }
        endif; 
        ?>         
    </div>
</div>

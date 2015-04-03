<?php
$url = $this->request->getCurrentUrl();
$this->session->set('url',$url);
$login = $this->session->get('login');
$error = $this->session->get('error');
$info = $question->getProperties();
$question_md = $this->textFilter->doFilter($info['question'], 'shortcode, markdown');
$question_comment = null;
$comment_form = null;
$answer_button = null;
$gravatar = get_gravatar($info['email']); 
$logged_in_acronym = $login['acronym'];
$logged_in_gravatar = get_gravatar($login['email']);
$acronym_q = $info['acronym']; // den som har skrivit frågan
$tagname = $info['name'];
$question = isset($info['question']) ? $info['question'] : null; 
$id = isset($info['id']) ? $info['id'] : null;   
$vote_question = "
<form action='{$this->url->create('questions/vote')}' method='post'>
<input type='hidden' value='{$info['id']}' name='questionid'>
<input type='hidden' value='up' name='vote'>
<button type='submit'><span class='glyphicon glyphicon-triangle-top'></span></button></form>
$rank
<form action='{$this->url->create('questions/vote')}' method='post'>
<input type='hidden' value='{$info['id']}' name='questionid'>
<input type='hidden' value='down' name='vote'>
<button type='submit'><span class='glyphicon glyphicon-triangle-bottom'></span></button></form>";  


// om den som är inloggad har skrivit frågan eller är admin
if($login['acronym'] == $acronym_q || $login['acronym'] == 'admin'){
    $user_only_q = " 
    <a href='{$this->url->create('questions/softDelete')}/$id/{$info['userid']}'>
    <button style='float: right; padding: 5px;' type='submit' class='btn btn-default btn-sm'>
    <span class=\"glyphicon glyphicon-trash\"></span> Lägg i papperskorg</button></form>
    <a href='{$this->url->create('questions/edit')}/$id'>
    <button style='float: right; padding: 5px;' type='button' class='btn btn-default btn-sm'>
    <span class=\"glyphicon glyphicon-pencil\"></span> Ändra</button></a>";
} else {
    $user_only_q = null;
}    

if(isset($login)){
    $question_comment = "
    <div class='form-group'>
    <form action='{$this->url->create('comments/add')}' method='post'>
    <input type=hidden value='$id' name='questionid'>
    <input type=hidden value='{$login['id']}' name='userid'>
    <input type='text' class='form-control' name='comment' placeholder='Lämna en kommentar'  required='required'>
    <button type='submit' class='btn btn-primary btn-sm'>Spara</button>
    </form></div>";
    $answer_button = "
    <div class='form-group pull-right'>
    <a href='{$this->url->create('answers/add')}/$id/{$login['id']}'>    
    <button type='submit' class='btn btn-primary btn-sm'>Svara på den här frågan</button>
    </a></div>";    
}

if(isset($error)):?>
    <script>
    alert('<?php echo $error; ?>');
    </script>
    <?php 
    unset($_SESSION['error']);
endif;
?>


<div class='container'>
    <div class='row'>
        <div class='title-color'>
            <h1><?=$title?></h1>
        </div>    
        <div class='panel panel-default panel-transparent'>
            <div class='panel-heading'>
                <?=$answer_button?>
                <?php
                echo <<<EOD
                <h2>{$info['title']}</h2>
            </div>    
            <div class='panel-body'><table><tr>
                <td class='vote text-center'>$vote_question</td>
                <td class='text-center'> 
                <a  href='{$this->url->create('users/question')}/{$info['userid']}'>$gravatar</a><br>$acronym_q</td>
                <td style='padding-left:10px'>$question_md</td>                  
                </tr></table>
            </div>             
            <div class='clearfix panel-footer bottom-buffer'> 
                $tags
                <div class='pull-right'>
                    $user_only_q
                </div>
            </div>     
EOD;
            include NAVIGARE_INSTALL_PATH . "app/view/comments/comments-on-questions.tpl.php";
    ?>
        </div>
    </div>
</div>  <!-- Här avslutas frågepanelens container inkl kommentarer -->


<?php
if(isset($answers)){
    ?><div class='container'>
    <div class='rows title-color'><?php
    $num = count($answers);
    echo "<h1>" . $num . " svar</h1>";
    ?></div>
    </div><?php
    echo $answerspanel;
}

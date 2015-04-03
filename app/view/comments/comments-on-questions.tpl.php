<?php
$this->comments = new \TBJ\Navigare\Boats\Comments\Comments();
$this->comments->setDI($this->di);
foreach ($comments_q as $comment) {
    $info_q = $comment->getProperties();
    $comment = $this->textFilter->doFilter($info_q['comment'], 'shortcode, markdown');
    // om den som har skrivit kommentaren = den som är inloggad 
    if($info_q['acronym'] == $logged_in_acronym || $logged_in_acronym == 'admin'){
        $user_only_c = "
        <a href='{$this->url->create('comments/softDelete')}/{$info_q['id']}'>
        <button style ='float: right; padding: 5px;' type='button' class='btn btn-default btn-sm'>
        <span class=\"glyphicon glyphicon-trash\"></span> Lägg i papperskorg</button></a>
                        
        <a href='{$this->url->create('comments/edit')}/{$info_q['id']}/$id/$acronym_q'>
        <button style ='float: right; padding: 5px;' type='button' class='btn btn-default btn-sm'>
        <span class=\"glyphicon glyphicon-pencil\"></span> Ändra</button></a>";
    } else{
         $user_only_c = null;
    }
    $gravatar = get_gravatar($info_q['email']); 
    $acronym = $info_q['acronym'];
    $res = $this->comments->findRank($info_q['id']);
    $rank_q = $res->rank;    
    $vote_comment = "
    <form action='{$this->url->create('comments/vote')}' method='post'>
    <input type='hidden' value='{$info_q['id']}' name='commentid'>
    <input type='hidden' value='up' name='vote'>
    <button type='submit'><span class='glyphicon glyphicon-triangle-top'></span></button></form>
    $rank_q
    <form action='{$this->url->create('comments/vote')}' method='post'>
    <input type='hidden' value='{$info_q['id']}' name='commentid'>
    <input type='hidden' value='down' name='vote'>
    <button type='submit'><span class='glyphicon glyphicon-triangle-bottom'></span></button></form>"; 
    ?>
    
        <?php

        echo <<<EOD
        <div class='container'>
            <div class='rows'>
                <div class="col-md-12">
                    <div class='well well-sm clearfix'>
                        <table><tr>
                        <td class='vote text-center'>$vote_comment</td>
                        <td style='padding-right:10px'><a  href='{$this->url->create('users/question')}/{$info_q['userid']}'>$gravatar<br>$acronym</td> <td>$comment</td>
                        </tr></table>
                        $user_only_c
                    </div>
                </div>
            </div>
        </div>
EOD;
  ?>
        <?php } 
        echo <<<EOD
        <div class='container'>
            <div class='rows'>
                <div class="col-md-12">
                    $question_comment
                </div>
            </div>
        </div>
EOD;
        ?>


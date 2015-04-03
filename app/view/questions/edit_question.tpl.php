<?php $gravatar = get_gravatar($email);?> 
<div class="container">
  <div class="row">
    <div class="col-md-8"><h1><?=$title?></h1>
    <h3><?=$question_title?></h3>
    <table><tr>
    <td><?=$gravatar?> <?=$acronym?></td>
    <td><?=$question?></td>
    </tr></table><br>
    <?=$form?>
    </div>
  </div>
</div>


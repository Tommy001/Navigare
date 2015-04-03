<?php $gravatar = get_gravatar($email);?> 
<div class="container">
    <div class="row">
        <div class="col-md-8"><h1 class='title-color'><?=$title?></h1>
            <div class='panel panel-default panel-transparent'>
                <div class='panel-heading'>
                    <h3><?=$question_title?></h3>
                </div> 
                <div class='panel-body'>
                    <table><tr>
                    <td><?=$gravatar?> <?=$acronym?></td>
                    <td><?=$question?></td>
                    </tr></table><br>
                    <?=$form?>
                </div>
            </div    
        </div>
    </div>
</div>


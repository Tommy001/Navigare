<?php
$url = $this->request->getCurrentUrl();
$this->session->set('url',$url);
?>
<div class="container">
    <div class="row">
    <h1 class='title-color'><?=$title?></h1>
        <div class='panel panel-default panel-transparent'>      
            <div class='panel-body'>         
                    <?php foreach ($tags as $tag) {
                        $info = $tag->getProperties();
                        $url_id = $this->url->create('tags/id/' . $info['id']);
                        $url_idaction = $this->url->create('tags/id');   
                        echo <<<EOD
                        <div class="col-md-3 top-buffer">
                            <form action='$url_idaction' method='post'>
                            <input type='hidden' value='{$info['id']}' name='tagid'>
                            <button class='btn btn-default btn-lg'>
                            <span class='glyphicon glyphicon-tags'> </span> 
                            {$info['name']}              
                            </button>
                            </form>
                        </div>
EOD;

                    } ?>
                    <div class='pull-left clear-both'>
            </div>
        </div>            
    </div> 
</div>

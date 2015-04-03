<?php
$url = $this->request->getCurrentUrl();
$this->session->set('url',$url);
$login = $this->session->get('login');
$error = $this->session->get('error');
$trash = null;
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
                              <li role="presentation"  class="<?=$trashed?>">
                              <a href="<?=$this->url->create('users/trashcan')?>">
                              Papperskorgen</a>
                              </li>
                              <li role="presentation"  class="<?=$inactive?>">
                              <a href="<?=$this->url->create('users/inactive')?>">
                              Inaktiva</a>
                              </li>
                              <li role="presentation"  class="<?=$active?>">
                              <a href="<?=$this->url->create('users')?>">
                              Aktiva</a>
                              </li>
                          </ul>
                      </div>         
                      <?php $url_delete_trash = $this->url->create('users/deleteTrash'); 
                        foreach ($users as $user) {
                          $info = $user->getProperties();
                          $url = $this->url->create('users/question/' . $info['id']);
                          $url_update = $this->url->create('users/update/' . $info['id']);
                          $url_delete = $this->url->create('users/softDelete/' . $info['id']);
                          $url_desactivate = $this->url->create('users/deactivate/' . $info['id']);
                          $url_activate = $this->url->create('users/activate/' . $info['id']);

                          $url_restore = $this->url->create('users/restore/' . $info['id']); 
                          $url_empty = $this->url->create('users/delete/' . $info['id']);
                          $status = "";
                          if($login['acronym'] == 'admin'){
                              if( $info['active'] == null && $info['deleted'] == null) {
                                  $status = "<a href='$url_activate'><button type='button' class='btn btn-default btn-xs'> Aktivera</button></a>";
                              } else if( $info['deleted'] != null) {
                                  $status = 'Borttagen';
                              } else {
                                  $status = "
                                  <a href='$url_desactivate'><button style='margin-bottom:5px; margin-right:5px;' type='button' class='btn btn-default btn-xs'> Inaktivera</button></a>";
                              }
                          }
                          $links = null;
                          if(isset($trashed) && $login['acronym'] == 'admin'){
                              $links = "<br>
                              <a href=\"$url_restore\"><button style='margin-bottom:5px; margin-right:5px;' type='button' class='btn btn-default btn-xs'><span class=\"glyphicon glyphicon-refresh\"></span> Ångra</button></a>
                              <a href=\"$url_empty\"><button type='button' class='btn btn-default btn-xs'><span class=\"glyphicon glyphicon-trash\"></span> Ta bort definitivt</button></a>";    
                              $trash = "
                              <form action='$url_delete_trash' method='post'>
                              <button style='margin-bottom:5px; margin-right:5px;' type='submit' class='btn btn-primary btn-xs pull-right'><span class=\"glyphicon glyphicon-trash\"></span> Töm papperskorgen</button>
                              </form>";  
                          }
                          if(!isset($trashed) && $login['acronym'] == 'admin'){
                              $links = "<br>
                              <a href=\"$url_update\"><button style='margin-bottom:5px; margin-right:5px;' type='button' class='btn btn-default btn-xs'><span class=\"glyphicon glyphicon-pencil\"></span> Ändra</button></a>
                              <a href=\"$url_delete\"><button style='margin-bottom:5px; margin-right:5px;' type='button' class='btn btn-default btn-xs'><span class=\"glyphicon glyphicon-trash\"></span> Ta bort</button</a>";
                          }
                          if(!isset($trashed) && $login['acronym'] == $info['acronym']){
                              $links = "
                              <a href=\"$url_update\"><button style='margin-bottom:5px; margin-right:5px;' type='button' class='btn btn-default btn-xs'><span class=\"glyphicon glyphicon-pencil\"></span> Ändra</button></a>";
                          }

                          $gravatar = get_gravatar($info['email']);
                          echo <<<EOD
                          <div class="col-md-3 top-buffer">
                              <div class='panel panel-default'>      
                                  <div class='panel-body'><table><tr>
                                      <td class='text-center' style='padding-right:10px; padding-top:10px'>
                                      <a href='$url'>$gravatar<br>
                                      {$info['acronym']}</a>
                                      </td>
                                      <td>            
                                      {$info['name']}<br>
                                      {$info['email']}<br>
                                      $status 
                                      $links
                                      </td>   
                                      </tr></table>    
                                  </div>
                              </div>
                          </div>
EOD;
                         } ?>
                   </div>                                             
             <div class='clearfix panel-footer bottom-buffer'> 
                  <?=$trash?>            
             </div>
        </div>    
    </div> 
</div>

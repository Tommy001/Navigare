<div class="container">
    <div class="row">
        <h1><?=$title?></h1>
        <div>
            <p><a href='<?=$this->url->create('users/add')?>'><span class=" glyphicon glyphicon-plus-sign"></span> Registrera dig som medlem</a></p> 
        </div>           
        <div>
            <ul class="nav nav-tabs right-to-left">
                <li role="presentation"  class="<?=$trashed?>">
                    <a href="<?=$this->url->create('users/trashcan')?>">
                    Borttagna</a>
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
    </div> 

<?php foreach ($users as $user) {


$info = $user->getProperties();

$url = $this->url->create('users/question/' . $info['id']);
$url_update = $this->url->create('users/update/' . $info['id']);
$url_delete = $this->url->create('users/softDelete/' . $info['id']);
$url_desactivate = $this->url->create('users/deactivate/' . $info['id']);
$url_activate = $this->url->create('users/activate/' . $info['id']);
$status = "";
if( $info['active'] == null && $info['deleted'] == null) {
    $status = '<a href="'.$url_activate.'">Inaktiv</a>';
} else if( $info['deleted'] != null) {
    $status = 'Borttagen';
} else {
    $status = '<a href="'.$url_desactivate.'">Aktiv</a>';
}
$gravatar = get_gravatar($info['email']);
echo <<<EOD
        <div class="col-md-3 bottom-buffer">
            <table><tr>
                <td style='padding-right:10px; padding-top:10px'>
                    $gravatar
                </td>
                <td>            
                    {$info['acronym']}<br>
                    {$info['name']}<br>
                    {$info['email']}<br>
                    $status 
                    <a href="$url"><span class="glyphicon glyphicon-eye-open"></span></a>
                    <a href="$url_update"><span class="glyphicon glyphicon-pencil"></span></a>
                    <a href="$url_delete"><span class="glyphicon glyphicon-trash"></span></a>
                </td>
            </tr></table>    
        </div>
EOD;

} ?>


</div>


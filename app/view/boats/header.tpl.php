    <nav class="navbar navbar-inverse navbar-static-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <form class="navbar-form navbar-right" method='post' action='<?=$this->url->create('questions/search')?>'>
                <div class="input-group">
                    <input name='search' type='text' class="form-control" placeholder="Sök fråga..." required='required' title='Tips! Skriv bara de första bokstäverna i sökordet för fler träffar'>
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="submit" value='submit'>Sök!</button>
                        </span>
                 </div><!-- /input-group -->
            </form>

           <ul class="nav navbar-nav">

              <li class='index'><a href="<?=$this->url->asset('')?>">Startsida</a>           
              <li class='questions'><a href="<?=$this->url->asset('')?>/questions">Frågor</a></li>
              <li class='tags'><a href="<?=$this->url->asset('')?>/tags">Ämnen</a></li>
              <li class='users'><a href="<?=$this->url->asset('')?>/users">Användare</a></li>              
              <li class='about'><a href="<?=$this->url->asset('')?>/about">Om oss</a></li>
            </ul>
            <div class='navbar-right'>
            <?php if($this->session->has('login')){
                $login = $this->session->get('login');
                $gravatar = get_gravatar($login['email']);
                $acronym = $login['acronym'];
                $login_id = $login['id'];
                $user_url = $this->url->create('users/question').'/'.$login_id;
                $ask_url = $this->url->asset('questions/add');
                $logout_url = $this->url->asset('login/logout');
                echo "<div class='pull-left'><a href='$ask_url".'?userid='."$login_id'>
                <button type='submit' class='btn btn-default navbar-btn btn_md'>Ställ en fråga</button></a></div>";
                echo "<form class='pull-left' action='$logout_url' method='post'><button type='submit' class='btn btn-default navbar-btn btn_md'>Logga ut</button></form>"; 
                echo "<div class='pull-left'><div class='text-center'><a href='$user_url'>$gravatar</a></div>               
                <span class='text-center' style='color:#FFF'>$acronym </span></div>"; 
            } else {
                $login_url = $this->url->asset('login/add');
                $add_user = $this->url->asset('users/add');
                echo "<form class='pull-right' action='$login_url' method='post'><button type='submit' class='btn btn-default navbar-btn btn_md'>Logga in</button></form>";
                echo "<form class='pull-right' action='$add_user'><button type='submit' class='btn btn-default navbar-btn btn_md'>Bli medlem</button></form>";
            } ?> 
        </div>
        </div><!--/.navbar-collapse -->
      </div>
    </nav>

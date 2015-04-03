    <nav class="navbar navbar-inverse navbar-static-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="<?=$this->url->asset('')?>">Startsida</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <form class="navbar-form navbar-right">
            <div class="form-group">
              <input type="text" placeholder="E-post" class="form-control">
            </div>
            <div class="form-group">
              <input type="password" placeholder="Lösenord" class="form-control">
            </div>
            <button type="submit" class="btn btn-success">Logga in</button>
          </form>

           <ul class="nav navbar-nav">
              <li class="active"><a href="<?=$this->url->asset('')?>/questions/list">Frågor</a></li>
              <li><a href="<?=$this->url->asset('')?>/tags">Ämnen</a></li>
              <li><a href="<?=$this->url->asset('')?>/users">Användare</a></li>              
              <li><a href="<?=$this->url->asset('')?>/about">Om oss</a></li>
<!--               <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Dropdown <span class="caret"></span></a>
                <ul class="dropdown-menu" role="menu">
                  <li><a href="#">Action</a></li>
                  <li><a href="#">Another action</a></li>
                  <li><a href="#">Something else here</a></li>
                  <li class="divider"></li>
                  <li class="dropdown-header">Nav header</li>
                  <li><a href="#">Separated link</a></li>
                  <li><a href="#">One more separated link</a></li>
                </ul>
              </li>-->
            </ul>
        </div><!--/.navbar-collapse -->
      </div>
    </nav>

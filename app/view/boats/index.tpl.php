<?php
$url = $this->request->getCurrentUrl();
$this->session->set('url',$url);
echo $content;
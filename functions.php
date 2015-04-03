<?php
/**
 * Theme related functions. 
 *
 */

/**
 * Get title for the webpage by concatenating page specific title with site-wide title.
 *
 * @param string $title for this page.
 * @param string $titleAppend a general title to append.
 * @return string/null wether the favicon is defined or not.
 */
/*function get_title($title, $titleAppend = null) {
  return $title . $title_append;
}
*/

/**
 * Get either a Gravatar URL or complete image tag for a specified email address.
 *
 * @param string $email The email address
 * @param string $s Size in pixels, defaults to 80px [ 1 - 2048 ]
 * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
 * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
 * @param boole $img True to return a complete IMG tag False for just the URL
 * @param array $atts Optional, additional key/value attributes to include in the IMG tag
 * @return String containing either just a URL or a complete image tag
 * @source http://gravatar.com/site/implement/images/php/
 */
function get_gravatar_big( $email, $s = 80, $d = 'wavatar', $r = 'g', $img = true, $atts = array() ) {
    $url = 'http://www.gravatar.com/avatar/';
    $url .= md5( strtolower( trim( $email ) ) );
    $url .= "?s=$s&amp;d=$d&amp;r=$r";
    if ( $img ) {
        $url = '<img alt=\'gravatar\' src="' . $url . '"';
        foreach ( $atts as $key => $val )
            $url .= ' ' . $key . '="' . $val . '"';
        $url .= ' />';
    }
    return $url;
}


function setBodyId() {

    $path = $_SERVER['PHP_SELF'];
    if(!isset($bodyId)) {
		if(mb_ereg_match('.*about', $path)) {
			$bodyId = 'about';
		} else if(mb_ereg_match('.*answers', $path)) {
			$bodyId = 'questions';
		} else if(mb_ereg_match('.*questions', $path)) {
			$bodyId = 'questions';
		} else if(mb_ereg_match('.*tags', $path)) {
			$bodyId = 'tags';
		} else if(mb_ereg_match('.*users', $path)) {
			$bodyId = 'users';
		} else if ($path == '') {
			$bodyId = 'home';
		} else if(mb_ereg_match('.*comments', $path)) {
			$bodyId = 'questions';
		} else {
			$bodyId = 'index';
		}
	}
	return $bodyId;
}


function get_gravatar( $email, $s = 40, $d = 'wavatar', $r = 'g', $img = true, $atts = array() ) {
    $url = 'http://www.gravatar.com/avatar/';
    $url .= md5( strtolower( trim( $email ) ) );
    $url .= "?s=$s&amp;d=$d&amp;r=$r";
    if ( $img ) {
        $url = '<img alt=\'gravatar\' src="' . $url . '"';
        foreach ( $atts as $key => $val )
            $url .= ' ' . $key . '="' . $val . '"';
        $url .= ' />';
    }
    return $url;
}
    
function getTimeAgo($timestamp) {
    $date = date("Y-m-d H:i:s", $timestamp);
    $timeAgo = new TimeAgo();
    $time =  $timeAgo->inWords($date);

    return $time;
}

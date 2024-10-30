<?php
/*
Plugin name: CP Tent Posts Shortcode
Plugin URI: https://brooks.tent.is/posts/pewx4u
Description: Shortcode and widget to display a list of recent public Tent posts. Visit tent.io to learn about Tent.
Version: 1.0
Author: Brooks
Author URI: http://brooks.tent.is
*/

//$cpentitybasic = false;
function cp_tentsc_discovery($ts) {

	
	$cpdiscovery = wp_remote_head($ts);
	$cpdiscoveryresponse = wp_remote_retrieve_headers($cpdiscovery);
	if(strpos($cpdiscoveryresponse['link'], 'https://tent.io/rels/profile') !== false):
		$cprofilelink = substr($cpdiscoveryresponse['link'], 1, (strpos($cpdiscoveryresponse['link'],';')-2));
	//	return $cprofilelink;
	elseif(wp_remote_retrieve_response_code($cpdiscovery)>=300 && wp_remote_retrieve_response_code($cpdiscovery)<400)://wp http class isn't following redirects. i'll check for a single redirect.
		$rcpdiscovery = wp_remote_head($cpdiscoveryresponse['location']);
		$rcpdiscoveryresponse = wp_remote_retrieve_headers($rcpdiscovery);
		if(strpos($rcpdiscoveryresponse['link'], 'https://tent.io/rels/profile') !== false):
			$cprofilelink = substr($rcpdiscoveryresponse['link'], 1, (strpos($rcpdiscoveryresponse['link'],';')-2));
		//	return $cprofilelink;
		else:
			return false;
		endif;
	else:
		return false;
	endif;
	
	$cptentscheme = wp_remote_get($cprofilelink);
	$cptentschemeresponse = wp_remote_retrieve_body($cptentscheme);
	$cptentschemearray = json_decode($cptentschemeresponse,true);
	$cptentserver = false;
	$cpentity = false;
	foreach($cptentschemearray as $type=>$info) {
		if(array_key_exists('servers', $info)):
			$cptentserver = $info['servers'][0];
		elseif(array_key_exists('name', $info)):
			$cpentity = $info;
		endif;
	}//foreach
	$cpentity['server'] = $cptentserver;
	if($cpentity['avatar_url'] == null):
		$cpdir = dirname(__FILE__) . '/cp-tent-posts.php';
		$cpavatar = plugin_dir_url($cpdir) . 'cptent.png';
		$cpentity['avatar_url'] = $cpavatar;
	endif;
	return $cpentity;
	
//	return $cptentschemearray;
	
}

function retrieve_public_tent_posts($tentserver = 'https://tent.tent.is',$args) {
$tpwparameters = array('method'=>'GET','timeout'=>'5','redirection'=>'5','httpversion'=>'1.1','blocking'=>'true','headers'=>array('Content-Length: 2'),'body'=>'null','cookies'=>array());//not using params right now

//request args
if(is_int($args['limit']) || ctype_digit($args['limit'])):
	$tpwlimit = $args['limit'];
else:
	$tpwlimit = 10;
endif;
$tpwrequeststring = $tentserver . '/posts?post_types=https://tent.io/types/post/status/v0.1.0&limit=' . $tpwlimit;
if($args['must_mention']):
	$tpwrequeststring .= '&mentioned_entity=' . urlencode($args['must_mention']);
endif;
/*
$tpwrcounter = 0;
foreach($args as $key=>$value) {
	if($tpwrcounter>0):
		$tpwrequeststring .= '&';
	endif;
	if($value):
		$tpwrcounter++;
	endif;
}//foreach
*/


$tpwresponse = wp_remote_get($tpwrequeststring);

$tpwresponsemessage = wp_remote_retrieve_body($tpwresponse);

$tpwresponsearray = json_decode($tpwresponsemessage, true);
$cp_tent_posts_array = array();
foreach($tpwresponsearray as $response_single_tent_post){
	if(array_key_exists('text', $response_single_tent_post['content'])):
		if(is_array($response_single_tent_post['mentions'])):
			$mentions = array();
			$gotmentions = $response_single_tent_post['mentions'];
			foreach($gotmentions as $amention){//mentions
				$themention = parse_url($amention['entity'], PHP_URL_HOST);
				//echo $themention;
				$mentions[] = $themention;
			}//mentions
		endif;
//		echo '<p>mentioning</p>';
//		print_r($mentions);
//		echo '<p>done mentioning.</p>';
		$thetext = $response_single_tent_post['content']['text'];
		$thiscppost = array('text'=>$thetext,'mentions'=>$mentions);
//		print_r($thiscppost);
//		echo '<br />';
		$cptentpostid = $response_single_tent_post['id'];
//		$cp_tent_posts_array[$cptentpostid] = $response_single_tent_post['content'];
		$cp_tent_posts_array[$cptentpostid] = $thiscppost;
	endif;
}//foreach
//echo '<br /><br />';
//print_r($cp_tent_posts_array);
return $cp_tent_posts_array;
}//function

//adding shortcode
function cp_tent_public_posts_shortcode( $atts ) {
	extract(shortcode_atts(array('tent'=>'https://tent.tent.is', 'limit'=>10, 'mentions'=>'off', 'links'=>'on', 'header'=>'on', 'must_mention'=>false, 'hashtag'=>false), $atts));
	$cptarget = cp_tentsc_discovery($tent);
	if($links == 'on'):
		$postlink = $tent . '/posts/';
	elseif($links!='off'):
		$postlink = $links;
	endif;

	//echo $tent;
//	$x=retrieve_public_tent_posts($tent);
//	print_r($x);
//	$tentcounter=0;
	if($header == 'on'):
	echo '<span class="cptentbasic"><a class="cptentlink" href="' . $cptarget['avatar_url'] . '"><img src="' . $cptarget['avatar_url'] . '" /></a> <a class="cptentlink" href="' . $tent . '">' . $cptarget['name'] . '</a></span><br />
	';
	endif;
	echo '
	<ul class="cptent">';
	foreach(retrieve_public_tent_posts($cptarget['server'],$atts) as $key=>$acptentpost){
//		print_r($acptentpost);
//		if($tentcounter == $limit):
//		break;
//		endif;
	   echo '<li>';
	   if(isset($postlink)):
	   	echo '<a class="cptentlink" href="' . $postlink . $key . '">&crarr;</a> ';
	   endif;
	   echo $acptentpost['text'];
	   if($mentions == 'on'):
	   $mentioncounter = 0;
//	   print_r($acptentpost['mentions']);
	   foreach($acptentpost['mentions'] as $acleanmention) {
	   	if($mentioncounter == 0):
	   		echo '<span class="cptentmentions">&lfloor; mentions: 
';
	   	endif;
		   echo $acleanmention . '
 ';
 		$mentioncounter++;
	   }
	   if($mentioncounter>0):
	   echo '</span>
';
	   endif;//is mentioncounter > 0
	   endif;//is mentions on
	   echo '</li>
	   ';
//	   $tentcounter++;
   }
   echo '</ul>
   ';
   
}
add_shortcode( 'cp-tent', 'cp_tent_public_posts_shortcode' );

add_action('wp_enqueue_scripts','cp_tent_stylesheet');
function cp_tent_stylesheet() {
	wp_register_style( 'cp-tent-style', plugins_url('style.css', __FILE__) );
	wp_enqueue_style( 'cp-tent-style' );
}

include_once('widget.php');

?>
<?php

// include configurtion
include 'config.inc.php';

// build Ice connection to murmur
try {
    require_once 'Ice.php';
    require_once 'Murmur.php';
    $ICE = Ice_initialize();
    $meta = Murmur_MetaPrxHelper::checkedCast($ICE->stringToProxy($config['ice_proxy_string']));
} catch (Ice_Exception $ex) {
    print_r($ex);
}

// get server 1
$server = $meta->getServer($config['mumble_server']);
$tree = $server->getTree();

// helper functions
function has_Sub($item) {
	if (count($item->children) > 0 || count($item->users) > 0) {
//	if ($item->children || $item->users) {

		return True;
	} else {
		return False;
	}
}

function pChannel($c) {
	$channelID = $c->c->id;
	$channelName = $c->c->name;

	$html_class = 'mumble-channel';
	$extra_tags = '';

	// has sub channels or users?
	if (!has_Sub($c)) {
		$html_class .= ' nosub';
	}
	// open root channel
	if ($channelID == 0) {
		$extra_tags = 'checked="checked"';
	}
	
	echo '<li class="'. $html_class .'">';
	if (has_Sub($c)) {
	echo '<input type="checkbox" '.$extra_tags . ' id="channelID-'. $channelID . '"  />';
	}
	echo '<label for="channelID-' . $channelID .'">'. $channelName . '</label>';
//	echo '<li>'.  $c->c->name ; 

}

function pUsers($users) {
//	echo '<ul>';
	foreach($users as $user) {
		echo '<li class="mumble-user"><span>' . $user->name . '</span></li>';
	}
//	echo '</ul>';

}

function getChannel($c) {
//	echo '<li>';	
	pChannel($c);

	if (has_Sub($c)) {
//	if ($c->children || $c->users) {
	    echo '<ul>';
	    for ($i =0 ; $i < count($c->children); ++$i) {
	    // foreach on $c use a wrong channel order
	    // foreach ($c->children as $child) {
		    getChannel($c->children[$i]); 
	    }
	    pUsers($c->users);
	    echo '</ul>';
    
	}
	//echo '</li>';
//	if ($c->users) {pUsers($c->users);}
	echo '</li>';
}

print '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">';
echo '<html lang="en-GB">';
echo '<head>';
echo '<meta http-equiv="Content-Type" content="text/html;charset=utf-8">';
echo '<link rel="stylesheet" type="text/css" href="style/default/style.css" media="screen">';
echo '<title>MumbleViewer without any JavaScript</title>';
echo '</head><body>';
echo '<div class="mv-treeview">';
echo '<ul>';
getChannel($tree);
echo '</ul>';


echo '</div></body></html>';

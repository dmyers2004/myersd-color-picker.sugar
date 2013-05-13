#!/usr/bin/php
<?php
error_reporting(0);

$content = '';
$fp = fopen('php://stdin', 'r');
while ($line = fgets($fp, 4096))
	$content .= $line;
fclose($fp);

$orginal = $content;

if (empty($content)) {
	$content = 'ffffff';
}

$content = trim($content,'#');

if (strlen($content) != 6) {
	/* double it up */
	$c = $content;
	$content = $c{0}.$c{0}.$c{1}.$c{1}.$c{2}.$c{2};	
}

$red = hexdec(substr($content,0,2))*256;
$green = hexdec(substr($content,2,2))*256;
$blue = hexdec(substr($content,4,2))*256;

$script = <<<SCRIPT
tell application "Finder"
	activate
	set my_color to { $red , $green , $blue }
	set AppleScript's text item delimiters to {","}
	set col to (choose color default color my_color) as text
end tell
SCRIPT;

file_put_contents('/tmp/pick_color_applescript.txt',$script);

$color = shell_exec('osascript /tmp/pick_color_applescript.txt');

list($red,$green,$blue) = explode(',',$color);

$dec_red = dechex2($red);
$dec_green = dechex2($green);
$dec_blue = dechex2($blue);

if (!empty($color)) {
	echo '#'.$dec_red.$dec_green.$dec_blue;
} else {
	echo $orginal;
}

function dechex2($color) {
	$foo = dechex($color/256);
	return substr('00'.$foo,-2);
}

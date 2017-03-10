<?php
session_start();
$r = random_text();
get_image($r);

$_SESSION['validator'] = md5($r);
$_SESSION['validator1'] = $r;

function random_text()
{
         $str='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789';
         $random='';
         $length=7;
         for($i=0; $i<$length; $i++){$random.=$str{rand(0,strlen($str)-1)};}
         return $random;
}
function get_image($verification_text)
{

    $img = @imagecreatetruecolor(63, 15) or die("Unable to create verification image!");
    $black = imagecolorallocate($img, rand(0,255), 0, rand(0,255));
    $white = imagecolorallocate($img, 255, 255, 255);
    imagefill($img, 0, 0, $white);
    imagestring($img, 5, 0, 0,  $verification_text, $black);
    imagejpeg($img);
    imagedestroy($img);

}
?>


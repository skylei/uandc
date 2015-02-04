<?php 
function verify(){
        $image = imagecreatetruecolor(75,25);
        $backround = imagecolorallocate($image,rand(100,255),rand(100,255),rand(100,255));
        imagefilledrectangle($image,0,0,75,25,$backround);
        $str = "";
        for($i = 0;$i < 4;$i++){
            $r = rand(0,9);
            $str .= $r;  //"1900"
        }
        $_SESSION['yzm'] = $str;
        $fontColor = imagecolorallocate($image,20,20,20);
        imagestring($image,5,5,5,$str,$fontColor);
        for($i = 0;$i < 150;$i++){
            $pointColor = imagecolorallocate($image,rand(0,255),rand(0,255),rand(0,255));
            imagesetpixel($image,rand(0,75),rand(0,25),$pointColor);
        }
        for($i = 0;$i < 5;$i++){
            $lineColor = imagecolorallocate($image,rand(0,255),rand(0,255),rand(0,255));
            imageline($image,rand(0,75),rand(0,25),rand(0,75),rand(0,25),$lineColor);
        }
        header("content-type:image/jpeg");
        imagegif($image);
    }
	verify();
	
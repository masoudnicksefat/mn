<?php

/**
 * @package             Joomla
 * @subpackage          CoalaWeb Contact Component
 * @author              Steven Palmer
 * @author url          https://coalaweb.com/
 * @author email        support@coalaweb.com
 * @license             GNU/GPL, see /assets/en-GB.license.txt
 * @copyright           Copyright (c) 2017 Steven Palmer All rights reserved.
 *
 * CoalaWeb Contact is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if (!defined('KEY_FOR_RC4')) {
    define("KEY_FOR_RC4", "adadaNchsadagadgakk342eiejfiejifje4234MnUUK25fjiNNBZBZNAkdaasd8sadhHZKZJnGREQhhsdjdksdsde");
}


class CaptchaSecurityImages {

    var $font = 'ThisisKeSha.ttf';

    function GenerateImage($width = '190', $height = '60', $code) {

        /* font size will be 75% of the image height */
        $font_size = $height * 0.55;
        $image = @imagecreate($width, $height) or die('Cannot initialize new GD image stream');

        /* set the colours */
        $background_color = imagecolorallocate($image, 255, 255, 255);
        $text_color = imagecolorallocate($image, 0, 26, 26);
        $noise_color = imagecolorallocate($image, 25, 89, 89);

        /* generate random dots in background */
        for ($i = 0; $i < ($width * $height) / 3; $i++) {
            imagefilledellipse($image, mt_rand(0, $width), mt_rand(0, $height), 1, 1, $noise_color);
        }

        /* generate random lines in background */
        for ($i = 0; $i < ($width * $height) / 150; $i++) {
            imageline($image, mt_rand(0, $width), mt_rand(0, $height), mt_rand(0, $width), mt_rand(0, $height), $noise_color);
        }

        /* create textbox and add text */
        $textbox = imagettfbbox($font_size, 0, $this->font, $code) or die('Error in imagettfbbox function');
        $x = ($width - $textbox[4]) / 2;
        $y = ($height - $textbox[5]) / 2;
        imagettftext($image, $font_size, 0, $x, $y, $text_color, $this->font, $code) or die('Error in imagettftext function');

        /* output captcha image to browser */
        header('Content-Type: image/jpeg');
        imagejpeg($image);
        imagedestroy($image);
    }

}

function str_decrypt($str) {
    $mystrInitial = base64_decode(rawurldecode($str));
    $mystr = RC4($mystrInitial, KEY_FOR_RC4);
    return $mystr;
}

function RC4($data, $key) {

    $x = 0;
    $j = 0;
    $a = 0;
    $temp = "";
    $Zcrypt = "";
    for ($i = 0; $i <= 255; $i++) {
        $counter[$i] = "";
    }

    $pwd = $key;
    $pwd_length = strlen($pwd);

    for ($i = 0; $i < 255; $i++) {
        $key[$i] = ord(substr($pwd, ($i % $pwd_length) + 1, 1));
        $counter[$i] = $i;
    }
    for ($i = 0; $i < 255; $i++) {
        $x = ($x + $counter[$i] + $key[$i]) % 256;
        $temp_swap = $counter[$i];
        $counter[$i] = $counter[$x];
        $counter[$x] = $temp_swap;
    }
    for ($i = 0; $i < strlen($data); $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $counter[$a]) % 256;
        $temp = $counter[$a];
        $counter[$a] = $counter[$j];
        $counter[$j] = $temp;
        $k = $counter[(($counter[$a] + $counter[$j]) % 256)];
        $Zcipher = ord(substr($data, $i, 1)) ^ $k;
        $Zcrypt .= chr($Zcipher);
    }

    return $Zcrypt;
}

$code = isset($_GET['code']) ? $_GET['code'] : '';

if (isset($_GET['code'])) {
    $captcha = new CaptchaSecurityImages();
    $captcha->GenerateImage('190', '60', str_decrypt($code));
}
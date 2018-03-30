<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/29
 * Time: 16:14
 */

namespace App\Common\Tools;


class CommonTools
{

    /**
     *
     * @param $token
     * @param $timestamp
     * @param $nonce
     * @return null|string
     */
    public static function getSHA1($token,$timestamp,$nonce) {
        try {
            $array = array($token,$timestamp,$nonce);
            sort($array,SORT_STRING);
            $str = implode($array);
            return sha1($str);
        } catch (\Exception $e) {
            return null;
        }

    }

    public static  function randChar($len = 6) {
        $chars = array(
            "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
            "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
            "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
            "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
            "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
            "3", "4", "5", "6", "7", "8", "9"
        );
        $charsLen = count($chars) - 1;
        shuffle($chars);    // 将数组打乱
        $output = "";
        for ($i = 0; $i < $len; $i++) {
            $output .= $chars[mt_rand(0, $charsLen)];
        }
        return $output;
    }

}
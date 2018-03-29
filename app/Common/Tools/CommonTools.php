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

}
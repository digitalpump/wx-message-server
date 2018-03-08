<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/8
 * Time: 09:40
 */

namespace App\Common\Tools\Jwt;


class JwtUser
{
    private $id;
    public function __construct($id = 0)
    {
        if(!empty($id)) $this->id = $id;
    }
    public function setId($id) {
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }

}
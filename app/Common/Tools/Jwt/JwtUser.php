<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/8
 * Time: 09:40
 */

namespace App\Common\Tools\Jwt;


use App\Models\Users;

class JwtUser
{
    private $id;
    private $user;
    private $vendor;
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

    public function getUser() {
        if(empty($this->user)) {
            $this->user = Users::find($this->id);
        }
        return $this->user;
    }

    public function getVendor() {
        if (empty($this->vendor)) {
            $this->vendor = Vendor::find($this->id);
        }
        return $this->vendor;
    }

}
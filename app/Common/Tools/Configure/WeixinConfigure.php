<?php

/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/18
 * Time: 13:07
 */
namespace App\Common\Tools\Configure;
class WeixinConfigure
{
    private $officialAccount = "official_account";
    private $miniProgram = "mini_program";
    private $configurs;
    public function __construct()
    {
        $configures = config('weixin.'.$this->miniProgram);
        if(!empty($configures)) {
            $this->configurs[$this->miniProgram] = $configures;
        }

        $configures = config('weixin.'.$this->officialAccount);
        if(!empty($configures)) {
            $this->configurs[$this->officialAccount] = $configures;
        }
    }

    public function getMiniProgramAppId() {
        if(empty($this->configurs[$this->miniProgram])) return "";
        if(empty($this->configurs[$this->miniProgram]['appid'])) return "";
        return $this->configurs[$this->miniProgram]['appid'];
    }

    public function getMiniProgramSecret() {
        if(empty($this->configurs[$this->miniProgram])) return "";
        if(empty($this->configurs[$this->miniProgram]['secret'])) return "";
        return $this->configurs[$this->miniProgram]['secret'];
    }

    public function getOfficialAccountAppId() {
        if(empty($this->configurs[$this->officialAccount])) return "";
        if(empty($this->configurs[$this->officialAccount]['appid'])) return "";
        return $this->configurs[$this->officialAccount]['appid'];
    }

    public function getOfficialAccountSecret() {
        if(empty($this->configurs[$this->officialAccount])) return "";
        if(empty($this->configurs[$this->officialAccount]['secret'])) return "";
        return $this->configurs[$this->officialAccount]['secret'];
    }
}
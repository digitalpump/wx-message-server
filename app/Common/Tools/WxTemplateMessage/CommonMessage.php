<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/21
 * Time: 11:35
 */

namespace App\Common\Tools\WxTemplateMessage;


abstract class CommonMessage extends Message
{

    private $keywords = [];
    private $keyword_colors = [];

    protected function getKeywords()
    {
        return $this->keywords;
    }

    protected function getKeywordColors()
    {
        return $this->keyword_colors;
    }

    public function addKeyWord($key,$value) {
        $this->keywords[$key] = $value;
        return $this;
    }
    public function addKeywordColor($key,$color) {
        $this->keyword_colors[$key]=$color;
        return $this;
    }

}
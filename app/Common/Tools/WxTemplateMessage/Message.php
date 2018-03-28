<?php

/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/21
 * Time: 09:52
 */
namespace App\Common\Tools\WxTemplateMessage;
abstract class Message
{
    protected $template_id = '';        //所需下发的模板消息的id
    protected $emphasis_keyword = '';   //模板需要放大的关键词，不填则默认无放大
    protected $color = '#000000';       //模板内容字体的颜色，不填默认黑色
    protected $touser = "";             //接收者（用户）的 openid
    protected $page ="";                //点击模板卡片后的跳转页面，仅限本小程序内的页面。支持带参数,（示例index?foo=bar）。该字段不填则模板无跳转。
    protected $form_id = "";            //表单提交场景下，为 submit 事件带上的 formId；支付场景下，为本次支付的 prepay_id
    protected $data = "";               //模板内容，不填则下发空模板
    protected $url ="";                 //跳转URL
    const COLOR_DEFAULT = "#000000";

/*
 *
小程序模板消息下发条件说明
支付

当用户在小程序内完成过支付行为，可允许开发者向用户在7天内推送有限条数的模板消息（1次支付可下发3条，多次支付下发条数独立，互相不影响）

提交表单

当用户在小程序内发生过提交表单行为且该表单声明为要发模板消息的，开发者需要向用户提供服务时，可允许开发者向用户在7天内推送有限条数的模板消息（1次提交表单可下发1条，多次提交下发条数独立，相互不影响）
*/
    public function __construct($touser,$form_id="",$page="")
    {
        $this->touser = $touser;
        if(!empty($form_id)) $this->form_id = $form_id;
        if(!empty($page)) $this->page = $page;
    }

    public function setJumpUrl($url) {
        $this->url = $url;
        return $this;
    }
    public function spew() {
        if(empty($this->template_id)) return "Template id not found.";
        if(empty($this->touser)) return "touser can't be empty";
        $obj = new \stdClass();
        $obj->touser = $this->touser;
        $obj->template_id = $this->template_id;
        if(!empty($this->page)) $obj->page = $this->page;

        if(!empty($this->form_id)) $obj->form_id = $this->form_id;
        if(!empty($this->url)) $obj->url = $this->url;
        $obj->data = $this->generateBody();
        if(!empty($this->color)) $obj->color = $this->color;
        if(!empty($this->emphasis_keyword)) $obj->emphasis_keyword = $this->emphasis_keyword;
        return $obj;
    }

    protected function generateBody() {
        $data = new \stdClass();
        $keywords = $this->getKeywords();
        if(empty($keywords)) return $data;
        if(!is_array($keywords)) return $data;
        $colors = $this->getKeywordColors();
        if(empty($colors)) $color = $this->color;

        foreach ($keywords as $key=>$val) {
            $data->$key = new \stdClass();
            $data->$key->value = $val;
            $data->$key->color = empty($color)?empty($colors[$key])?self::COLOR_DEFAULT:$colors[$key]:$color;
        }
        return $data;
    }

    protected abstract function getKeywords();

    protected abstract function getKeywordColors();

}
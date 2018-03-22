<?php

/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/21
 * Time: 09:31
 */
namespace App\Common\Tools\WxTemplateMessage;
class ReserveSuccessMessage extends CommonMessage
{
    /*
        预定商品
        {{keyword1.DATA}}
        预定商家
        {{keyword2.DATA}}
        预定门店
        {{keyword3.DATA}}
        预定单号
        {{keyword4.DATA}}
        开始时间
        {{keyword5.DATA}}
        结束时间
        {{keyword6.DATA}}
        预计使用
        {{keyword7.DATA}}
        温馨提示
        {{keyword8.DATA}}
    */
    protected $template_id = '_LPzw2XCM7jUcSd3JBTPqv2B8dh8qsIGjjxjl_1-KlQ';
    protected $emphasis_keyword = 'keyword4.DATA';
    protected $color = '#FFFFFF';

    /*protected function getKeywords()
    {
        return [
            "keyword1"=>"jeffrey name",
            "keyword2" =>"jeffrey age",
        ];
    }

    protected function getKeywordColors()
    {
        return [
            "keyword1"=>"#FFF000",
        ];
    }*/

}
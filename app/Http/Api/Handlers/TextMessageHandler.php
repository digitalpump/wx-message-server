<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/29
 * Time: 22:28
 */

namespace App\Http\Api\Handlers;


use App\Common\Tools\UserTools;
use EasyWeChat\Kernel\Contracts\EventHandlerInterface;
use EasyWeChat\Kernel\Messages\Article;
use Log;
class TextMessageHandler implements EventHandlerInterface
{
    public function handle($payload = null)
    {
        //Log::debug("@TextMessageHandler payload=".json_encode($payload));
       //MsgId
        $msgId = $payload['MsgId'];
        $openid = $payload['FromUserName'];
        $content = trim($payload['Content']);
        $user = UserTools::weChatUserRegisterAndLogin($payload['ToUserName'],$openid);
        if ($user==null) {
            Log::error("Login failed:".json_encode($payload));
            return "糟糕，我不认识你。";
        }
        if ($user->role==1) {
            return "老板你好。我会执行老板命令";
        }

        $haveInHandBiz = false;
        $bizOrder = UserTools::getBizOrder($user->id);
        if(!empty($bizOrder)) {
            $haveInHandBiz = true;
        }


        //自动用户注册
        //-->并返回用户ID 和 角色
        //登录后获取用户ID , 通过appid+ openid  key 保存倒redis


        //定义命令 ID 1223   char['我是谁','who an i']


        //帮助，命令行列表
        //
        //TODO 简单独立的命令优先级最高，比如查我的openid
        if ($content=="我是谁") {
            $article = new Article();
            $article->title  = "你是谁";
            $article->author = "jeffrey";
            $article->content = "你的openid:" . $openid;
            return $article;
        }

        if ($content =="什么情况") {
            return $this->queryProcessStatus($user->id);
        }



        //TODO 根据用户 openid 生成一个组合命令的（订单） 记录用户command  和 组合command的状态机 ，有时效性，过60分钟未处理自动清理


        /*
         * 比如用户开始注册公众号
         *
         * 1 步骤      状态机 process_status 0 等待
         */

        if (trim($content)=="我要上天") {
            if($haveInHandBiz) {
                return "";
            }
            $bizOrder = UserTools::createNewBizOrder($user->id);
            //TODO 查用户有没有注册角色
            //有注册成为商家

            //无注册，发送注册验证码

            //功能：

            /**
             *   注册账号  openid ,appid 公众号
             *
             *
             *
             *
             *   查寻当前办理业务 （类似订单）
             *      如果有，提示用户下一步应该做什么
             *
             *   用户配合执行命令
             *    更新业务状态，继续下一步
             *
             *   确认订单信息
             *
             *   一项一项确认
             *
             *
             *   对 打 1
             *   修改  2
             *
             *
             *
             */
            return "OK. 请继续完成您的配置，请输入：".$bizOrder->update_code  .",appid,你的微信appid";
        }
       //Log::debug("@TextMessageHandler from user:".$payload['FromUserName']);
       //Log::debug("@TextMessageHandler Content:".$payload['Content']);
    }


    private function doContinueBizOrder($bizOrder,$content) {

    }

    /**
     * @param $uid
     */
    private function queryProcessStatus($uid) {
        return "不知道呢";
    }




}
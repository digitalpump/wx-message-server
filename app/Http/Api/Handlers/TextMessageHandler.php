<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/29
 * Time: 22:28
 */

namespace App\Http\Api\Handlers;


use App\Common\Tools\CommonTools;
use App\Common\Tools\Db\WechatBizAccoutRegisterDbTools;

use Log;
class TextMessageHandler implements WeChatMessageHandler
{
    public function handle($payload = null)
    {
        //Log::debug("@TextMessageHandler payload=".json_encode($payload));
       //MsgId
        $msgId = $payload['MsgId'];
        $openid = $payload['FromUserName'];
        $content = $payload['Content'];
        $user = WechatBizAccoutRegisterDbTools::weChatUserRegisterAndLogin($payload['ToUserName'],$openid);
        if ($user == null) {
            Log::error("Login failed:".json_encode($payload));
            return "糟糕，我不认识你。";
        }

        //TODO 登录后获取用户ID , 通过appid+ openid  key 保存倒redis,没必要，用户量必然不大

        if ($user->role==1) {
            return "老板来了？需要我做什么您吩咐";
        }

        $haveInHandBiz = false;
        $bizOrder = WechatBizAccoutRegisterDbTools::getBizOrder($user->id);
        if(!empty($bizOrder)) {
            if($bizOrder->process_status!=6) $haveInHandBiz = true;
        }

        //定义命令 ID 1223   char['我是谁','who an i']


        //帮助，命令行列表
        //
        //TODO 简单独立的命令优先级最高，比如查我的openid
        if ($content=="我是谁") {

            return $openid;
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

            if ($haveInHandBiz) return $this->getPromptByProcessStatus($bizOrder->process_status,$bizOrder->update_code);

            if (empty($bizOrder)) {
                $bizOrder = WechatBizAccoutRegisterDbTools::createNewBizOrder($user->id);
                return $this->getPromptByProcessStatus($bizOrder->process_status,$bizOrder->update_code);
            } else {
                return "你已经完成注册，请输入（什么情况）查询结果";
            }


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

        }

        if($haveInHandBiz) {
            //if(empty($content)) return $this->getPromptByProcessStatus($bizOrder->process_status,$bizOrder->update_code);
            return $this->doContinueBizOrder($bizOrder,$content);
        } else {
            return $this->returnHelp();
        }
       //Log::debug("@TextMessageHandler from user:".$payload['FromUserName']);
       //Log::debug("@TextMessageHandler Content:".$payload['Content']);
    }

    private function returnHelp(){
        return "命令参考：我是谁,什么情况,其它密秘指令不告诉你：）";
    }

    private function getPromptByProcessStatus($status,$update_code) {
        switch ($status){
            case 0:
                return "请输入括号中的内容完成您的配置：（".$update_code  .",appid,你的微信appid)中间以,或空格隔开";
                break;
            case 2:
                return "请输入括号中的内容完成您的配置：（".$update_code  .",secret,你的微信app_secret)中间以,或空格隔开";
                break;
            case 4:
                return "请输入括号中的内容完成您的配置：（".$update_code  .",appid,你的微信appid)中间以,或空格隔开";
                break;
            case 6:
                return "你已经完成注册，请输入（什么情况）查询结果";
                break;
        }
    }

    private function doContinueBizOrder(&$bizOrder,$content) {

        Log::debug("content=".$content);
        $temp = preg_split("/[\s,，]+/",$content);
        Log::debug("after split=".json_encode($temp));
        if (sizeof($temp)<3) {
            return $this->getPromptByProcessStatus($bizOrder->process_status,$bizOrder->update_code);
        }
        list($update_code,$cmd,$value) = $temp;
        Log::debug("$update_code--$cmd--$value");
        if(empty($update_code)||empty($cmd)||empty($value)) return "命令行正确格式如下：108908,appid或者secret,对就的微信appid或secret内容";

        if ($update_code!=$bizOrder->update_code) {
            return "更新代码错误。";
        }
        if(!in_array($cmd,['appid','secret','reset'])) {
            return "指令错误。只接受：appid或secret";
        }

        if($cmd=='reset') {
            if(WechatBizAccoutRegisterDbTools::resetBizOrder($bizOrder)) {
                return "重置成功，请重新开始注册申请。";
            } else {
                return "重置失败，请稍后再试。";
            }
        }
        //TODO reset
        $value=trim($value);
        if(empty($value)) {
            return "内容不能空";
        }
        if(!CommonTools::alnumCheck($value)) return "输入的微信appid 或 secret不符合要求。";
        $step = $cmd=="appid"?1:2;
        $result =  WechatBizAccoutRegisterDbTools::updateBizOrder($bizOrder,$step,$value);
        if(empty($result)) {
            return "操作更新失败。如果一直出现，可能是你的appid和别人的重了，你可以输入:（" . $update_code . ",reset,OK） 来重新注册";
        } else {
            return $step==1?"操作成功，请继续输入微信secret完成注册":"操作成功，等待后台审核中。可输入命令：（什么情况）查看审核结果";
        }
    }

    /**
     * @param $uid
     */
    private function queryProcessStatus($uid) {
        $wxAccount = WechatBizAccoutRegisterDbTools::getWxAccount($uid);
        if(empty($wxAccount)) return "不知道呢";
        switch ($wxAccount->status){
            case -1:
                return "你的微信账号appid：" . $wxAccount->wx_appid . " 还在审核中";
            case 0:
                return "你的微信账号appid：" . $wxAccount->wx_appid . " 被禁用";
            case 1:

                if($wxAccount->running_status ==1)  return "你的微信账号appid：" . $wxAccount->wx_appid . " 已经通过并运行正常，可以正常发送消息了";
                if($wxAccount->running_status ==0)  return "你的微信账号appid：" . $wxAccount->wx_appid . " 运行错误，错误信息：" . $wxAccount->error_message;
                break;
        }
        return "不知道呢";
    }




}
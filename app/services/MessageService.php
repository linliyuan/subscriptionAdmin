<?php


namespace Services;

use App\Libs\MqProduct;
use Models\MessageModel;
use Phalcon\Di;
use Utils\RedisKey;
const MESSAGE_TYPE = [
    "GROUP"   => 1,
    "PRIVATE" => 2,
];


class MessageService {
    public static function messagePush($messageData) {
        // 消息入库
        $message = new MessageModel();
        $message->title = self::setMsgTitle($messageData);
        $message->status = 0;
        $message->content = $messageData["content"];
        $message->files = $messageData["files"] ?? "[]";
        $message->type = $messageData["type"];
        $message->touser = $messageData["touser"];
        $message->sender = $messageData["sender"];
        $message->workSetId = $messageData["workSetId"];
        if ($message->save()){
            $messageId = $message->id;
        }else {
            throw new \Exception("插入失败");
        }
        // 消息推送
        MqProduct::insertMq("message-push", ["msg_id" => $messageId], true);
    }

    public static function groupMessagePush($messageData) {
        $senders = $messageData["senders"];
        foreach ($senders as $sender){
            // 消息入库
            $message = new MessageModel();
            $message->title = self::setMsgTitle($messageData);
            $message->status = 0;
            $message->content = $messageData["content"];
            $message->files = $messageData["files"] ?? "[]";
            $message->type = $messageData["type"];
            $message->touser = $messageData["touser"];
            $message->sender = $sender;
            $message->workSetId = $messageData["workSetId"];
            if ($message->save()){
                $messageId = $message->id;
            }else {
                throw new \Exception("插入失败");
            }
            // 消息推送
            MqProduct::insertMq("message-push", ["msgId" => $messageId], true);
        }
    }

    public static function privateMessagePush($messageData) {
        // 写入
    }

    public static function setMsgTitle($messageData){
        $title = "";

        return $title;
    }

    public static function ConfirmMessageSend($msgId) {
        /**
         * @var \Redis $redis
         */
        $redis = Di::getDefault()->get('redis');
        $msg = $redis->Hget(RedisKey::H_MSG_DETAIL, $msgId);
        $msg = json_decode(json_encode($msg),1);
        // 发送消息
        SubscribeService::sendSubscribeMsg("wx3685ea18873c54e5", $msg["touser"], $msg["id"], time(), $msg["sender"]);
        // 重新怼入
        $redis->zAdd(RedisKey::Z_MSG_CONFIRM, [], time(), $msgId);
    }
}
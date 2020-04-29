<?php


use App\Libs\MqProduct;
use Exceptions\MsgNotFoundException;
use Models\UserModel;
use Phalcon\Di;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Utils\RedisKey;

class MessageTask extends \Phalcon\Cli\Task {
    function MessagePushAction() {
        $diConfig = Di::getDefault()["config"]["AMQP"];
        $prefix   = $diConfig('AMQP_PREFIX');
        $name     = "message-push";
        $exchange = $prefix . '-exchange-' . $name;
        $queue    = $prefix . '-queue-' . $name;
        $routing  = $prefix . '-routing-' . $name;

        $host  = $diConfig('AMQP_HOST');
        $port  = $diConfig('AMQP_PORT');
        $user  = $diConfig('AMQP_USER');
        $pass  = $diConfig('AMQP_PASS');
        $vhost = $diConfig('AMQP_VHOST');


        try {
            $connection = new AMQPStreamConnection($host, $port, $user, $pass, $vhost, false, 'AMQPLAIN', null, 'en_US', 3.0, 1300, null, false, 600); // 创建连接
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        $channel = $connection->channel();

        $channel->queue_declare($queue, false, true, false, false);
        $channel->exchange_declare($exchange, 'direct', false, true, false);
        $channel->queue_bind($queue, $exchange, $routing);
        echo " [*] Waiting for messages. To exit press CTRL+C\n";
        $callback = function ($msg) {
            //数据错误则返回,数据输入为json格式
            $msg_body    = $msg->body;
            $msg_content = json_decode($msg_body, true);

            // 主要运行逻辑
            $this->handel($msg_content);

            # ack 确定
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);

        };

        $channel->basic_qos(null, 1, null);
        $channel->basic_consume($queue, '', false, false, false, false, $callback);

        while (count($channel->callbacks)) {
            $channel->wait();
        }
        $channel->close();
        $connection->close();
    }

    public function ConfirmMessagePushAction() {
        $now = time();
        $startTime = $now - 60;
        /**
         * @var \Redis $redis
         */
        $redis = Di::getDefault()->get('redis');
        $msgs = $redis->zRange(RedisKey::Z_MSG_CONFIRM, $startTime, $now);
        foreach ($msgs as $msg){
            // 推入群发消息处理
            MqProduct::insertMq("message-group-push", ["msg_id" => $msg], true);
        }
    }

    function ConfirmMessageSendAction() {
        $diConfig = Di::getDefault()["config"]["AMQP"];
        $prefix   = $diConfig('AMQP_PREFIX');
        $name     = "message-group-push";
        $exchange = $prefix . '-exchange-' . $name;
        $queue    = $prefix . '-queue-' . $name;
        $routing  = $prefix . '-routing-' . $name;

        $host  = $diConfig('AMQP_HOST');
        $port  = $diConfig('AMQP_PORT');
        $user  = $diConfig('AMQP_USER');
        $pass  = $diConfig('AMQP_PASS');
        $vhost = $diConfig('AMQP_VHOST');


        try {
            $connection = new AMQPStreamConnection($host, $port, $user, $pass, $vhost, false, 'AMQPLAIN', null, 'en_US', 3.0, 1300, null, false, 600); // 创建连接
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        $channel = $connection->channel();

        $channel->queue_declare($queue, false, true, false, false);
        $channel->exchange_declare($exchange, 'direct', false, true, false);
        $channel->queue_bind($queue, $exchange, $routing);
        echo " [*] Waiting for messages. To exit press CTRL+C\n";
        $callback = function ($msg) {
            //数据错误则返回,数据输入为json格式
            $msg_body    = $msg->body;
            $msg_content = json_decode($msg_body, true);

            // 主要运行逻辑
            $this->handel($msg_content);

            # ack 确定
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);

        };

        $channel->basic_qos(null, 1, null);
        $channel->basic_consume($queue, '', false, false, false, false, $callback);

        while (count($channel->callbacks)) {
            $channel->wait();
        }
        $channel->close();
        $connection->close();
    }

}
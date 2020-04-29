<?php


namespace App\Libs;

use Phalcon\Di;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class MqProduct
{
    /**
     * @param $name string 队列名
     * @param array $data 传输数据
     * @param array $config 自定义配置
     * @param bool $is_routing 是否使用路由
     * @throws \Exception
     */
    public static function insertMq($name, $data = [],$config = [], $is_routing = false)
    {
        $diConfig = Di::getDefault()["config"]["AMQP"];

        if (!empty($config)){
            $host = $config['amqp_host'];
            $port = $config['amqp_port'];
            $user = $config['amqp_user'];
            $pass = $config['amqp_pass'];
            $vhost = $config['amqp_vhost'];
        }else{
            $host  = $diConfig('AMQP_HOST');
            $port  = $diConfig('AMQP_PORT');
            $user  = $diConfig('AMQP_USER');
            $pass  = $diConfig('AMQP_PASS');
            $vhost = $diConfig('AMQP_VHOST');
        }

        $prefix   = $diConfig('AMQP_PREFIX');
        $exchange = $prefix . '-exchange-' . $name;
        $queue    = $prefix . '-queue-' . $name;
        $routing  = $prefix . '-routing-' . $name;

        $connection = new AMQPStreamConnection($host,$port,$user,$pass,$vhost); // 创建连接
        $channel    = $connection->channel();
        $channel->queue_declare($queue, false, true, false, false);
        $channel->exchange_declare($exchange, 'direct', false, true, false);
        if ($is_routing){
            $channel->queue_bind($queue, $exchange, $routing); // 队列和交换器绑定
        }else{
            $channel->queue_bind($queue, $exchange); // 队列和交换器绑定
        }
        $messageBody = json_encode($data,JSON_UNESCAPED_UNICODE) ?? ""; // 要推送的消息
        $message     = new AMQPMessage($messageBody, ['content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);
        if ($is_routing){
            $channel->basic_publish($message, $exchange,$routing); // 推送消息
        }else{
            $channel->basic_publish($message, $exchange); // 推送消息
        }
        $channel->close();
        $connection->close();
    }

}
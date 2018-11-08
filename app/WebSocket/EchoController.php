<?php

namespace App\WebSocket;

use Swoft\Http\Message\Server\Request;
use Swoft\Http\Message\Server\Response;
use Swoft\WebSocket\Server\Bean\Annotation\WebSocket;
use Swoft\WebSocket\Server\HandlerInterface;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

/**
 * Class EchoController
 * @package App\WebSocket
 * @WebSocket("/echo")
 */
class EchoController implements HandlerInterface
{

    private $prefix = 'app:login:';
    /**
     * {@inheritdoc}
     */
    public function checkHandshake(Request $request, Response $response): array
    {
        return [0, $response];
    }

    /**
     * @param Server $server
     * @param Request $request
     * @param int $fd
     */
    public function onOpen(Server $server, Request $request, int $fd)
    {
        $server->push($fd, 'hello, welcome! :)');
    }

    /**
     * @param Server $server
     * @param Frame $frame
     */
    public function onMessage(Server $server, Frame $frame)
    {
        $msg = $frame->data;
        if (stripos($msg, 'logOut')) {
            $phone = substr($msg,  0, strpos($msg, 'logOut'));
            $fds = cache()->hgetall($this->prefix . $phone);
            foreach ($fds as $val) {
                $server->push($val, 'logOut');
            }
            cache()->del($this->prefix . $phone);
        } else {
            if ($frame->data != 'appheartbeat' && $frame->data != 'heartbeat') {
                cache()->hset($this->prefix . $frame->data, time() , $frame->fd);
                cache()->set($this->prefix . $frame->fd, $frame->data);
            }
        }
    }

    /**
     * @param Server $server
     * @param int $fd
     */
    public function onClose(Server $server, int $fd)
    {
        // do something. eg. record log, unbind user ...
        $phone = cache()->get($this->prefix . $fd);
        $fds = cache()->hgetall($this->prefix . $phone);
        foreach ($fds as $key => $val) {
            if ($val == $fd) {
                cache()->hdel($this->prefix . $phone, $key);
            }
        }
        cache()->del($this->prefix . $fd);
    }

    public function sendMessage(Server $server, int $fd, string $msg)
    {
        \Swoft::$server->sendToAll($msg);
//        \Swoft::$server->sendToAll('');
    }

    public function sendTo(Server $server, int $fd, string $msg)
    {
        \Swoft::$server->sendTo($fd, $msg);
    }
}

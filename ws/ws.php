<?php
use MyApp\Pusher;
require __DIR__ . '/vendor/autoload.php';

$loop = React\EventLoop\Factory::create();
$pusher = new MyApp\Pusher;
//ratchet/src/Ratchet/MyApp

// Listen for the web server to make a ZeroMQ push after an ajax request
$context = new React\ZMQ\Context($loop);
$pull = $context->getSocket(ZMQ::SOCKET_PULL);
$pull->bind('tcp://*:5555'); // Binding to 127.0.0.1 means the only client that can connect is itself
$pull->on('message', array($pusher, 'onBlogEntry'));
// Set up our WebSocket server for clients wanting real-time updates
$webSock = new React\Socket\Server($loop);
$webSock->listen(8080, '127.0.0.1'); // Binding to 0.0.0.0 means remotes can connect
$webServer = new Ratchet\Server\IoServer(
                new Ratchet\WebSocket\WsServer(
                        new Ratchet\Wamp\WampServer($pusher)
                ),
                $webSock
        );

$loop->run();
//die(var_dump($loop->loop));


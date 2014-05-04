<?php

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Ratchet\Http\HttpServer;
use Ratchet\Wamp\WampServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\Server\IoServer;
use React\EventLoop\Factory;
use React\Socket\Server;
use React\ZMQ\Context;
use SEEQD\Connection;
use SEEQD\Messenger;

require dirname(__DIR__) . '/vendor/autoload.php';

$logDir = __DIR__.'/../logs';

if (!file_exists($logDir)) {
    if (!mkdir($logDir, 0774, true)) {
        throw new \RunTimeExeption(sprintf('Failed to create log dir %s', $logDir));
    }    
}

// initialize logger
$logger = new Logger('SEEQD');
$logger->pushHandler(new StreamHandler($logDir.'/seeqd.log', Logger::INFO));
$logger->info('Logger initialized');

$loop = Factory::create();

$connection = new Connection($logger);
$messenger = new Messenger($logger);

$context = new Context($loop);

$pull = $context->getSocket(\ZMQ::SOCKET_PULL);
$pull->bind('tcp://127.0.0.1:5555');
$pull->on('message', array($messenger, 'send'));

// // Set up our WebSocket server for clients wanting real-time updates
$webSock = new Server($loop);
$webSock->listen(8080, '0.0.0.0'); // Binding to 0.0.0.0 means remotes can connect

$server = new IoServer(
    $connection,
    $webSock,
    $loop
);

$loop->run();

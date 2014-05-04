<?php

namespace SEEQD;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Connection implements MessageComponentInterface 
{
    protected $clients;
    protected $logger;
    protected $messenger;

    public function __construct($logger)
    {
        $this->logger = $logger;
        $this->clients = new \SplObjectStorage;
        $this->logInfo('connection initialized');
    }

    public function onOpen(ConnectionInterface $conn)
    {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);

        $this->logInfo(sprintf("New connection! %s", $conn->resourceId));
    }

    public function onMessage(ConnectionInterface $conn, $msg)
    {
    }

    public function onClose(ConnectionInterface $conn)
    {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        $this->logInfo(sprintf("Connection %s has disconnected", $conn->resourceId));
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $this->logger->error(sprintf("An error has ocurred: %s", $e->getMessage()));

        $conn->close();
    }

    private function logInfo($msg)
    {
        $this->logger->info($msg);
    }
}

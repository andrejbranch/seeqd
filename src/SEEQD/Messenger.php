<?php

namespace SEEQD;

class Messenger
{
    private $logger;

    public function __construct($logger)
    {
        $this->logger = $logger;
        $this->logInfo('messenger initialized');
    }

    public function send($msg)
    {
        $this->logInfo(sprintf('Messenger: sending message %s', $msg));
    }

    private function logInfo($msg)
    {
        $this->logger->info($msg);
    }
}

<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

class Helpers
{

    private $langue;
    public function __construct(private LoggerInterface $logger) {
    }
    public function azizCisse(): string {
        $this->logger->info('Je dis Salam');
        return 'Salam';
    }
}
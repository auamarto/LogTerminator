<?php

namespace LogsTerminator2k;

use DateTime;

class LogCleaner
{
    private LogManager $logManager;

    public function __construct(LogManager $logManager)
    {
        $this->logManager = $logManager;
    }

    public function deleteLogsOlderThan(DateTime $dateTime) {
        $this->logManager->deleteLogsOlderThan($dateTime);
    }
}
<?php

namespace LogsTerminator2k;

use DateTime;

interface LogManager
{
    function deleteLogsOlderThan(DateTime $dateTime);
}
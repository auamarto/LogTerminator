<?php

namespace LogsTerminator2k;

use DateTime;
use LogsTerminator2k\Model\FileLogLine;
use SplFileObject;
use SplTempFileObject;

class FileLogManager implements LogManager
{
    /**
     * @var string[]
     */
    private iterable $files;

    /**
     * FileLogManager constructor.
     * @param string[] $LogFilesPats
     */
    public function __construct(iterable $logFilesPaths)
    {
        $this->files = $logFilesPaths;
    }

    public function deleteLogsOlderThan(DateTime $dateTime)
    {
        foreach ($this->files as $filePath) {
            $file = new SplFileObject($filePath, "r+");
            $file->setFlags(7);
            $file->flock(LOCK_EX);

            $temp = $this->generateTempFileWithCleanLogs($file, $dateTime);

            $file->ftruncate(0);
            $file->rewind();

            foreach( $temp as $line ){
                $file->fwrite($line);
            }

            $temp->flock(LOCK_UN);
            $file->flock(LOCK_UN);
        }
    }

    private function generateTempFileWithCleanLogs(SplFileObject $file, DateTime $dateTime): SplTempFileObject
    {
        $temp = new SplTempFileObject(0);
        $temp->flock(LOCK_EX);

        foreach( $file as $line ){
            if($dateTime < $this->extractCreatedAt($line)) {
                $temp->fwrite($line.PHP_EOL);
            }
        }

        return $temp;
    }

    private function extractCreatedAt(string $line): DateTime
    {
        $explodedLine = explode(" ", $line, 3);
        return date_create_from_format('d/m/Y H:i:s', $explodedLine[0]." ".$explodedLine[1]);
    }
}
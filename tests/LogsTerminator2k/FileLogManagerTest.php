<?php

namespace LogsTerminator2k;

use DateTime;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class FileLogManagerTest extends TestCase
{
    private array $logs = [
        './tests/resources/tmp/testLog1.log',
        './tests/resources/tmp/testLog2.log'
    ];

    /**
     * @before
     */
    public function copyLogFiles(): void
    {
        $source = './tests/resources/testLog.log';
        $destination = $this->logs[0];
        $destination2 = $this->logs[1];

        copy($source, $destination);
        copy($source, $destination2);
    }

    /**
     * @after
     */
    public function deleteTempFiles(): void
    {
        $destination = $this->logs[0];
        $destination2 = $this->logs[1];

        unlink($destination);
        unlink($destination2);
    }

    private function getLines($file): int
    {
        $f = fopen($file, 'r');
        $lines = 0;

        while (!feof($f)) {
            $lines += substr_count(fread($f, 8192), "\n");
        }

        fclose($f);

        return $lines;
    }

    /**
     * @test
     */
    public function throwErrorWhenFileNotExist() {
        $paths = ["notExisting.log"];
        $manager = new FileLogManager($paths);

        $this->expectException(RuntimeException::class);
        $manager->deleteLogsOlderThan(new \DateTime());
    }

    /**
     * @test
     */
    public function cleanLogsShouldRemoveSomeLinesFromLog() {
        $manager = new FileLogManager($this->logs);

        $file1NoOfLines = $this->getLines($this->logs[0]);
        $file2NoOfLines = $this->getLines($this->logs[1]);

        $manager->deleteLogsOlderThan(\DateTime::createFromFormat('d/m/Y H:i:s', '12/11/2010 11:46:36'));

        $this->assertLessThan($file1NoOfLines, $this->getLines($this->logs[0]));
        $this->assertLessThan($file2NoOfLines, $this->getLines($this->logs[1]));
    }

    /**
     * @test
     */
    public function nowShouldRemoveAllLogs() {
        $manager = new FileLogManager($this->logs);

        $manager->deleteLogsOlderThan(new DateTime());

        $this->assertEquals(0, $this->getLines($this->logs[0]));
        $this->assertEquals(0, $this->getLines($this->logs[1]));
    }

    /**
     * @test
     */
    public function oldDateWillNotRemoveAnything() {
        $manager = new FileLogManager($this->logs);

        $file1NoOfLines = $this->getLines($this->logs[0]);
        $file2NoOfLines = $this->getLines($this->logs[1]);

        $manager->deleteLogsOlderThan(\DateTime::createFromFormat('d/m/Y H:i:s', '12/11/2000 11:46:36'));

        $this->assertGreaterThanOrEqual($file1NoOfLines, $this->getLines($this->logs[0]));
        $this->assertGreaterThanOrEqual($file2NoOfLines, $this->getLines($this->logs[1]));
    }
}

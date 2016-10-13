<?php
/**
 * @author Felix A. Milovanov <fmilovanov@yahoo.com>
 */
require_once(__DIR__  . '/ParallelProcessor.php');

class Printer extends ParallelProcessor
{
    protected function executeJob($data)
    {
        if (!is_int($data))
            return;

        usleep(500000 - $data * 50000);
        print "$data\n";
    }
}

$printer = new Printer(10);

for ($i = 0; $i < 10; $i++)
{
    $printer->runJob($i);
}

$printer->completeJobs();
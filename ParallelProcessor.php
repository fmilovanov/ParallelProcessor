<?php
/**
 * @author Felix A. Milovanov <fmilovanov@yahoo.com>
 */

abstract class ParallelProcessor
{
    private $__nprocesses;

    public function __construct($nprocesses)
    {
        $this->__nprocesses = $nprocesses;
    }

    protected function lock()
    {

    }

    protected function release()
    {
        
    }

    public function runJob($data)
    {

    }

    public function completeJobs()
    {

    }

    abstract protected function executeJob($data);
}

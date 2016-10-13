<?php
/**
 * @author Felix A. Milovanov <fmilovanov@yahoo.com>
 */

abstract class ParallelProcessor
{
    const DEFAULT_IPC_ID    = 0xdead;

    private $__ipc_id;
    private $__mess_queue;
    private $__semaphore;
    private $__processes;

    public function __construct($nprocesses, $ipc_id = self::DEFAULT_IPC_ID)
    {
        if (!function_exists('pcntl_fork'))
            throw new \Exception('pcntl_fork() not found; please, install PCNTL library');

        if (!function_exists('msg_get_queue'))
            throw new \Exception('msg_get_queue() not found; please, install library');

        if (!function_exists('sem_get'))
            throw new \Exception('sem_get() not found; please, install library');

        // create message queue
        $this->__ipc_id = $ipc_id;
        $this->__semaphore = sem_get($this->__ipc_id);
        $this->__mess_queue = msg_get_queue($ipc_id);

        for ($i = 0; $i < $nprocesses; $i++)
        {
            if ($pid = pcntl_fork())
            {
                $this->__processes[$pid] = true;
                continue;
            }

            // init a child, if needed
            if (method_exists($this, 'childInit()'))
                $this->childInit();

            // in a child - wait for jobs and execute them
            while(msg_receive($this->__mess_queue, 0, $msgtype, 16 * 1024, $data))
            {
                $this->executeJob($data);
            }

            // init a child, if needed
            if (method_exists($this, 'childShutdown()'))
                $this->childShutdown();

            // exit once message queue is destroyed - e.g. all jobs are processed
            exit();
        }
    }

    protected function lock()
    {
        sem_acquire($this->__semaphore);
    }

    protected function release()
    {
        sem_release($this->__semaphore);
    }

    public function runJob($data)
    {
        msg_send($this->__mess_queue, 1, $data);
    }

    public function completeJobs()
    {
        while(1)
        {
            // check if we have any messages in the queue
            $stat = msg_stat_queue($this->__mess_queue);
            if (!$stat['msg_qnum'])
                break;

            // if yes - sleep 1/10 of second
            usleep(100000);
        }

        // remove message queue - this will effectively send children a signal to kill themselves
        msg_remove_queue($this->__mess_queue);

        while(!empty($this->__processes))
        {
            $pid = pcntl_wait($status);
            if (array_key_exists($pid, $this->__processes))
                unset($this->__processes[$pid]);
        }

        sem_remove($this->__semaphore);
    }

    abstract protected function executeJob($data);
}

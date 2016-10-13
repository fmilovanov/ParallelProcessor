ParallelProcessor
=================

Why is this?
------------
Often we have tasks that take too long to run, and we hit the wall optimizing
them. Parallel processing is one of ways to solve this problem, but a few of
us know parallel processing is possible in PHP, as many (wrongly) perceive PHP
as a web-only simple language. Well - parallel processing IS possible in PHP,
and with this library it it very simple to do so.

How?
----
First of all, parallel processing is not a panacea; it's won't speed up ANY
task. In order to go parallel, you must break your TASK into JOBS: small 
pieces that can be completed independent of others. Once you broke your TASK
into JOBS, you must derive a class from a `ParallelProcessor`:

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

As you can see, the only function that we have (and we MUST have it) in our
class is `executeJob()` - it will be called every time a processor is available
and there is a JOB for it. In this example the job is simple - expect an int as
a parameter, wait some time and print the parameter. 

Here is how we execute it:

    $printer = new Printer(10);

    for ($i = 0; $i < 10; $i++)
    {
        $printer->runJob($i);
    }

    $printer->completeJobs();

First we create our parallel process. The argument (10) is number or parallel 
processes we want to run. Once it's created, we start feeding it with the jobs.
Job is represented by a data what will be processed; in this case it's simple
numbers, but it can be filenames, database IDs, whole object - anything, that
can be serialized. This data will be packed in the main process, sent to one 
of parallel processed and passed into `executeJob()` function. 

Synchronization
---------------
Sometimes you need synchronize your jobs. An example would be you need to save 
results to the same file; you don't want two processed to write into the file
in parallel. Therefore, there is a synchronization mechanism. BEFORE you want
to start doing something exclusive (e.g. writing into a file), call `lock()`
method. It will lock your resource, or wait until it's unlocked by another 
process who locked it. Once you're done working with the resource - call 
`release()` method, and the resource will be available for other processes:

    protected function executeJob($data)
    {
        // process data

        $this->lock();
        fputs($file, $data);
        $this->release();
    }

Please note that if forgot to unlock, all other processes will be blocked trying
to acquire the lock, and your script will be frozen. 


How many processed do I need?
-----------------------------
There are different types of tasks that need to be parallelized. One type is
CPU-intensive tasks: encrypt/decrypt something, convert audio/video, etc. For
those you should set number of processes to a number of CPUs available in your
system. You can set more processes, but it won't speed up your task, as 
processes will compete for CPU, and one will wait for another.

Another type of tasks involve accessing external resources, designed to handle
large amount of parallelism: Google, Facebook, public databases, etc, where
time mostly spent on WAITING for a response. For such tasks you can run dozens 
of parallel processes, just make sure they don't overload your server. An 
author had an experience running 128 processed feeding Google Base on a 
relatively weak server (over 10 years ago). 

Running under Apache
--------------------
When PHP is running as Apache module, `pcntl_fork()` function is disabled, so 
you will not be able to use this library. You may try to run PHP via FastCGI 
interface.


Licence
-------
[BSD] (https://en.wikipedia.org/wiki/BSD_licenses)

Author Information
------------------

* [Felix A. Milovanov](https://github.com/fmilovanov)
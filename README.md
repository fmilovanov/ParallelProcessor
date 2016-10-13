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
task. In order to go parallel, you must break your task into JOBS: small 
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
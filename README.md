ParallelProcessor
=================

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

Licence
-------
[BSD] (https://en.wikipedia.org/wiki/BSD_licenses)

Author Information
------------------

* [Felix A. Milovanov](https://github.com/fmilovanov)
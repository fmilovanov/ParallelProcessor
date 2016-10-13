ParallelProcessor
=================

How many processed do I need?
-----------------------------

  There are different types of tasks that need to be parallelized. One type is
CPU-intensive tasks: encrypt/decrypt something, convert audio/video, etc. For
those you should set number of processes to a number of CPUs available in your
system. You can set some processes, but it won't speed up your task, as they 
will compete for CPU, and one will wait for another.

  Another type of tasks involve accessing external resources, designed to handle
large amount of parallelizm: Google, Facebook, public databases, etc, where
most of time spent on working on those resources is WAITING for response. For
such tasks you can run dozens of parallel processes, just make sure they don't
ruin your server: an author had experience running 128 processed feeding Google
Base on a relatively weak server (over 10 years ago). 
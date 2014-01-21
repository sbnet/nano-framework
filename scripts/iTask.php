<?php
namespace Scripts;

/**
* Interface for the tasks, they all must have a run method
*/
interface iTask
{
    public function __construct($cli);
    public function run();
    public function help();
}

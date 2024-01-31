<?php
    namespace Hdgarau\Runners;

    interface iRunner
    {
        public function handler() : bool;
        public function dependencies() : array;
        public function runnedOnce() : array;
        public function canRun() : bool|callable;
    }
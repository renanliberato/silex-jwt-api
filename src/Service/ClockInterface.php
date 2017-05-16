<?php

namespace App\Service;

interface ClockInterface
{
    public function now();
    
    public function plusTwentyMinutes();

    public function plusFiveMinutes();
}
<?php

namespace App\Service;

use App\Service\ClockInterface;

class Clock implements ClockInterface
{
    public function now()
    {
        return strtotime('now');
    }

    public function plusTwentyMinutes()
    {
        return strtotime('+20 minutes');
    }

    public function plusFiveMinutes()
    {
        return strtotime('+5 minutes');
    }
}
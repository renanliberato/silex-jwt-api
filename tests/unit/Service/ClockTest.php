<?php

namespace AppTest\Service;

use App\Service\Clock;
use PHPUnit\Framework\TestCase;

class ClockTest extends TestCase
{
    private $clock;

    public function setUp()
    {
        $this->clock = new Clock();
    }

    public function testNowMethodReturnsAnInteger()
    {
        $now = $this->clock->now();

        $this->assertInternalType('int', $now);
        $this->assertTrue($now > 0);

        return $now;
    }

    /**
     * @depends testNowMethodReturnsAnInteger
     */
    public function testPlusFiveMinutesMethodReturnsAnInteger($now)
    {
        $plusFiveMinutes = $this->clock->plusFiveMinutes();

        $this->assertInternalType('int', $plusFiveMinutes);
        $this->assertTrue($plusFiveMinutes > $now);
    }

    /**
     * @depends testPlusFiveMinutesMethodReturnsAnInteger
     */
    public function testPlusTwentyMinutesMethodReturnsAnInteger($plusFiveMinutes)
    {
        $plusTwentyMinutes = $this->clock->plusTwentyMinutes();

        $this->assertInternalType('int', $plusTwentyMinutes);
        $this->assertTrue($plusTwentyMinutes > $plusFiveMinutes);

        return $plusTwentyMinutes;
    }
}
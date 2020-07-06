<?php

use Mockery as m;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    public function tearDown(): void
    {
        m::close();
    }
}
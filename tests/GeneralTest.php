<?php

use iPaymu\iPaymu;
use PHPUnit\Framework\TestCase;

final class GeneralTest extends TestCase
{

    public function testCheckBalance()
    {
        $iPaymu = new iPaymu($_SERVER['apiKey'], $_SERVER['va'], $_SERVER['production']);
        $this->assertArrayHasKey('Data', $iPaymu->checkBalance());
    }
}

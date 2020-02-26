<?php

use frankyso\iPaymu\iPaymu;
use PHPUnit\Framework\TestCase;

/**
 * @author Franky So <frankyso.mail@gmail.com>
 */
final class GeneralTest extends TestCase
{
    public function testCheckApiKey()
    {
        $iPaymu = new iPaymu($_SERVER['APP_KEY']);
        $this->assertEquals(true, $iPaymu->isApiKeyValid());
    }

    public function testCheckApiKeyFailure()
    {
        $this->assertEquals(false, (new iPaymu('123j12lkdasjfoisadoj'))->isApiKeyValid());
    }

    public function testCheckBalance(): void
    {
        $iPaymu = new iPaymu($_SERVER['APP_KEY']);
        $this->assertArrayHasKey('Saldo', $iPaymu->checkBalance());
    }
}

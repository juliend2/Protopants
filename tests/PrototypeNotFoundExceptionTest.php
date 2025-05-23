<?php

require __DIR__.'/../vendor/autoload.php';
/* require_once __DIR__.'/../main.php'; */

use Protopants\Prototype;
use Protopants\PrototypeNotFoundException;
use function Protopants\create;
use PHPUnit\Framework\TestCase;

class PrototypeNotFoundExceptionTest extends TestCase {

    function testPrototypeMethodMissing() {
        $this->expectException(PrototypeNotFoundException::class);
        create('DoesntExist', []);
    }

}


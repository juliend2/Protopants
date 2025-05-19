<?php

require __DIR__.'/../vendor/autoload.php';
/* require_once __DIR__.'/../main.php'; */

use Protopants\Prototype;
use Protopants\PrototypeMethodMissingException;
use function Protopants\create;
use PHPUnit\Framework\TestCase;

class PrototypeMethodMissingExceptionTest extends TestCase {

    function testPrototypeMethodMissing() {
        $prototype = Prototype::create('BobbyMethodMissing', [], methods: []);
        $this->expectException(PrototypeMethodMissingException::class);
        $prototype->talkToMe();
    }

    function testPrototypeOverrideMethodMissingException() {
        Prototype::get('Object')->setSingleMethod([
            'methodMissing' => function($self) {
                // Muffling the exception
            }
        ]);
        $obj = create('Object', []);
        $obj->boutique();
        $this->assertTrue(true); // no exception
    }
}


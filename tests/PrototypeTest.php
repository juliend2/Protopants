<?php

require __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../main.php';
require_once __DIR__.'/../src/PrototypeMethodMissingException.php';

use PHPUnit\Framework\TestCase;

class PrototypeTest extends TestCase {
    function testPrototypeName() {
        $prototype = Prototype::create('Human', properties: [], methods: []);
        $this->assertEquals('Human', $prototype->__prototypeName);
    }

    function testPrototypeChain() {
        $prototype = Prototype::create('Bob', [], methods: [
            'parler' => function ($self) {
                return 'allo';
            }
        ]);
        $this->assertEquals('Object', $prototype->parentPrototype->__prototypeName);
        $this->assertNull($prototype->parentPrototype->parentPrototype);
    }

    function testCallingParentMethod() {
        $prototype = Prototype::create('Bob', [], methods: []);
        $this->assertEquals('TODO', $prototype->toString());
    }

    function testCallingParentOfParentMethod() {
        Prototype::create('Bob', [], methods: []);
        $prototype = Prototype::extendPrototype('Bob', 'Bobby', [], []);
        $this->assertEquals('TODO', $prototype->toString());
    }

    function testPrototypeMethodMissing() {
        $prototype = Prototype::create('BobbyMethodMissing', [], methods: []);
        $this->expectException(PrototypeMethodMissingException::class);
        $prototype->talkToMe();
    }

}

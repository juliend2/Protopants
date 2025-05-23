<?php

require __DIR__.'/../vendor/autoload.php';

use Protopants\Prototype;
use Protopants\PrototypeMethodMissingExceptionTest;
use function Protopants\create;
use PHPUnit\Framework\TestCase;

class PrototypeTest extends TestCase {
    function testPrototypeName() {
        $object = create('Object');
        $this->assertEquals('[Object]', $object->toString());

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
        $this->assertEquals('[Bob]', $prototype->toString());
    }

    function testCallingParentOfParentMethod() {
        Prototype::create('Bob', [], methods: []);
        $prototype = Prototype::extend('Bobby', 'Bob', [], []);
        $this->assertEquals('[Bobby]', $prototype->toString());
    }

    function testAccessingFirstLevelPropertyFromMethod() {
        $prototype = Prototype::create('Friend',
            properties: [
                'name' => 'Benny',
            ], methods: [
                'getName' => function ($self) {
                    return "Hello, this is {$self->name}.";
                }
            ]
        );
        $this->assertEquals("Hello, this is Benny.", $prototype->getName());
    }

    function testAccessingOverriddenParentPropertyFromMethod() {
        Prototype::create('Friend',
            properties: [
                'name' => null,
            ], methods: [
                'getName' => function ($self) {
                    return "Hello, this is {$self->name}.";
                }
            ]
        );
        $friend = create('Friend', ['name' => 'Bobby']);
        $this->assertEquals("Hello, this is Bobby.", $friend->getName());
    }

    function testAccessingParentPropertyFromFirstLevelMethod() {
        Prototype::create('Person',
            properties: [
                'name' => 'Ben',
            ],
            methods: [
            ]
        );
        $benny = Prototype::extend('Friend', 'Person',
            properties: [
                'nickname' => 'Benny',
            ], methods: [
                'getName' => function ($self) {
                    return "Hello, this is {$self->name}.";
                }
            ]
        );
        $this->assertEquals("Hello, this is Ben.", $benny->getName());
    }

    function testGetParentMethod() {
        Prototype::create('VeryParent',
            methods: [
                'methode' => function ($self) {/* */}
            ]
        );
        Prototype::extend('Parent', 'VeryParent',
        );
        Prototype::extend('Child', 'Parent');

        $child = create('Child');
        $method = $child->getParentMethod('methode');
        $this->assertIsCallable($method);
    }
}

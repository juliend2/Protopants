<?php

require __DIR__.'/../vendor/autoload.php';

use Protopants\Prototype;
use Protopants\PrototypeMethodMissingExceptionTest;
use function Protopants\create;
use PHPUnit\Framework\TestCase;

class MixinTest extends TestCase {
    function testSimpleMixin() {
        Prototype::create('Wagon', [],
            methods: [
                'roll' => function ($self) { }
            ]
        );
        Prototype::create('Tank', [],
            methods: [
                'fire' => function ($self) { }
            ]
        );
        Prototype::create('Submarine', [],
            methods: [
                'sink' => function ($self) { }
            ]
        );

        $proto = Prototype::mixin('WarMachine', ['Wagon', 'Tank', 'Submarine']);

        $proto->roll();
        $proto->fire();
        $proto->sink();
        $this->assertTrue($noExceptionRaised = true);
    }
}


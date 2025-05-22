<?php

namespace Protopants;

use function Protopants\__slidingPairs;

class Prototype {
    public static $__prototypesRegistry = [];

    public $parentPrototype = null;
    public $__prototypeName = null;
    protected $properties = [];
    protected $methods = [];

    public static function get($prototypeName): static|null {
        if (isset(static::$__prototypesRegistry[$prototypeName])) {
            return static::$__prototypesRegistry[$prototypeName];
        }
        return null;
    }

    public static function create($prototypeName, array $properties = [], array $methods = []) {
        $parentPrototypeName = $prototypeName === 'Object' ? null : 'Object';
        static::$__prototypesRegistry[$prototypeName] = static::extend($prototypeName, $parentPrototypeName, $properties, $methods);
        return static::$__prototypesRegistry[$prototypeName];
    }

    public static function extend(string $prototypeName, string|null $parentPrototypeName, array $properties = [], array $methods = []) {
        $parentPrototype = Prototype::get($parentPrototypeName ?? 'Object');
        static::$__prototypesRegistry[$prototypeName] = new Prototype($properties, $methods);
        static::$__prototypesRegistry[$prototypeName]->setParentPrototype($parentPrototype);
        static::$__prototypesRegistry[$prototypeName]->setName($prototypeName);
        return static::$__prototypesRegistry[$prototypeName];
    }

    /**
     * Given an input: a, b, c, d
     * It will return a prototype that has the following structure of prototypes:
     * a:
     *  parentPrototype: b
     *   parentPrototype: c
     *    parentPrototype: d
     *     parentPrototype: Object <-- 'Object' is always the parent-most prototype.
     */
    public static function mixin($prototypeName, array $otherPrototypeNames): static|null {
        // Prepare input array:
        $propNames = $otherPrototypeNames;
        array_unshift($propNames, $prototypeName);
        $slidingPairs = __slidingPairs(
            // Reversed array so we it mimicks the way array_merge() works, where
            // it has the last element have the least precedence:
            array_reverse($propNames)
        );

        $lastProtoName = null;
        foreach ($slidingPairs as [$a, $b, $is_last]) {
            $createdProtoName = $is_last ? $b : ($b.'_With_'.$a);
            $rightMostProto = static::get($a);
            $extendProto = static::get($lastProtoName ?? $a);
            $createdProto = static::create($createdProtoName,
                properties: $rightMostProto->getProperties(),
                methods: $rightMostProto->getMethods(),
            );
            if (!is_null($lastProtoName)) { // So it's the first iteration...
                $createdProto->setParentPrototype($extendProto);
            }
            $lastProtoName = $createdProtoName;
            if ($is_last) {
                return $createdProto;
            }
        }
        return null;
    }

    public function setName($prototypeName) {
        $this->__prototypeName = $prototypeName;
    }

    public function __construct($properties, $methods) {
        $this->properties = $properties;
        $this->methods['methodMissing'] = function ($self, $methodName, $arguments) {
            if (!is_null($self->parentPrototype)) {
                return $self->parentPrototype->methodMissing($methodName, $arguments);
            }
            if (isset($self->methods[$methodName])) {
                return $self->methods[$methodName]($self, ...$arguments);
            }

            throw new PrototypeMethodMissingException("Method Missing: ".$methodName);
        };
        $this->methods = array_merge($this->methods, $methods);
    }

    public function setParentPrototype(Prototype|null $parentPrototype) {
        $this->parentPrototype = $parentPrototype;
    }

    public function setSingleMethod($method) {
        $this->methods = array_merge($this->methods, $method);
    }

    public function setProperties($properties) {
        $this->properties = array_merge($this->properties, $properties);
    }

    public function setMethods($methods) {
        $this->methods = array_merge($this->methods, $methods);
    }

    public function setInitialParams($properties) {
        $this->properties = array_merge($this->properties, $properties);
    }

    public function __call($methodName, $arguments) {
        $method = $this->getParentMethod($methodName);
        if (is_null($method)) {
            $methodMissingMethod = $this->getParentMethod('methodMissing');
            return $methodMissingMethod($this, $methodName, $arguments);
        }

        return $method($this, ...$arguments);
    }

    public function getProperties() {
        return $this->properties;
    }

    public function getMethods() {
        return $this->methods;
    }

    /**
     * (Recursive)
     */
    public function getParentMethod($methodName): Callable|null {
        if (isset($this->methods[$methodName])) {
            return $this->methods[$methodName];
        }
        return $this->parentPrototype?->getParentMethod($methodName);
    }

    public function __get($propertyName) {

        if (isset($this->properties[$propertyName])) {
            return $this->properties[$propertyName];
        }

        // this has no prop of that name...

        if (is_null($this->parentPrototype)) {
            return null;
        }
        // this has no prop of that name, nor does it have a null parent...

        // So maybe its parent has the property? (recursion happens)
        return $this->parentPrototype->{$propertyName};
    }
}

Prototype::create('Object',
    properties: [
    ],
    methods: [
        'toString' => function ($self) {
            $name = $self->__prototypeName ?? 'Object';
            return "[{$name}]";
        }
    ]
);

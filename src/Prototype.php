<?php

require_once __DIR__ . '/autorun.php';

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

    public static function create($prototypeName, array $properties, array $methods) {
        $parentPrototypeName = $prototypeName === 'Object' ? null : 'Object';
        // $parent = Prototype::get($parentPrototypeName);
        static::$__prototypesRegistry[$prototypeName] = static::extend($parentPrototypeName, $prototypeName, $properties, $methods);
        // static::$__prototypesRegistry[$prototypeName]->setParentPrototype($parent);
        return static::$__prototypesRegistry[$prototypeName];
    }

    public static function extend(string|null $parentPrototypeName, string $prototypeName, array $properties, array $methods) {
        $parentPrototype = Prototype::get($parentPrototypeName ?? 'Object');
        static::$__prototypesRegistry[$prototypeName] = new Prototype($properties, $methods);
        static::$__prototypesRegistry[$prototypeName]->setParentPrototype($parentPrototype);
        static::$__prototypesRegistry[$prototypeName]->setName($prototypeName);
        return static::$__prototypesRegistry[$prototypeName];
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
        if ($methodName === 'methodMissing' && isset($this->methods[$arguments[0]])) {
            $foundMethodName = array_shift($arguments);
            return $this->methods[$foundMethodName]($this, ...$arguments);
        }

        if (isset($this->methods[$methodName])) {
            return $this->methods[$methodName]($this, ...$arguments);
        }

        if (isset($this->methods['methodMissing'])) {
            return $this->methods['methodMissing']($this, $methodName, $arguments);
        }

        return $this->parentPrototype->methodMissing($methodName, $arguments);
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

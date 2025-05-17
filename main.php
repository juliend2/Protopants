<?php

// $__prototypesRegistry = [];

class Prototype {
    public static $__prototypesRegistry = [];

    public $parentPrototype = null;
    public $__prototypeName = null;
    protected $properties = [];
    protected $methods = [];

  public static function get($prototypeName): static|null {
    // global $__prototypesRegistry;
    if (isset(static::$__prototypesRegistry[$prototypeName])) {
      return static::$__prototypesRegistry[$prototypeName];
    }
    return null;
  }

    public static function create($prototypeName, array $properties, array $methods) {
        static::$__prototypesRegistry[$prototypeName] = static::extendPrototype('Object', $prototypeName, $properties, $methods);
        // $__prototypesRegistry[$prototypeName] = new Prototype($properties, $methods);
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

            // if (isset($this->parentPrototype->methods[$methodName])) {
            //   return $this->parentPrototype->methods[$methodName]($this, ...$arguments);
            // }


            throw new PrototypeMethodMissingException("Method Missing: ".$methodName);
        };
        $this->methods = array_merge($this->methods, $methods);
    }

  public static function extendPrototype(string $parentPrototypeName, string $prototypeName, array $properties, array $methods) {
    $parentPrototype = Prototype::get($parentPrototypeName ?? 'Object');
    static::$__prototypesRegistry[$prototypeName] = new Prototype($properties, $methods);
    static::$__prototypesRegistry[$prototypeName]->setParentPrototype($parentPrototype);
    static::$__prototypesRegistry[$prototypeName]->setName($prototypeName);
    return static::$__prototypesRegistry[$prototypeName];
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
    return $this->properties[$propertyName] ?? null;
  }
}

class SubPrototype extends Prototype {
  public function __construct($properties, $methods) {
    $this->properties = $properties;
    // the difference is that we don't want to override the base methodMissing
    $this->methods = array_merge($this->methods, $methods);
  }
}

Prototype::create('Object',
  properties: [
  ],
  methods: [
    'toString' => function ($self) {
      // $str = "Mon nom est {$self->nom} et j'ai {$self->age} ans.";
      // return $str;
      // TODO: get the name of that object's prototype
      return "TODO";
    }
  ]
);


function setMethod(string $prototypeName, string $methodName, callable $methodBody) {
  $prototype = Prototype::get($prototypeName);
  $prototype->setSingleMethod([$methodName => $methodBody]);
}

function create(string $prototypeName, array $initialParams = []) {
  $parentPrototype = Prototype::get($prototypeName);
  $cloned = new SubPrototype($initialParams ?? [], []);
  $cloned->setParentPrototype($parentPrototype);
  $cloned->setInitialParams($initialParams);
  return $cloned;
}

$humainProto = Prototype::create('Human',
  properties: [
    'nom' => '',
    'age' => 0,
  ],
  methods: [
    'parler' => function ($self) {
      // $str = "Mon nom est {$self->nom} et j'ai {$self->age} ans.";
      $str = "Bonjour.";
      return $str;
    }
  ]
);

// assert(get_class($humainProto) === 'Prototype');

// $julien = create('Human', ['nom'=>'Julien', 'age' => 39]);
// // echo $julien->parler() . "\n";
// // assert($julien->parler() === "Mon nom est Julien et j'ai 39 ans.");
// // assert($julien->parler() === "Bonjour.");

// echo $julien->toString(). "\n";
// assert($julien->toString() === 'TODO');

// setMethod('Human', 'parler', function ($self) {
//   return "My name is {$self->nom} and I'm {$self->age} years old.";
// });

// function oui() {
//   global $julien;
//   $nathalie = create('Human', ['nom'=>'Nat', 'age' => 29]);
//   echo $nathalie->parler() . "\n";
//   assert($nathalie->parler() === "My name is Nat and I'm 29 years old."); // has the new parler() implementation
//   echo $julien->parler() . "\n";
//   assert($julien->parler() === "My name is Julien and I'm 39 years old."); // still has the old parler() method implementation
// }
// oui();

// $coughtException = false;
// try {
//   $julien->jaser();
// } catch (PrototypeMethodMissingException $e) {
//   // SHOULD go there since the parent prototype's methodMissing throws an Exception
//   $coughtException = true;
// }
// assert($coughtException);

// setMethod('Human', 'methodMissing', function ($self, $methodName) {
//   echo "Purée! C'est platte mais $methodName est pas implémenté.\n";
// });

// $hadNoException = true;
// try {
//   $julien->jaser();
// } catch (PrototypeMethodMissingException $e) {
//   // Should not go there, since it will execute its own methodMissing method which doesn't throw an Exception.
//   $hadNoException = false;
// }
// assert($hadNoException);



// echo "Everything is Good.\n";

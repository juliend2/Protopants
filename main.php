<?php

class PrototypeMethodMissingException extends Exception { }

$__prototypesRegistry = [];

class Prototype {
  protected $parentPrototype = null;
  protected $properties = [];
  protected $methods = [];

  public static function get($prototypeName): static|null {
    global $__prototypesRegistry;
    if (isset($__prototypesRegistry[$prototypeName])) {
      return $__prototypesRegistry[$prototypeName];
    }
    return null;
  }

  public static function create() {
    // TODO
  }

  public function __construct($properties, $methods) {
    $this->properties = $properties;
    $this->methods['methodMissing'] = function ($self, $methodName, $arguments) {
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
    if (isset($this->methods[$methodName])) {
      return $this->methods[$methodName]($this, ...$arguments);
    }

    if (isset($this->parentPrototype->methods[$methodName])) {
      return $this->parentPrototype->methods[$methodName]($this, ...$arguments);
    }
    
    return $this->methodMissing($methodName, $arguments);
  }

  public function __get($propertyName) {
    return $this->properties[$propertyName] ?? null;
  }
}

class SubPrototype extends Prototype {
  public function __construct($properties, $methods) {
    $this->properties = $properties;
    $this->methods = array_merge($this->methods, $methods);
  }
}

prototype('Object',
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

function prototype(string $prototypeName, array $properties, array $methods) {
  global $__prototypesRegistry;
  $__prototypesRegistry[$prototypeName] = extendPrototype('Object', $prototypeName, $properties, $methods);
  $__prototypesRegistry[$prototypeName] = new Prototype($properties, $methods);
  return $__prototypesRegistry[$prototypeName];
}

function extendPrototype(string $parentPrototypeName, string $prototypeName, array $properties, array $methods) {
  $parentPrototype = Prototype::get($parentPrototypeName ?? 'Object');
  $__prototypesRegistry[$prototypeName] = new Prototype($properties, $methods);
  $__prototypesRegistry[$prototypeName]->setParentPrototype($parentPrototype);
  return $__prototypesRegistry[$prototypeName];
}

function setMethod(string $prototypeName, string $methodName, callable $methodBody) {
  global $__prototypesRegistry;
  $prototype = $__prototypesRegistry[$prototypeName];
  // $prototype->setMethods([$methodName => $methodBody]);
  $prototype->setSingleMethod([$methodName => $methodBody]);
}

function create(string $prototypeName, array $initialParams = []) {
  // TODO: use Prototype::get($prototypeName) instead
  global $__prototypesRegistry;
  $cloned = new SubPrototype($initialParams ?? [], []);
  $cloned->setParentPrototype($__prototypesRegistry[$prototypeName]);
  $cloned->setInitialParams($initialParams);
  return $cloned;
}

$humainProto = prototype('Human',
  properties: [
    'nom' => '',
    'age' => 0,
  ],
  methods: [
    'parler' => function ($self) {
      $str = "Mon nom est {$self->nom} et j'ai {$self->age} ans.";
      return $str;
    }
  ]
);

assert(get_class($humainProto) === 'Prototype');

$julien = create('Human', ['nom'=>'Julien', 'age' => 39]);
assert($julien->parler() === "Mon nom est Julien et j'ai 39 ans.");


setMethod('Human', 'parler', function ($self) {
  return "My name is {$self->nom} and I'm {$self->age} years old.";
});

function oui() {
  global $julien;
  $nathalie = create('Human', ['nom'=>'Nat', 'age' => 29]);
  echo $nathalie->parler() . "\n";
  assert($nathalie->parler() === "My name is Nat and I'm 29 years old."); // has the new parler() implementation
  echo $julien->parler() . "\n";
  assert($julien->parler() === "My name is Julien and I'm 39 years old."); // still has the old parler() method implementation
}
oui();

$coughtException = false;
try {
  $julien->jaser();
} catch (PrototypeMethodMissingException $e) {
  // SHOULD go there since the parent prototype's methodMissing throws an Exception
  $coughtException = true;
}
assert($coughtException);

setMethod('Human', 'methodMissing', function ($self, $methodName) {
  echo "Purée! C'est platte mais $methodName est pas implémenté.\n";
});

$hadNoException = true;
try {
  $julien->jaser();
} catch (PrototypeMethodMissingException $e) {
  // Should not go there, since it will execute its own methodMissing method which doesn't throw an Exception.
  $hadNoException = false;
}
assert($hadNoException);



echo "Everything is Good.\n";

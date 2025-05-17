<?php

class Prototype {
  protected $properties = [];
  protected $methods = [];

  public function __construct($properties, $methods) {
    $this->properties = $properties;
    $this->methods = $methods;
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
  }

  public function __get($propertyName) {
    return $this->properties[$propertyName] ?? null;
  }

}

$__prototypesRegistry = [];

function prototype(string $prototypeName, array $properties, array $methods) {
  global $__prototypesRegistry;
  $__prototypesRegistry[$prototypeName] = new Prototype($properties, $methods);
  return $__prototypesRegistry[$prototypeName];
}

function setMethod(string $prototypeName, string $methodName, callable $methodBody) {
  global $__prototypesRegistry;
  $prototype = $__prototypesRegistry[$prototypeName];
  $prototype->setMethods([$methodName => $methodBody]);
}

prototype('Human',
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

//assert(get_class($Human) === 'Prototype');


function create(string $prototypeName, array $initialParams = []) {
  global $__prototypesRegistry;
  $object = clone $__prototypesRegistry[$prototypeName];
  $object->setInitialParams($initialParams);
  return $object;
}

$julien = create('Human', ['nom'=>'Julien', 'age' => 39]);
assert($julien->parler() === "Mon nom est Julien et j'ai 39 ans.");

setMethod('Human', 'parler', function ($self) {
  return "My name is {$self->nom} and I'm {$self->age} years old.";
});

function oui() {
  global $julien;
  $nathalie = create('Human', ['nom'=>'Nat', 'age' => 29]);
  assert($nathalie->parler() === "My name is Nat and I'm 29 years old."); // has the new parler() implementation
  assert($julien->parler() === "Mon nom est Julien et j'ai 39 ans."); // still has the old parler() method implementation
}
oui();




echo "Everything is Good.\n";

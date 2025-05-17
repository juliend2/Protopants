<?php

class Prototype {
  protected $properties = [];
  protected $methods = [];

  public function __construct($properties, $methods) {
    $this->properties = $properties;
    $this->methods = $methods;
  }

  public function setInitialParams($properties) {
    $this->properties = array_merge($this->properties, $properties);
  }

  public function setMethod($methodName, callable $methodBody) {
    $this->methods[$methodName] = $methodBody;
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

$Human = new Prototype(
  properties: [
    'nom' => '',
    'age' => 0,
  ],
  // default methods:
  methods: [
    'parler' => function ($self) {
      $str = "Mon nom est {$self->nom} et j'ai {$self->age} ans.";
      return $str;
    }
  ]
);

assert(get_class($Human) === 'Prototype');


function create(Prototype $prototype, array $initialParams = []) {
  $object = clone $prototype;
  $object->setInitialParams($initialParams);
  return $object;
}

$julien = create($Human, ['nom'=>'Julien', 'age' => 39]);
assert($julien->parler() === "Mon nom est Julien et j'ai 39 ans.");

$Human->setMethod('parler', function ($self) {
  return "My name is {$self->nom} and I'm {$self->age} years old.";
});

$nathalie = create($Human, ['nom'=>'Nat', 'age' => 29]);
assert($nathalie->parler() === "My name is Nat and I'm 29 years old."); // has the new parler() implementation
assert($julien->parler() === "Mon nom est Julien et j'ai 39 ans."); // still has the old parler() method implementation



echo "Everything is Good.\n";
<?php

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

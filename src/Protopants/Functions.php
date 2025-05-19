<?php

namespace Protopants;

use Protopants\Prototype;
use Protopants\SubPrototype;

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


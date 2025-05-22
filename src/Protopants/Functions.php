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

function __slidingPairs(array $arr): \Generator {
    for ($i = 0; $i < count($arr) - 1; $i++) {
        yield [$arr[$i], $arr[$i + 1], array_key_last($arr) == $i + 1];
    }
}


<?php

namespace Protopants;

use Protopants\Prototype;
use Protopants\SubPrototype;

/**
 * Given a prototype, it will allow you to add or override any method.
 */
function setMethod(string $prototypeName, string $methodName, callable $methodBody) {
  $prototype = Prototype::get($prototypeName);
  $prototype->setSingleMethod([$methodName => $methodBody]);
}

/**
 * Creates an object of a given prototype, with optional params to initialize
 * the object (overrides the default object's params).
 */
function create(string $prototypeName, array $initialParams = []) {
  $parentPrototype = Prototype::get($prototypeName);
  $cloned = new SubPrototype($initialParams ?? [], []);
  $cloned->setParentPrototype($parentPrototype);
  $cloned->setInitialParams($initialParams);
  return $cloned;
}

/**
 * Given an array like this example:
 *  [1, 2, 3, 4]
 * It will loop such that the iterations will look like the following:
 *  [3, 4, false]
 *  [2, 3, false]
 *  [1, 2, true] <-- this is the last, so the 3rd value is true.
 */
function __slidingPairs(array $arr): \Generator {
    for ($i = 0; $i < count($arr) - 1; $i++) {
        yield [$arr[$i], $arr[$i + 1], array_key_last($arr) == $i + 1];
    }
}


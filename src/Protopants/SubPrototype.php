<?php

namespace Protopants;

class SubPrototype extends Prototype {
    public function __construct($properties, $methods) {
        $this->properties = array_merge($this->properties, $properties);
        // the difference with parent::__construct() is that we don't want to override the base methodMissing
        $this->methods = array_merge($this->methods, $methods);
    }
}

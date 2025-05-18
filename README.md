# Protopants

Prototype-based programming for the PHP fancy pants.

## TODO

- [x] Followable prototype chain for inheritance-like behavior
- [x] `methodMissing` method that can be overridden, to allow for unknown
  method calls to not throw exceptions.
- [x] Fix access to properties of $self from within method. Access the right value based on what prototype has it.
- [ ] Maybe add a way to call methods on non-object values, such as integers.
  Through a function.

# Protopants

Prototype-based programming for the PHP fancy pants.

## TODO

- [ ] Followable prototype chain for inheritance-like behavior
- [ ] `methodMissing` method that can be overridden, to allow for unknown
  method calls to not throw exceptions.
- [ ] Maybe add a way to call methods on non-object values, such as integers.
  Through a function.
- [ ] See if I can have the Prototype object be some kind of defined constant,
  so we can use it in any scope without the need to `global`-ize it all the
  time.


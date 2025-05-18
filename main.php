<?php

require_once __DIR__. '/src/Prototype.php';
require_once __DIR__. '/src/SubPrototype.php';
require_once __DIR__. '/src/Functions.php';

require_once __DIR__. '/src/autorun.php';

// assert(get_class($humainProto) === 'Prototype');

// $julien = create('Human', ['nom'=>'Julien', 'age' => 39]);
// // echo $julien->parler() . "\n";
// // assert($julien->parler() === "Mon nom est Julien et j'ai 39 ans.");
// // assert($julien->parler() === "Bonjour.");

// echo $julien->toString(). "\n";
// assert($julien->toString() === 'TODO');

// setMethod('Human', 'parler', function ($self) {
//   return "My name is {$self->nom} and I'm {$self->age} years old.";
// });

// function oui() {
//   global $julien;
//   $nathalie = create('Human', ['nom'=>'Nat', 'age' => 29]);
//   echo $nathalie->parler() . "\n";
//   assert($nathalie->parler() === "My name is Nat and I'm 29 years old."); // has the new parler() implementation
//   echo $julien->parler() . "\n";
//   assert($julien->parler() === "My name is Julien and I'm 39 years old."); // still has the old parler() method implementation
// }
// oui();

// $coughtException = false;
// try {
//   $julien->jaser();
// } catch (PrototypeMethodMissingException $e) {
//   // SHOULD go there since the parent prototype's methodMissing throws an Exception
//   $coughtException = true;
// }
// assert($coughtException);

// setMethod('Human', 'methodMissing', function ($self, $methodName) {
//   echo "Purée! C'est platte mais $methodName est pas implémenté.\n";
// });

// $hadNoException = true;
// try {
//   $julien->jaser();
// } catch (PrototypeMethodMissingException $e) {
//   // Should not go there, since it will execute its own methodMissing method which doesn't throw an Exception.
//   $hadNoException = false;
// }
// assert($hadNoException);



// echo "Everything is Good.\n";

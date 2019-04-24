<?php

use Quanta\Container\Configuration\Tagging\Implementations;

require_once __DIR__ . '/../../.test/classes.php';

describe('Implementations', function () {

    beforeEach(function () {

        $this->predicate = new Implementations(Test\TestInterface::class);

    });

    describe('->__invoke()', function () {

        context('when the given id is not the name of a subclass of the class', function () {

            it('should return false', function () {

                $test = ($this->predicate)(Test\SomeClass::class);

                expect($test)->toBeFalsy();

            });

        });

        context('when the given id is the name of a subclass of the class', function () {

            it('should return true', function () {

                $test = ($this->predicate)(Test\TestClass::class);

                expect($test)->toBeTruthy();

            });

        });

    });

});

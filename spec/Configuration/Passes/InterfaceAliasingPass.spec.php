<?php

use Quanta\Container\Configuration\Passes\InterfaceAliasingPass;
use Quanta\Container\Configuration\Passes\ProcessingPassInterface;

require_once __DIR__ . '/../../.test/classes.php';

describe('InterfaceAliasingPass', function () {

    beforeEach(function () {

        $this->pass = new InterfaceAliasingPass;

    });

    it('should implement ProcessingPassInterface', function () {

        expect($this->pass)->toBeAnInstanceOf(ProcessingPassInterface::class);

    });

    context('->aliases()', function () {

        context('when the given id is not a class name', function () {

            it('should return an empty array', function () {

                $test = $this->pass->aliases('id');

                expect($test)->toEqual([]);

            });

        });

        context('when the given id is an interface name', function () {

            it('should return an empty array', function () {

                $test = $this->pass->aliases(Test\TestInterface1::class);

                expect($test)->toEqual([]);

            });

        });

        context('when the given id is a class name', function () {

            context('when the class with the given name does not implement any interface', function () {

                it('should return an empty array', function () {

                    $test = $this->pass->aliases(StdClass::class);

                    expect($test)->toEqual([]);

                });

            });

            context('when the class with the given name implements at least one interface', function () {

                it('should return the names of the interfaces implemented by the class with he given name', function () {

                    $test = $this->pass->aliases(Test\TestClass::class);

                    expect($test)->toEqual([
                        Test\TestInterface1::class,
                        Test\TestInterface2::class,
                        Test\TestInterface3::class,
                    ]);

                });

            });

        });

    });

    context('->tags()', function () {

        it('should return an empty array', function () {

            $test = $this->pass->tags('id1', 'id2', 'id3');

            expect($test)->toEqual([]);

        });

    });

    context('->processed()', function () {

        it('should return the given factory', function () {

            $factory = function () {};

            $test = $this->pass->processed('id', $factory);

            expect($test)->toBe($factory);

        });

    });

});

<?php

use function Eloquent\Phony\Kahlan\stub;
use function Eloquent\Phony\Kahlan\mock;

use Psr\Container\ContainerInterface;

use Quanta\Container\Parameter;
use Quanta\Container\FactoryInterface;

require_once __DIR__ . '/.test/classes.php';

describe('Factory', function () {

    context('when the parameter is a boolean', function () {

        context('when the boolean value is true', function () {

            beforeEach(function () {

                $this->factory = new Parameter(true);

            });

            it('should implement FactoryInterface', function () {

                expect($this->factory)->toBeAnInstanceOf(FactoryInterface::class);

            });

            describe('->__invoke()', function () {

                it('should return the value', function () {

                    $container = mock(ContainerInterface::class);

                    $test = ($this->factory)($container->get());

                    expect($test)->toBeTruthy();

                });

            });

            describe('->compiled()', function () {

                it('should return true', function () {

                    $test = $this->factory->compiled('container', function () {});

                    expect($test)->toEqual('true');

                });

            });

        });

        context('when the boolean value is false', function () {

            beforeEach(function () {

                $this->factory = new Parameter(false);

            });

            it('should implement FactoryInterface', function () {

                expect($this->factory)->toBeAnInstanceOf(FactoryInterface::class);

            });

            describe('->__invoke()', function () {

                it('should return the value', function () {

                    $container = mock(ContainerInterface::class);

                    $test = ($this->factory)($container->get());

                    expect($test)->toBeFalsy();

                });

            });

            describe('->compiled()', function () {

                it('should return true', function () {

                    $test = $this->factory->compiled('container', function () {});

                    expect($test)->toEqual('false');

                });

            });

        });

    });

    context('when the parameter is an int', function () {

        beforeEach(function () {

            $this->factory = new Parameter(1);

        });

        it('should implement FactoryInterface', function () {

            expect($this->factory)->toBeAnInstanceOf(FactoryInterface::class);

        });

        describe('->__invoke()', function () {

            it('should return the value', function () {

                $container = mock(ContainerInterface::class);

                $test = ($this->factory)($container->get());

                expect($test)->toEqual(1);

            });

        });

        describe('->compiled()', function () {

            it('should return the int as a string', function () {

                $test = $this->factory->compiled('container', function () {});

                expect($test)->toEqual('1');

            });

        });

    });

    context('when the parameter is a float', function () {

        beforeEach(function () {

            $this->factory = new Parameter(1.1);

        });

        it('should implement FactoryInterface', function () {

            expect($this->factory)->toBeAnInstanceOf(FactoryInterface::class);

        });

        describe('->__invoke()', function () {

            it('should return the value', function () {

                $container = mock(ContainerInterface::class);

                $test = ($this->factory)($container->get());

                expect($test)->toEqual(1.1);

            });

        });

        describe('->compiled()', function () {

            it('should return the float as a string', function () {

                $test = $this->factory->compiled('container', function () {});

                expect($test)->toEqual('1.1');

            });

        });

    });

    context('when the parameter is a string', function () {

        beforeEach(function () {

            $this->factory = new Parameter('value');

        });

        it('should implement FactoryInterface', function () {

            expect($this->factory)->toBeAnInstanceOf(FactoryInterface::class);

        });

        describe('->__invoke()', function () {

            it('should return the value', function () {

                $container = mock(ContainerInterface::class);

                $test = ($this->factory)($container->get());

                expect($test)->toEqual('value');

            });

        });

        describe('->compiled()', function () {

            it('should return the quoted string', function () {

                $test = $this->factory->compiled('container', function () {});

                expect($test)->toEqual('\'value\'');

            });

        });

    });

    context('when the parameter is an array', function () {

        context('when the array is not a callable', function () {

            it('should throw an InvalidArgumentException', function () {

                $test = function () { new Parameter([]); };

                expect($test)->toThrow(new InvalidArgumentException);

            });

        });

        context('when the array is a callable', function () {

            it('should not fail', function () {

                $test = function () {
                    new Parameter([Test\TestFactory::class, 'createStatic']);
                };

                expect($test)->not->toThrow();

            });

        });

    });

    context('when the parameter is an object', function () {

        beforeEach(function () {

            $this->object = new class {};

            $this->factory = new Parameter($this->object);

        });

        it('should implement FactoryInterface', function () {

            expect($this->factory)->toBeAnInstanceOf(FactoryInterface::class);

        });

        describe('->__invoke()', function () {

            it('should return the value', function () {

                $container = mock(ContainerInterface::class);

                $test = ($this->factory)($container->get());

                expect($test)->toBe($this->object);

            });

        });

        describe('->compiled()', function () {

            it('should throw a LogicException', function () {

                $test = function () {
                    $this->factory->compiled('container', function () {});
                };

                expect($test)->toThrow(new LogicException);

            });

        });

    });

    context('when the parameter is a resource', function () {

        beforeEach(function () {

            $this->resource = tmpfile();

            $this->factory = new Parameter($this->resource);

        });

        it('should implement FactoryInterface', function () {

            expect($this->factory)->toBeAnInstanceOf(FactoryInterface::class);

        });

        describe('->__invoke()', function () {

            it('should return the value', function () {

                $container = mock(ContainerInterface::class);

                $test = ($this->factory)($container->get());

                expect($test)->toBe($this->resource);

            });

        });

        describe('->compiled()', function () {

            it('should throw a LogicException', function () {

                $test = function () {
                    $this->factory->compiled('container', function () {});
                };

                expect($test)->toThrow(new LogicException);

            });

        });

    });

    context('when the parameter is null', function () {

        beforeEach(function () {

            $this->factory = new Parameter(null);

        });

        it('should implement FactoryInterface', function () {

            expect($this->factory)->toBeAnInstanceOf(FactoryInterface::class);

        });

        describe('->__invoke()', function () {

            it('should return the value', function () {

                $container = mock(ContainerInterface::class);

                $test = ($this->factory)($container->get());

                expect($test)->toBeNull();

            });

        });

        describe('->compiled()', function () {

            it('should throw return null', function () {

                $test = $this->factory->compiled('container', function () {});

                expect($test)->toEqual('null');

            });

        });

    });

    context('when the value is a callable', function () {

        beforeEach(function () {

            $this->callable = function () {};

            $this->factory = new Parameter($this->callable);

        });

        it('should implement FactoryInterface', function () {

            expect($this->factory)->toBeAnInstanceOf(FactoryInterface::class);

        });

        describe('->__invoke()', function () {

            it('should return the value', function () {

                $container = mock(ContainerInterface::class);

                $test = ($this->factory)($container->get());

                expect($test)->toBe($this->callable);

            });

        });

        describe('->compiled()', function () {

            it('should proxy the given compiler', function () {

                $compiler = stub()->with($this->callable)->returns('value');

                $test = $this->factory->compiled('container', $compiler);

                expect($test)->toEqual('value');

            });

        });

    });

});

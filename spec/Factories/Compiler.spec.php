<?php

use function Eloquent\Phony\Kahlan\mock;

use Test\TestFactory;

use Quanta\Container\Factories\Compiler;
use Quanta\Container\Factories\CompiledFactory;
use Quanta\Container\Factories\ClosureCompilerInterface;
use Quanta\Container\Factories\CompilableFactoryInterface;

require_once __DIR__ . '/../.test/classes.php';

describe('Compiler', function () {

    beforeEach(function () {

        $this->delegate = mock(ClosureCompilerInterface::class);

        $this->compiler = new Compiler($this->delegate->get());

    });

    describe('->__invoke()', function () {

        context('when the given callable is an object', function () {

            context('when the given callable is an implementation of CompilableFactoryInterface', function () {

                it('should return the string representation of the CompiledFactory returned by the factory ->compiled() method', function () {

                    $callable = mock(CompilableFactoryInterface::class);

                    $compiled = new CompiledFactory('container', '$previous', '// body');

                    $callable->compiled->with($this->compiler)->returns($compiled);

                    $test = ($this->compiler)($callable->get());

                    expect($test)->toEqual((string) $compiled);

                });

            });

            context('when the given callable is a closure', function () {

                it('should return a string representation of the closure using the analyzer', function () {

                    $closure = function () {};

                    $this->delegate->__invoke
                        ->with(Kahlan\Arg::toBe($closure))
                        ->returns('function () { // some closure }');

                    $test = ($this->compiler)($closure);

                    expect($test)->toEqual('function () { // some closure }');

                });

            });

            context('when the given callable is an invokable object', function () {

                it('should throw a LogicException', function () {

                    $test = function () {
                        ($this->compiler)(new TestFactory('factory'));
                    };

                    expect($test)->toThrow(new LogicException);

                });

            });

        });

        context('when the given callable is an array', function () {

            context('when the given callable is a static method', function () {

                it('should return a string representation of the callable', function () {

                    $test = ($this->compiler)([TestFactory::class, 'createStatic']);

                    expect($test)->toEqual('[\Test\TestFactory::class, \'createStatic\']');

                });

            });

            context('when the given callable is an instance method', function () {

                it('should throw a LogicException', function () {

                    $test = function () {
                        ($this->compiler)([new TestFactory('factory'), 'create']);
                    };

                    expect($test)->toThrow(new LogicException);

                });

            });

        });

        context('when the given callable is a function name', function () {

            it('should return the function name', function () {

                function delegate () {};

                $test = ($this->compiler)('delegate');

                expect($test)->toEqual('delegate');

            });

        });

    });

});

<?php

use function Eloquent\Phony\Kahlan\mock;

use Test\TestFactory;

use Quanta\Container\Factories\CompilableFactoryInterface;

use Quanta\Container\Compilation\Template;
use Quanta\Container\Compilation\CallableCompiler;
use Quanta\Container\Compilation\ClosureCompilerInterface;

require_once __DIR__ . '/../.test/classes.php';

describe('CallableCompiler::withDummyClosureCompiler()', function () {

    it('should return a callable compiler with a dummy closure compiler', function () {

        $test = CallableCompiler::withDummyClosureCompiler();

        expect($test)->toEqual(new CallableCompiler(
            new Quanta\Container\Compilation\DummyClosureCompiler
        ));

    });

});

describe('CallableCompiler', function () {

    beforeEach(function () {

        $this->delegate = mock(ClosureCompilerInterface::class);

        $this->compiler = new CallableCompiler($this->delegate->get());

    });

    describe('->compiled()', function () {

        context('when the given callable is an object', function () {

            context('when the given callable is an implementation of CompilableFactoryInterface', function () {

                it('should proxy the callable ->compiled() method with a template using this callable compiler', function () {

                    $callable = mock(CompilableFactoryInterface::class);

                    $callable->compiled
                        ->with(new Template($this->compiler))
                        ->returns('value');

                    $test = $this->compiler->compiled($callable->get());

                    expect($test)->toEqual('value');

                });

            });

            context('when the given callable is a closure', function () {

                it('should return a string representation of the closure using the analyzer', function () {

                    $closure = function () {};

                    $this->delegate->compiled->with($closure)->returns('function () {}');

                    $test = $this->compiler->compiled($closure);

                    expect($test)->toEqual('function () {}');

                });

            });

            context('when the given callable is an invokable object', function () {

                it('should throw a LogicException', function () {

                    $test = function () {
                        $this->compiler->compiled(new TestFactory('factory'));
                    };

                    expect($test)->toThrow(new LogicException);

                });

            });

        });

        context('when the given callable is an array', function () {

            context('when the given callable is a static method', function () {

                it('should return a string representation of the callable', function () {

                    $test = $this->compiler->compiled([TestFactory::class, 'createStatic']);

                    expect($test)->toEqual('[\Test\TestFactory::class, \'createStatic\']');

                });

            });

            context('when the given callable is an instance method', function () {

                it('should throw a LogicException', function () {

                    $test = function () {
                        $this->compiler->compiled([new TestFactory('factory'), 'create']);
                    };

                    expect($test)->toThrow(new LogicException);

                });

            });

        });

        context('when the given callable is a function name', function () {

            it('should return the function name', function () {

                function delegate () {};

                $test = $this->compiler->compiled('delegate');

                expect($test)->toEqual('delegate');

            });

        });

    });

});

<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\FactoryInterface;
use Quanta\Container\Compilation\Compiler;
use Quanta\Container\Compilation\ClosureCompilerInterface;

require_once __DIR__ . '/../.test/classes.php';

describe('Compiler', function () {

    beforeEach(function () {

        $this->delegate = mock(ClosureCompilerInterface::class);

        $this->compiler = new Compiler($this->delegate->get());

    });

    describe('->__invoke()', function () {

        context('when the given callable is an array', function () {

            context('when the given array represents a static method', function () {

                it('should return a string representation of the static method', function () {

                    $test = ($this->compiler)([Test\TestFactory::class, 'createStatic']);

                    expect($test)->toEqual('[Test\TestFactory::class, \'createStatic\']');

                });

            });

            context('when the given array represents an instance method', function () {

                it('should throw an InvalidArgumentException', function () {

                    $test = function () {
                        ($this->compiler)([new Test\TestFactory, 'create']);
                    };

                    expect($test)->toThrow(new InvalidArgumentException);

                });

            });

        });

        context('when the given callable is an object', function () {

            context('when the given object implements FactoryInterface', function () {

                it('should return the string representation of the factory', function () {

                    $factory = mock(FactoryInterface::class);

                    $factory->compiled
                        ->with('container', $this->compiler)
                        ->returns(implode(PHP_EOL, ['value1', 'value2', 'value3']));

                    $test = ($this->compiler)($factory->get());

                    expect($test)->toEqual(implode(PHP_EOL, [
                        'function (Psr\Container\ContainerInterface $container) {',
                        '    return value1',
                        '    value2',
                        '    value3;',
                        '}',
                    ]));

                });

            });

            context('when the given object is a closure', function () {

                it('should proxy the delegate', function () {

                    $closure = function () {};

                    $this->delegate->__invoke
                        ->with(Kahlan\Arg::toBe($closure))
                        ->returns('function () { // some closure }');

                    $test = ($this->compiler)($closure);

                    expect($test)->toEqual('function () { // some closure }');

                });

            });

            context('when the object is any other object', function () {

                it('should throw an InvalidArgumentException', function () {

                    $test = function () {
                        ($this->compiler)(new class {
                            public function __invoke() {}
                        });
                    };

                    expect($test)->toThrow(new InvalidArgumentException);

                });

            });

        });

        context('when the given callable is a string', function () {

            it('should return the quoted string', function () {

                $test = ($this->compiler)('Test\test_function');

                expect($test)->toEqual('\'Test\\test_function\'');

            });

        });

    });

});

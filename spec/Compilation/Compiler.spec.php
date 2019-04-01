<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\Compilation\Compiler;
use Quanta\Container\Compilation\CompilableInterface;
use Quanta\Container\Compilation\DummyClosureCompiler;
use Quanta\Container\Compilation\ClosureCompilerInterface;

require_once __DIR__ . '/../.test/classes.php';

describe('Compiler::testing()', function () {

    context('when no compiled array is given', function () {

        it('should return a new Compiler with a dummy closure compiler and an empty compiled array', function () {

            $test = Compiler::testing();

            expect($test)->toEqual(new Compiler(new DummyClosureCompiler, []));

        });

    });

    context('when a compiled array is given', function () {

        it('should return a new Compiler with a dummy closure compiler and the given compiled array', function () {

            $test = Compiler::testing([
                'compiled1' => $this->value1 = new class {},
                'compiled2' => $this->value2 = new class {},
                'compiled3' => $this->value3 = new class {},
            ]);

            expect($test)->toEqual(new Compiler(new DummyClosureCompiler, [
                'compiled1' => $this->value1,
                'compiled2' => $this->value2,
                'compiled3' => $this->value3,
            ]));

        });

    });

});

describe('Compiler', function () {

    beforeEach(function () {

        $this->delegate = mock(ClosureCompilerInterface::class);

    });

    context('when there is no compiled array', function () {

        it('should use an empty compiled array', function () {

            $test = new Compiler($this->delegate->get());

            expect($test)->toEqual(new Compiler($this->delegate->get(), []));

        });

    });

    context('when there is a compiled array', function () {

        beforeEach(function () {

            $this->compiler = new Compiler($this->delegate->get(), [
                'compiled1' => $this->value1 = new class {},
                'compiled2' => $this->value2 = new class {},
                'compiled3' => $this->value3 = new class {},
            ]);

        });

        describe('->__invoke()', function () {

            context('when the given value is in the compiled array', function () {

                it('should return the compiled string associated to the given value', function () {

                    $test = ($this->compiler)($this->value2);

                    expect($test)->toEqual('compiled2');

                });

            });

            context('when the given value is not in the compiled array', function () {

                context('when the given value is a boolean', function () {

                    context('when the given boolean is true', function () {

                        it('should return true', function () {

                            $test = ($this->compiler)(true);

                            expect($test)->toEqual('true');

                        });

                    });

                    context('when the given boolean is false', function () {

                        it('should return false', function () {

                            $test = ($this->compiler)(false);

                            expect($test)->toEqual('false');

                        });

                    });

                });

                context('when the given value is an integer', function () {

                    it('should return the given integer as a string', function () {

                        $test = ($this->compiler)(1);

                        expect($test)->toEqual('1');

                    });

                });

                context('when the given value is a float', function () {

                    it('should return the given float as a string', function () {

                        $test = ($this->compiler)(1.1);

                        expect($test)->toEqual('1.1');

                    });

                });

                context('when the given value is a string', function () {

                    context('when the string is a function name', function () {

                        it('should return the given function name as a string', function () {

                            $test = ($this->compiler)('\Test\test_function');

                            expect($test)->toEqual('\Test\test_function');

                        });

                    });

                    context('when the string is not a function name', function () {

                        it('should return the given string with quotes', function () {

                            $test = ($this->compiler)('test');

                            expect($test)->toEqual('\'test\'');

                        });

                    });

                });

                context('when the given value is an array', function () {

                    context('when the given array is a callable', function () {

                        context('when the callable array is a static method', function () {

                            it('should return a string representation of the callable', function () {

                                $test = ($this->compiler)([Test\TestFactory::class, 'createStatic']);

                                expect($test)->toEqual('[Test\TestFactory::class, \'createStatic\']');

                            });

                        });

                        context('when the callable array is an instance method', function () {

                            it('should throw an InvalidArgumentException', function () {

                                $test = function () {
                                    ($this->compiler)([new Test\TestFactory, 'create']);
                                };

                                expect($test)->toThrow(new InvalidArgumentException);

                            });

                        });

                    });

                    context('when the array is a regular array', function () {

                        context('when the array is a list', function () {

                            it('should return a string representation of the array without keys', function () {

                                $test = ($this->compiler)([$this->value1, $this->value2, $this->value3]);

                                expect($test)->toEqual(implode(PHP_EOL, [
                                    '[',
                                    '    compiled1,',
                                    '    compiled2,',
                                    '    compiled3,',
                                    ']',
                                ]));

                            });

                        });

                        context('when the array has numeric keys', function () {

                            it('should return a string representation of the array with keys', function () {

                                $test = ($this->compiler)([$this->value1, 2 => $this->value2, $this->value3]);

                                expect($test)->toEqual(implode(PHP_EOL, [
                                    '[',
                                    '    0 => compiled1,',
                                    '    2 => compiled2,',
                                    '    3 => compiled3,',
                                    ']',
                                ]));

                            });

                        });

                        context('when the array has string keys', function () {

                            it('should return a string representation of the array with keys', function () {

                                $test = ($this->compiler)([$this->value1, 'k2' => $this->value2, $this->value3]);

                                expect($test)->toEqual(implode(PHP_EOL, [
                                    '[',
                                    '    0 => compiled1,',
                                    '    \'k2\' => compiled2,',
                                    '    1 => compiled3,',
                                    ']',
                                ]));

                            });

                        });

                    });

                });

                context('when the given value is an object', function () {

                    context('when the given object implements CompilableInterface', function () {

                        it('should return the string representation of the object', function () {

                            $compilable = mock(CompilableInterface::class);

                            $compilable->compiled->with($this->compiler)->returns('value');

                            $test = ($this->compiler)($compilable->get());

                            expect($test)->toEqual('value');

                        });

                    });

                    context('when the given object is a closure', function () {

                        it('should return a string representation of the closure using the delegate', function () {

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

                            $test = function () { ($this->compiler)(new class {}); };

                            expect($test)->toThrow(new InvalidArgumentException);

                        });

                    });

                });

                context('when the given value is a resource', function () {

                    it('should throw an InvalidArgumentException', function () {

                        $test = function () { ($this->compiler)(tmpfile()); };

                        expect($test)->toThrow(new InvalidArgumentException);

                    });

                });

                context('when the given value is null', function () {

                    it('should return null as a string', function () {

                        $test = ($this->compiler)(null);

                        expect($test)->toEqual('null');

                    });

                });

            });

        });

    });

});

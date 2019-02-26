<?php

use function Eloquent\Phony\Kahlan\mock;

use Psr\Container\ContainerInterface;

use Quanta\Container\Values\Value;
use Quanta\Container\Values\ValueInterface;

require_once __DIR__ . '/../.test/classes.php';

describe('Value', function () {

    beforeEach(function () {

        $this->container = mock(ContainerInterface::class);

    });

    context('when the value is a boolean', function () {

        context('when the value is true', function () {

            beforeEach(function () {

                $this->value = new Value(true);

            });

            it('should implement ValueInterface', function () {

                expect($this->value)->toBeAnInstanceOf(ValueInterface::class);

            });

            describe('->value()', function () {

                it('should return true', function () {

                    $test = $this->value->value($this->container->get());

                    expect($test)->toBeTruthy();

                });

            });

            it('should return true string', function () {

                $test = $this->value->str('container');

                expect($test)->toEqual('true');

            });

        });

        context('when the value is false', function () {

            beforeEach(function () {

                $this->value = new Value(false);

            });

            it('should implement ValueInterface', function () {

                expect($this->value)->toBeAnInstanceOf(ValueInterface::class);

            });

            describe('->value()', function () {

                it('should return false', function () {

                    $test = $this->value->value($this->container->get());

                    expect($test)->toBeFalsy();

                });

            });

            it('should return false string', function () {

                $test = $this->value->str('container');

                expect($test)->toEqual('false');

            });

        });

    });

    context('when the value is an integer', function () {

        beforeEach(function () {

            $this->value = new Value(1);

        });

        it('should implement ValueInterface', function () {

            expect($this->value)->toBeAnInstanceOf(ValueInterface::class);

        });

        describe('->value()', function () {

            it('should return the integer', function () {

                $test = $this->value->value($this->container->get());

                expect($test)->toEqual(1);

            });

        });

        describe('->str()', function () {

            it('should return the integer string', function () {

                $test = $this->value->str('container');

                expect($test)->toEqual('1');

            });

        });

    });

    context('when the value is a float', function () {

        beforeEach(function () {

            $this->value = new Value(1.1);

        });

        it('should implement ValueInterface', function () {

            expect($this->value)->toBeAnInstanceOf(ValueInterface::class);

        });

        describe('->value()', function () {

            it('should return the float', function () {

                $test = $this->value->value($this->container->get());

                expect($test)->toEqual(1.1);

            });

        });

        describe('->str()', function () {

            it('should return the float string', function () {

                $test = $this->value->str('container');

                expect($test)->toEqual('1.1');

            });

        });

    });

    context('when the value is a string', function () {

        beforeEach(function () {

            $this->value = new Value('value');

        });

        it('should implement ValueInterface', function () {

            expect($this->value)->toBeAnInstanceOf(ValueInterface::class);

        });

        describe('->value()', function () {

            it('should return the string', function () {

                $test = $this->value->value($this->container->get());

                expect($test)->toEqual('value');

            });

        });

        describe('->str()', function () {

            it('should return the string with quotes', function () {

                $test = $this->value->str('container');

                expect($test)->toEqual('\'value\'');

            });

        });

    });

    context('when the value is an array', function () {

        context('when the array is not a callable', function () {

            it('should throw an InvalidArgumentException', function () {

                $test = function () {
                    new Value([]);
                };

                expect($test)->toThrow(new InvalidArgumentException);

            });

        });

        context('when the array is a callable', function () {

            context('when the callable is an instance method', function () {

                it('should throw an InvalidArgumentException', function () {

                    $test = function () {
                        new Value([new Test\TestFactory('factory'), 'create']);
                    };

                    expect($test)->toThrow(new InvalidArgumentException);

                });

            });

            context('when the callable is a static method', function () {

                beforeEach(function () {

                    $this->value = new Value([Test\TestFactory::class, 'createStatic']);

                });

                it('should implement ValueInterface', function () {

                    expect($this->value)->toBeAnInstanceOf(ValueInterface::class);

                });

                describe('->value()', function () {

                    it('should return the callable', function () {

                        $test = $this->value->value($this->container->get());

                        expect($test)->toEqual([Test\TestFactory::class, 'createStatic']);

                    });

                });

                describe('->str()', function () {

                    it('should return a string representation of the callable', function () {

                        $test = $this->value->str('container');

                        expect($test)->toEqual('[\Test\TestFactory::class, \'createStatic\']');

                    });

                });

            });

        });

    });

    context('when the value is an object', function () {

        it('should throw an InvalidArgumentException', function () {

            $test = function () {
                new Value(new class {});
            };

            expect($test)->toThrow(new InvalidArgumentException);

        });

    });

    context('when the value is a resource', function () {

        it('should throw an InvalidArgumentException', function () {

            $test = function () {
                new Value(tmpfile());
            };

            expect($test)->toThrow(new InvalidArgumentException);

        });

    });

    context('when the value is null', function () {

        beforeEach(function () {

            $this->value = new Value(null);

        });

        it('should implement ValueInterface', function () {

            expect($this->value)->toBeAnInstanceOf(ValueInterface::class);

        });

        describe('->value()', function () {

            it('should return null', function () {

                $test = $this->value->value($this->container->get());

                expect($test)->toBeNull();

            });

        });

        describe('->str()', function () {

            it('should return null', function () {

                $test = $this->value->str('container');

                expect($test)->toEqual('null');

            });

        });

    });

});

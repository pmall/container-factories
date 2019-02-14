<?php

use function Eloquent\Phony\Kahlan\mock;

use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use Quanta\Container\Values\Reference;
use Quanta\Container\Values\ValueInterface;

describe('Reference', function () {

    context('when the reference is not nullable', function () {

        beforeEach(function () {

            $this->value = new Reference('id', false);

        });

        it('should implement ValueInterface', function () {

            expect($this->value)->toBeAnInstanceOf(ValueInterface::class);

        });

        describe('->value()', function () {

            beforeEach(function () {

                $this->container = mock(ContainerInterface::class);

            });

            context('when the container entry is defined', function () {

                beforeEach(function () {

                    $this->container->has->with('id')->returns(true);

                });

                context('when the container does not throw a NotFoundExceptionInterface', function () {

                    it('should return the container entry', function () {

                        $this->container->get->with('id')->returns('value');

                        $test = $this->value->value($this->container->get());

                        expect($test)->toEqual('value');

                    });

                });

                context('when the container throws a NotFoundExceptionInterface', function () {

                    it('should propagate the exception', function () {

                        $exception = mock([Throwable::class, NotFoundExceptionInterface::class]);

                        $this->container->get->with('id')->throws($exception);

                        $test = function () {
                            $this->value->value($this->container->get());
                        };

                        expect($test)->toThrow($exception->get());

                    });

                });

            });

            context('when the container entry is not defined', function () {

                it('should call the container ->get() method anyway', function () {

                    $this->container->has->with('id')->returns(false);

                    $this->value->value($this->container->get());

                    $this->container->get->once()->calledWith('id');

                });

            });

        });

        describe('->str()', function () {

            it('should return the string representation of the container entry', function () {

                $test = $this->value->str('container');

                expect($test)->toEqual('$container->get(\'id\')');

            });

        });

    });

    context('when the reference is nullable', function () {

        beforeEach(function () {

            $this->value = new Reference('id', true);

        });

        it('should implement ValueInterface', function () {

            expect($this->value)->toBeAnInstanceOf(ValueInterface::class);

        });

        describe('->value()', function () {

            beforeEach(function () {

                $this->container = mock(ContainerInterface::class);

            });

            context('when the container entry is defined', function () {

                beforeEach(function () {

                    $this->container->has->with('id')->returns(true);

                });

                context('when the container does not throw a NotFoundExceptionInterface', function () {

                    it('should return the container entry', function () {

                        $this->container->get->with('id')->returns('value');

                        $test = $this->value->value($this->container->get());

                        expect($test)->toEqual('value');

                    });

                });

                context('when the container throws a NotFoundExceptionInterface', function () {

                    it('should return null', function () {

                        $exception = mock([Throwable::class, NotFoundExceptionInterface::class]);

                        $this->container->get->with('id')->throws($exception);

                        $test = $this->value->value($this->container->get());

                        expect($test)->toBeNull();

                    });

                });

            });

            context('when the container entry is not defined', function () {

                it('should return null', function () {

                    $this->container->has->with('id')->returns(false);

                    $test = $this->value->value($this->container->get());

                    expect($test)->toBeNull();

                });

            });

        });

        describe('->str()', function () {

            it('should return the string representation of the nullable container entry', function () {

                $test = $this->value->str('container');

                expect($test)->toEqual(<<<'EOT'
(function (\Psr\Container\ContainerInterface $container) {
    if ($container->has('id')) {
        try { return $container->get('id'); }
        catch (\Psr\Container\NotFoundExceptionInterface $e) { return null; }
    }
    return null;
})($container)
EOT
                );

            });

        });

    });

});

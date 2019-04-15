<?php

use function Eloquent\Phony\Kahlan\mock;

use Psr\Container\ContainerInterface;

use Quanta\Container\Alias;
use Quanta\Container\FactoryInterface;

describe('Alias', function () {

    context('when there is no nullable boolean', function () {

        it('should be an alias with the id and nullable set to false', function () {

            $test = new Alias('id');

            expect($test)->toEqual(new Alias('id', false));

        });

    });

    context('when there is a nullable boolean', function () {

        context('when the nullable boolean is set to false', function () {

            beforeEach(function () {

                $this->factory = new Alias('id', false);

            });

            it('should implement FactoryInterface', function () {

                expect($this->factory)->toBeAnInstanceOf(FactoryInterface::class);

            });

            describe('->__invoke()', function () {

                it('should return the container entry associated with the id', function () {

                    $container = mock(ContainerInterface::class);

                    $container->get->with('id')->returns('value');

                    $test = ($this->factory)($container->get());

                    expect($test)->toEqual('value');

                });

            });

            describe('->compiled()', function () {

                it('should return a compiled version of the alias', function () {

                    $test = $this->factory->compiled('container', function () {});

                    expect($test)->toEqual('$container->get(\'id\')');

                });

            });

        });

        context('when the nullable boolean is set to true', function () {

            beforeEach(function () {

                $this->factory = new Alias('id', true);

            });

            it('should implement FactoryInterface', function () {

                expect($this->factory)->toBeAnInstanceOf(FactoryInterface::class);

            });

            describe('->__invoke()', function () {

                beforeEach(function () {

                    $this->container = mock(ContainerInterface::class);

                });

                context('when the container does not contains id', function () {

                    it('should return null', function () {

                        $this->container->has->with('id')->returns(false);

                        $test = ($this->factory)($this->container->get());

                        expect($test)->toBeNull();

                    });

                });

                context('when the container contains id', function () {

                    it('should return the container entry', function () {

                        $this->container->has->with('id')->returns(true);
                        $this->container->get->with('id')->returns('value');

                        $test = ($this->factory)($this->container->get());

                        expect($test)->toEqual('value');

                    });

                });

            });

            describe('->compiled()', function () {

                it('should return a compiled version of the nullable alias', function () {

                    $test = $this->factory->compiled('container', function () {});

                    expect($test)->toEqual(implode(PHP_EOL, [
                        '$container->has(\'id\')',
                        '    ? $container->get(\'id\')',
                        '    : null',
                    ]));

                });

            });

        });

    });

});

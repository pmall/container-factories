<?php

use function Eloquent\Phony\Kahlan\mock;

use Psr\Container\ContainerInterface;

use Quanta\Container\EnvVar;
use Quanta\Container\FactoryInterface;

describe('EnvVar', function () {

    context('when there is no default value', function () {

        it('should use an empty string as default value and string as type', function () {

            $test = new EnvVar('QUANTA_TEST');

            expect($test)->toEqual(new EnvVar('QUANTA_TEST', '', 'string'));

        });

    });

    context('when there is a default value', function () {

        context('when there is no type', function () {

            it('should use string as type', function () {

                $test = new EnvVar('QUANTA_TEST', 'default');

                expect($test)->toEqual(new EnvVar('QUANTA_TEST', 'default', 'string'));

            });

        });

        context('when there is a type', function () {

            beforeEach(function () {

                $this->factory = new EnvVar('QUANTA_TEST', '2', 'int');

            });

            it('should implement FactoryInterface', function () {

                expect($this->factory)->toBeAnInstanceOf(FactoryInterface::class);

            });

            describe('->__invoke()', function () {

                beforeEach(function () {

                    $this->container = mock(ContainerInterface::class);

                });

                context('when the env variable is set', function () {

                    it('should return the value of the env variable casted as the type', function () {

                        putenv('QUANTA_TEST=1');

                        $test = ($this->factory)($this->container->get());

                        expect($test)->toBe(1);

                    });

                });

                context('when the env variable is not set', function () {

                    it('should return the default value casted as the type', function () {

                        putenv('QUANTA_TEST');

                        $test = ($this->factory)($this->container->get());

                        expect($test)->toBe(2);

                    });

                });

            });

            describe('->compiled()', function () {

                it('should return a compiled version of the env var', function () {

                    $test = $this->factory->compiled('container', function () {});

                    expect($test)->toEqual(implode(PHP_EOL, [
                    '(function () {',
                    '    $value = getenv(\'QUANTA_TEST\');',
                    '    if ($value === false) $value = \'2\';',
                    '    settype($value, \'int\');',
                    '    return $value;',
                    '})()',
                    ]));

                });

            });

        });

    });

});

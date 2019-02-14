<?php

use function Eloquent\Phony\Kahlan\mock;

use Psr\Container\ContainerInterface;

use Quanta\Container\Values\EnvVar;
use Quanta\Container\Values\ValueInterface;

describe('EnvVar', function () {

    context('when there is no default value', function () {

        beforeEach(function () {

            $this->value = new EnvVar('QUANTA_TEST');

        });

        it('should implement ValueInterface', function () {

            expect($this->value)->toBeAnInstanceOf(ValueInterface::class);

        });

        describe('->value()', function () {

            beforeEach(function () {

                $this->container = mock(ContainerInterface::class);

            });

            context('when the env variable is set', function () {

                it('should return the value of the env variable', function () {

                    putenv('QUANTA_TEST=1');

                    $test = $this->value->value($this->container->get());

                    expect($test)->toBe('1');

                });

            });

            context('when the env variable is not set', function () {

                it('should return an empty string', function () {

                    putenv('QUANTA_TEST');

                    $test = $this->value->value($this->container->get());

                    expect($test)->toBe('');

                });

            });

        });

        describe('->str()', function () {

            it('should return a string representation of the env variable with \'\' as default value and \'string\' as type', function () {

                $test = $this->value->str('container');

                expect($test)->toEqual(<<<'EOT'
(function (\Psr\Container\ContainerInterface $container) {
    $value = getenv('QUANTA_TEST');
    if ($value === false) $value = '';
    settype($value, 'string');
    return $value;
})($container)
EOT
                );

            });

        });

    });

    context('when there is a default value', function () {

        context('when there is no type', function () {

            beforeEach(function () {

                $this->value = new EnvVar('QUANTA_TEST', '2');

            });

            it('should implement ValueInterface', function () {

                expect($this->value)->toBeAnInstanceOf(ValueInterface::class);

            });

            describe('->value()', function () {

                beforeEach(function () {

                    $this->container = mock(ContainerInterface::class);

                });

                context('when the env variable is set', function () {

                    it('should return the value of the env variable', function () {

                        putenv('QUANTA_TEST=1');

                        $test = $this->value->value($this->container->get());

                        expect($test)->toBe('1');

                    });

                });

                context('when the env variable is not set', function () {

                    it('should return the default value', function () {

                        putenv('QUANTA_TEST');

                        $test = $this->value->value($this->container->get());

                        expect($test)->toBe('2');

                    });

                });

            });

            describe('->str()', function () {

                it('should return a string representation of the env variable with the default value and \'string\' as type', function () {

                    $test = $this->value->str('container');

                    expect($test)->toEqual(<<<'EOT'
(function (\Psr\Container\ContainerInterface $container) {
    $value = getenv('QUANTA_TEST');
    if ($value === false) $value = '2';
    settype($value, 'string');
    return $value;
})($container)
EOT
                    );

                });

            });

        });

        context('when there is a type', function () {

            beforeEach(function () {

                $this->value = new EnvVar('QUANTA_TEST', '2', 'int');

            });

            it('should implement ValueInterface', function () {

                expect($this->value)->toBeAnInstanceOf(ValueInterface::class);

            });

            describe('->value()', function () {

                beforeEach(function () {

                    $this->container = mock(ContainerInterface::class);

                });

                context('when the env variable is set', function () {

                    it('should return the value of the env variable casted as the type', function () {

                        putenv('QUANTA_TEST=1');

                        $test = $this->value->value($this->container->get());

                        expect($test)->toBe(1);

                    });

                });

                context('when the env variable is not set', function () {

                    it('should return the default value casted as the type', function () {

                        putenv('QUANTA_TEST');

                        $test = $this->value->value($this->container->get());

                        expect($test)->toBe(2);

                    });

                });

            });

            describe('->str()', function () {

                it('should return a string representation of the env variable with the default value and the type', function () {

                    $test = $this->value->str('container');

                    expect($test)->toEqual(<<<'EOT'
(function (\Psr\Container\ContainerInterface $container) {
    $value = getenv('QUANTA_TEST');
    if ($value === false) $value = '2';
    settype($value, 'int');
    return $value;
})($container)
EOT
                    );

                });

            });

        });

    });

});

<?php

use function Eloquent\Phony\Kahlan\mock;

use Psr\Container\ContainerInterface;

use Quanta\Container\Factories\Tag;
use Quanta\Container\Factories\CompilableFactoryInterface;

use Quanta\Container\Compilation\Template;

use Quanta\Exceptions\ArrayArgumentTypeErrorMessage;

describe('Tag', function () {

    context('when the array of identifiers is empty', function () {

        beforeEach(function () {

            $this->factory = new Tag;

        });

        it('should implement CompilableFactoryInterface', function () {

            expect($this->factory)->toBeAnInstanceOf(CompilableFactoryInterface::class);

        });

        describe('->__invoke()', function () {

            context('when no array of previous container entries is given', function () {

                it('should return an empty array', function () {

                    $container = mock(ContainerInterface::class);

                    $test = ($this->factory)($container->get());

                    expect($test)->toEqual([]);

                });

            });

            context('when an array of previous container entries is given', function () {

                it('should return the given array of previous container entries', function () {

                    $container = mock(ContainerInterface::class);

                    $previous = ['k1' => 'k1', 'k2' => 'k2', 'k3' => 'k3'];

                    $test = ($this->factory)($container->get(), $previous);

                    expect($test)->toEqual($previous);

                });

            });

        });

        describe('->compiled()', function () {

            it('should return a string representation of a factory returning the array of previous container entries', function () {

                $template = Template::withDummyClosureCompiler('container_var_name');

                $test = $this->factory->compiled($template);

                expect($test)->toEqual(<<<'EOT'
function (\Psr\Container\ContainerInterface $container_var_name, array $tagged = []) {
    $entries = array_map([$container_var_name, 'get'], []);
    return array_merge($tagged, $entries);
}
EOT
                );

            });

        });

    });

    context('when the array of identifiers is not empty', function () {

        context('when a value of the array of identifiers is not a string', function () {

            it('should throw an InvalidArgumentException', function () {

                ArrayArgumentTypeErrorMessage::testing();

                $ids = ['k1' => 'v1', 'k2' => [], 'k3' => 'v3'];

                $test = function () use ($ids) { new Tag($ids); };

                expect($test)->toThrow(new InvalidArgumentException(
                    (string) new ArrayArgumentTypeErrorMessage(1, 'string', $ids)
                ));

            });

        });

        context('when all the values of the array of identifiers are strings', function () {

            beforeEach(function () {

                $this->factory = new Tag(['k1' => 'id1', 'k2' => 'id2', 'k3' => 'id3']);

            });

            it('should implement CompilableFactoryInterface', function () {

                expect($this->factory)->toBeAnInstanceOf(CompilableFactoryInterface::class);

            });

            describe('->__invoke()', function () {

                context('when no array of previous container entries is given', function () {

                    it('should return the array of container entries', function () {

                        $container = mock(ContainerInterface::class);

                        $container->get->with('id1')->returns('v1');
                        $container->get->with('id2')->returns('v2');
                        $container->get->with('id3')->returns('v3');

                        $test = ($this->factory)($container->get());

                        expect($test)->toEqual(['k1' => 'v1', 'k2' => 'v2', 'k3' => 'v3']);

                    });

                });

                context('when an array of previous container entries is given', function () {

                    it('should return the array of container entries merged with the given array of previous container entries', function () {

                        $container = mock(ContainerInterface::class);

                        $container->get->with('id1')->returns('v1');
                        $container->get->with('id2')->returns('v2');
                        $container->get->with('id3')->returns('v3');

                        $test = ($this->factory)($container->get(), [
                            'k4' => 'v4', 'k5' => 'v5', 'k2' => 'v6'
                        ]);

                        expect($test)->toEqual([
                            'k1' => 'v1',
                            'k2' => 'v2',
                            'k3' => 'v3',
                            'k4' => 'v4',
                            'k5' => 'v5',
                        ]);

                    });

                });

            });

            describe('->compiled()', function () {

                it('should return a string representation of a factory merging the container entries with the array of previous container entries', function () {

                    $template = Template::withDummyClosureCompiler('container_var_name');

                    $test = $this->factory->compiled($template);

                    expect($test)->toEqual(<<<'EOT'
function (\Psr\Container\ContainerInterface $container_var_name, array $tagged = []) {
    $entries = array_map([$container_var_name, 'get'], [
        'k1' => 'id1',
        'k2' => 'id2',
        'k3' => 'id3',
    ]);
    return array_merge($tagged, $entries);
}
EOT
                    );

                });

            });

        });

    });

});

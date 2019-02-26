<?php

use function Eloquent\Phony\Kahlan\mock;

use Psr\Container\ContainerInterface;

use Quanta\Container\Factories\Tag;
use Quanta\Container\Factories\Compiler;
use Quanta\Container\Factories\CompilableFactoryInterface;

describe('Tag', function () {

    context('when there is no identifier', function () {

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

                    $test = ($this->factory)($container->get(), ['k1', 'k2', 'k3']);

                    expect($test)->toEqual(['k1', 'k2', 'k3']);

                });

            });

        });

        describe('->compiled()', function () {

            it('should return a string representation of the tag', function () {

                $compiler = Compiler::withDummyClosureCompiler();

                $test = (string) $this->factory->compiled($compiler);

                expect($test)->toEqual(<<<'EOT'
function (\Psr\Container\ContainerInterface $container, array $tagged = []) {
    return array_merge($tagged, array_map([$container, 'get'], []));
}
EOT
                );

            });

        });

    });

    context('when there is at least one identifier', function () {

        beforeEach(function () {

            $this->factory = new Tag('id1', 'id2', 'id3');

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

                    expect($test)->toEqual(['v1', 'v2', 'v3']);

                });

            });

            context('when an array of previous container entries is given', function () {

                it('should return the array of container entries merged with the given array of previous container entries', function () {

                    $container = mock(ContainerInterface::class);

                    $container->get->with('id1')->returns('v1');
                    $container->get->with('id2')->returns('v2');
                    $container->get->with('id3')->returns('v3');

                    $test = ($this->factory)($container->get(), ['v4', 'v5', 'v6']);

                    expect($test)->toEqual(['v4', 'v5', 'v6', 'v1', 'v2', 'v3']);

                });

            });

        });

        describe('->compiled()', function () {

            it('should return a string representation of the tag', function () {

                $compiler = Compiler::withDummyClosureCompiler();

                $test = (string) $this->factory->compiled($compiler);

                expect($test)->toEqual(<<<'EOT'
function (\Psr\Container\ContainerInterface $container, array $tagged = []) {
    return array_merge($tagged, array_map([$container, 'get'], [
        'id1',
        'id2',
        'id3',
    ]));
}
EOT
                );

            });

        });

    });

});

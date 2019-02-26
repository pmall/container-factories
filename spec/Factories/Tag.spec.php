<?php

use function Eloquent\Phony\Kahlan\mock;

use Psr\Container\ContainerInterface;

use Quanta\Container\Factories\Tag;
use Quanta\Container\Factories\Compiler;
use Quanta\Container\Factories\CompilableFactoryInterface;

describe('Tag', function () {

    beforeEach(function () {

        $this->factory = new Tag('id');

    });

    it('should implement CompilableFactoryInterface', function () {

        expect($this->factory)->toBeAnInstanceOf(CompilableFactoryInterface::class);

    });

    describe('->__invoke()', function () {

        beforeEach(function () {

            $this->container = mock(ContainerInterface::class);

            $this->container->get->with('id')->returns('value');

        });

        context('when no array of previous container entries is given', function () {

            it('should return an array containing the container entry', function () {

                $test = ($this->factory)($this->container->get());

                expect($test)->toEqual(['value']);

            });

        });

        context('when an array of previous container entries is given', function () {

            it('should add the container entry to the given array of previous container entries', function () {

                $test = ($this->factory)($this->container->get(), [
                    'previous1',
                    'previous2',
                    'previous3',
                ]);

                expect($test)->toEqual(['previous1', 'previous2', 'previous3', 'value']);

            });

        });

    });

    describe('->compiled()', function () {

        it('should return a string representation of the tag', function () {

            $compiler = Compiler::withDummyClosureCompiler();

            $test = (string) $this->factory->compiled($compiler);

            expect($test)->toEqual(<<<'EOT'
function (\Psr\Container\ContainerInterface $container, array $tagged) {
    return array_merge($tagged, [$container->get('id')]);
}
EOT
            );

        });

    });

});

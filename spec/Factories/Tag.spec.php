<?php

use function Eloquent\Phony\Kahlan\mock;

use Psr\Container\ContainerInterface;

use Quanta\Container\Factories\Tag;
use Quanta\Container\Factories\Compiler;
use Quanta\Container\Factories\DummyClosureCompiler;
use Quanta\Container\Factories\CompilableFactoryInterface;

describe('Tag::instance()', function () {

    it('should return a new Tag with the given ids', function () {

        $test = Tag::instance('id1', 'id2', 'id3');

        expect($test)->toEqual(new Tag('id1', 'id2', 'id3'));

    });

});

describe('Tag', function () {

    context('when there is no id', function () {

        beforeEach(function () {

            $this->factory = new Tag;

        });

        it('should implement CompilableFactoryInterface', function () {

            expect($this->factory)->toBeAnInstanceOf(CompilableFactoryInterface::class);

        });

        describe('->__invoke()', function () {

            it('should return an empty array', function () {

                $container = mock(ContainerInterface::class);

                $test = ($this->factory)($container->get());

                expect($test)->toEqual([]);

            });

        });

        describe('->compiled()', function () {

            it('should return the string representation of a factory returning an empty array', function () {

                $compiler = new Compiler(new DummyClosureCompiler);

                $test = (string) $this->factory->compiled($compiler);

                expect($test)->toEqual(<<<'EOT'
function (\Psr\Container\ContainerInterface $container) {
    return [];
}
EOT
                );

            });

        });

    });

    context('when there is at leas one id', function () {

        beforeEach(function () {

            $this->factory = new Tag('id1', 'id2', 'id3');

        });

        it('should implement CompilableFactoryInterface', function () {

            expect($this->factory)->toBeAnInstanceOf(CompilableFactoryInterface::class);

        });

        describe('->__invoke()', function () {

            it('should return an array containing the container entries', function () {

                $container = mock(ContainerInterface::class);

                $container->get->with('id1')->returns('value1');
                $container->get->with('id2')->returns('value2');
                $container->get->with('id3')->returns('value3');

                $test = ($this->factory)($container->get());

                expect($test)->toEqual(['value1', 'value2', 'value3']);

            });

        });

        describe('->compiled()', function () {

            it('should return a string representation of the tag', function () {

                $compiler = new Compiler(new DummyClosureCompiler);

                $test = (string) $this->factory->compiled($compiler);

                expect($test)->toEqual(<<<'EOT'
function (\Psr\Container\ContainerInterface $container) {
    return array_map([$container, 'get'], [
        'id1',
        'id2',
        'id3',
    ]);
}
EOT
                );

            });

        });

    });

});

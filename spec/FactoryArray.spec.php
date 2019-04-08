<?php

use function Eloquent\Phony\Kahlan\mock;

use Psr\Container\ContainerInterface;

use Quanta\Container\FactoryArray;
use Quanta\Container\FactoryInterface;
use Quanta\Container\Compilation\Compiler;
use Quanta\Container\Compilation\CompilableInterface;

require_once __DIR__ . '/.test/classes.php';

describe('FactoryArray', function () {

    beforeEach(function () {

        $this->factory1 = mock(FactoryInterface::class);
        $this->factory2 = mock(FactoryInterface::class);
        $this->factory3 = mock(FactoryInterface::class);

    });

    context('when all the values of the array of factories are implementations of FactoryInterface', function () {

        beforeEach(function () {

            $this->factory = new FactoryArray([
                'id1' => $this->factory1->get(),
                'id2' => $this->factory2->get(),
                'id3' => $this->factory3->get(),
            ]);

        });

        it('should implement FactoryInterface', function () {

            expect($this->factory)->toBeAnInstanceOf(FactoryInterface::class);

        });

        describe('->__invoke()', function () {

            it('should return an array of the factories values', function () {

                $container = mock(ContainerInterface::class);

                $this->factory1->__invoke->with($container)->returns('value1');
                $this->factory2->__invoke->with($container)->returns('value2');
                $this->factory3->__invoke->with($container)->returns('value3');

                $test = ($this->factory)($container->get());

                expect($test)->toEqual([
                    'id1' => 'value1',
                    'id2' => 'value2',
                    'id3' => 'value3',
                ]);

            });

        });

        describe('->compilable()', function () {

            it('should return a compilable version of the factory array', function () {

                $compilable1 = mock(CompilableInterface::class);
                $compilable2 = mock(CompilableInterface::class);
                $compilable3 = mock(CompilableInterface::class);

                $compiler = Compiler::testing([
                    'precompiled' => [
                        'id1' => $compilable1->get(),
                        'id2' => $compilable2->get(),
                        'id3' => $compilable3->get(),
                    ]
                ]);

                $this->factory1->compilable->with('container')->returns($compilable1);
                $this->factory2->compilable->with('container')->returns($compilable2);
                $this->factory3->compilable->with('container')->returns($compilable3);

                $test = $this->factory->compilable('container');

                expect($compiler($test))->toEqual('precompiled');

            });

        });

    });

    context('when a value of the of the array of factories is not an implementation of FactoryInterface', function () {

        it('should throw an InvalidArgumentException', function () {

            $test = function () {
                new FactoryArray([
                    'id1' => $this->factory1->get(),
                    'id2' => 2,
                    'id3' => $this->factory3->get(),
                ]);
            };

            expect($test)->toThrow(new InvalidArgumentException);

        });

    });

});

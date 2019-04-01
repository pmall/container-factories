<?php

use function Eloquent\Phony\Kahlan\mock;

use Psr\Container\ContainerInterface;

use Quanta\Container\Instance;
use Quanta\Container\FactoryInterface;
use Quanta\Container\Compilation\Compiler;
use Quanta\Container\Compilation\CompilableInterface;

require_once __DIR__ . '/.test/classes.php';

describe('Instance::instance()', function () {

    context('when no factory is given', function () {

        it('should return a new Instance with the given class name and no factory', function () {

            $test = Instance::instance(Test\TestClass::class);

            expect($test)->toEqual(new Instance(Test\TestClass::class));

        });

    });

    context('when factories are given', function () {

        it('should return a new Instance with the given class name and factories', function () {

            $factory1 = mock(FactoryInterface::class);
            $factory2 = mock(FactoryInterface::class);
            $factory3 = mock(FactoryInterface::class);

            $test = Instance::instance(Test\TestClass::class, ...[
                $factory1->get(),
                $factory2->get(),
                $factory3->get(),
            ]);

            expect($test)->toEqual(new Instance(Test\TestClass::class, ...[
                $factory1->get(),
                $factory2->get(),
                $factory3->get(),
            ]));

        });

    });

});

describe('Instance', function () {

    context('when there is no factories', function () {

        beforeEach(function () {

            $this->factory = new Instance(Test\TestClass::class);

        });

        it('should implement FactoryInterface', function () {

            expect($this->factory)->toBeAnInstanceOf(FactoryInterface::class);

        });

        describe('->__invoke()', function () {

            it('should return an instance of the class with no argument', function () {

                $container = mock(ContainerInterface::class);

                $test = ($this->factory)($container->get());

                expect($test)->toEqual(new Test\TestClass);

            });

        });

        describe('->compilable()', function () {

            it('should return a compilable version of the instance with no argument', function () {

                $compiler = Compiler::testing();

                $test = $this->factory->compilable('container');

                expect($compiler($test))->toEqual('new Test\TestClass');

            });

        });

    });

    context('when the is factories', function () {

        beforeEach(function () {

            $this->factory1 = mock(FactoryInterface::class);
            $this->factory2 = mock(FactoryInterface::class);
            $this->factory3 = mock(FactoryInterface::class);

            $this->factory = new Instance(Test\TestClass::class, ...[
                $this->factory1->get(),
                $this->factory2->get(),
                $this->factory3->get(),
            ]);

        });

        it('should implement FactoryInterface', function () {

            expect($this->factory)->toBeAnInstanceOf(FactoryInterface::class);

        });

        describe('->__invoke()', function () {

            it('should return an instance of the class with the value produced by the factories as argument', function () {

                $container = mock(ContainerInterface::class);

                $this->factory1->__invoke->with($container)->returns('value1');
                $this->factory2->__invoke->with($container)->returns('value2');
                $this->factory3->__invoke->with($container)->returns('value3');

                $test = ($this->factory)($container->get());

                expect($test)->toEqual(new Test\TestClass('value1', 'value2', 'value3'));

            });

        });

        describe('->compilable()', function () {

            it('should return a compilable version of the instance with arguments', function () {

                $compilable1 = mock(CompilableInterface::class);
                $compilable2 = mock(CompilableInterface::class);
                $compilable3 = mock(CompilableInterface::class);

                $compiler = Compiler::testing([
                    'precompiled1' => $compilable1->get(),
                    'precompiled2' => $compilable2->get(),
                    'precompiled3' => $compilable3->get(),
                ]);

                $this->factory1->compilable->with('container')->returns($compilable1);
                $this->factory2->compilable->with('container')->returns($compilable2);
                $this->factory3->compilable->with('container')->returns($compilable3);

                $test = $this->factory->compilable('container');

                expect($compiler($test))->toEqual(implode(PHP_EOL, [
                    'new Test\TestClass(',
                    '    precompiled1,',
                    '    precompiled2,',
                    '    precompiled3',
                    ')',
                ]));

            });

        });

    });

});

<?php

use function Eloquent\Phony\Kahlan\mock;

use Psr\Container\ContainerInterface;

use Quanta\Container\Instance;
use Quanta\Container\FactoryInterface;

require_once __DIR__ . '/.test/classes.php';

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

        describe('->compiled()', function () {

            it('should return a compiled version of the instance with no argument', function () {

                $test = $this->factory->compiled('container', function () {});

                expect($test)->toEqual('new Test\TestClass');

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

        describe('->compiled()', function () {

            it('should return a compiled version of the instance with arguments', function () {

                $compiler = function () {};

                $this->factory1->compiled
                    ->with('container', Kahlan\Arg::toBe($compiler))
                    ->returns(implode(PHP_EOL, ['value11', 'value12', 'value13']));

                $this->factory2->compiled
                    ->with('container', Kahlan\Arg::toBe($compiler))
                    ->returns(implode(PHP_EOL, ['value21', 'value22', 'value23']));

                $this->factory3->compiled
                    ->with('container', Kahlan\Arg::toBe($compiler))
                    ->returns(implode(PHP_EOL, ['value31', 'value32', 'value33']));

                $test = $this->factory->compiled('container', $compiler);

                expect($test)->toEqual(implode(PHP_EOL, [
                    'new Test\TestClass(',
                    '    value11',
                    '    value12',
                    '    value13,',
                    '    value21',
                    '    value22',
                    '    value23,',
                    '    value31',
                    '    value32',
                    '    value33',
                    ')',
                ]));

            });

        });

    });

});

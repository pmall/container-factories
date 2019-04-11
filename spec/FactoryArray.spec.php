<?php

use function Eloquent\Phony\Kahlan\mock;

use Psr\Container\ContainerInterface;

use Quanta\Container\FactoryArray;
use Quanta\Container\FactoryInterface;

require_once __DIR__ . '/.test/classes.php';

describe('FactoryArray', function () {

    context('when the factory array is empty', function () {

        beforeEach(function () {

            $this->factory = new FactoryArray([]);

        });

        it('should implement FactoryInterface', function () {

            expect($this->factory)->toBeAnInstanceOf(FactoryInterface::class);

        });

        describe('->__invoke()', function () {

            it('should return an empty array', function () {

                $container = mock(ContainerInterface::class);

                $test = ($this->factory)($container->get());

                expect($test)->toEqual([]);

            });

        });

        describe('->compiled()', function () {

            it('should return []', function () {

                $test = $this->factory->compiled('container', function () {});

                expect($test)->toEqual('[]');

            });

        });

    });

    context('when the factory array is not empty', function () {

        beforeEach(function () {

            $this->factory1 = mock(FactoryInterface::class);
            $this->factory2 = mock(FactoryInterface::class);
            $this->factory3 = mock(FactoryInterface::class);

        });

        context('when all the values of the array of factories are implementations of FactoryInterface', function () {

            beforeEach(function () {

                $this->factory = new FactoryArray([
                    $this->factory1->get(),
                    'key2' => $this->factory2->get(),
                    10 => $this->factory3->get(),
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
                        0 => 'value1',
                        'key2' => 'value2',
                        10 => 'value3',
                    ]);

                });

            });

            describe('->compiled()', function () {

                it('should return a compiled version of the factory array', function () {

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
                        '[',
                        '    0 => value11',
                        '    value12',
                        '    value13,',
                        '    \'key2\' => value21',
                        '    value22',
                        '    value23,',
                        '    10 => value31',
                        '    value32',
                        '    value33,',
                        ']',
                    ]));

                });

            });

        });

        context('when a value of the of the array of factories is not an implementation of FactoryInterface', function () {

            it('should throw an InvalidArgumentException', function () {

                $test = function () {
                    new FactoryArray([
                        $this->factory1->get(),
                        'id2' => 2,
                        10 => $this->factory3->get(),
                    ]);
                };

                expect($test)->toThrow(new InvalidArgumentException);

            });

        });

    });

});

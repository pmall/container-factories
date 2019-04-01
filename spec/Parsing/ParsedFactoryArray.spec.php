<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\FactoryArray;
use Quanta\Container\FactoryInterface;
use Quanta\Container\Parsing\ParsedFactoryArray;
use Quanta\Container\Parsing\ParsedFactoryInterface;

describe('ParsedFactoryArray', function () {

    beforeEach(function () {

        $this->parsed1 = mock(ParsedFactoryInterface::class);
        $this->parsed2 = mock(ParsedFactoryInterface::class);
        $this->parsed3 = mock(ParsedFactoryInterface::class);

    });

    context('when all the values of the array of parsed factories are implementations of ParsedFactoryInterface', function () {

        beforeEach(function () {

            $this->parsed = new ParsedFactoryArray([
                $this->parsed1->get(),
                'key2' => $this->parsed2->get(),
                $this->parsed3->get(),
            ]);

        });

        it('should implement ParsedFactoryInterface', function () {

            expect($this->parsed)->toBeAnInstanceOf(ParsedFactoryInterface::class);

        });

        describe('->success()', function () {

            it('should return true', function () {

                $test = $this->parsed->success();

                expect($test)->toBeTruthy();

            });

        });

        describe('->factory()', function () {

            it('should a factory array containing all the parsed factories', function () {

                $factory1 = mock(FactoryInterface::class);
                $factory2 = mock(FactoryInterface::class);
                $factory3 = mock(FactoryInterface::class);

                $this->parsed1->factory->returns($factory1);
                $this->parsed2->factory->returns($factory2);
                $this->parsed3->factory->returns($factory3);

                $test = $this->parsed->factory();

                expect($test)->toEqual(new FactoryArray([
                    $factory1->get(),
                    'key2' => $factory2->get(),
                    $factory3->get(),
                ]));

            });

        });

    });

    context('when a value of the of the given array of parsed factories is not an implementation of ParsedFactoryInterface', function () {

        it('should throw an InvalidArgumentException', function () {

            $test = function () {
                new ParsedFactoryArray([
                    $this->parsed1->get(),
                    'key2' => 1,
                    $this->parsed3->get(),
                ]);
            };

            expect($test)->toThrow(new InvalidArgumentException);

        });

    });

});

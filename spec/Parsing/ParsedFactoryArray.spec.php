<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\FactoryArray;
use Quanta\Container\FactoryInterface;
use Quanta\Container\Parsing\ParsedFactoryArray;
use Quanta\Container\Parsing\ParsingResultInterface;

describe('ParsedFactoryArray', function () {

    beforeEach(function () {

        $this->result1 = mock(ParsingResultInterface::class);
        $this->result2 = mock(ParsingResultInterface::class);
        $this->result3 = mock(ParsingResultInterface::class);

    });

    context('when all the values of the array of parsed factories are implementations of ParsingResultInterface', function () {

        beforeEach(function () {

            $this->result = new ParsedFactoryArray([
                $this->result1->get(),
                'key2' => $this->result2->get(),
                $this->result3->get(),
            ]);

        });

        it('should implement ParsingResultInterface', function () {

            expect($this->result)->toBeAnInstanceOf(ParsingResultInterface::class);

        });

        describe('->isParsed()', function () {

            it('should return true', function () {

                $test = $this->result->isParsed();

                expect($test)->toBeTruthy();

            });

        });

        describe('->factory()', function () {

            it('should a factory array containing all the parsed factories', function () {

                $factory1 = mock(FactoryInterface::class);
                $factory2 = mock(FactoryInterface::class);
                $factory3 = mock(FactoryInterface::class);

                $this->result1->factory->returns($factory1);
                $this->result2->factory->returns($factory2);
                $this->result3->factory->returns($factory3);

                $test = $this->result->factory();

                expect($test)->toEqual(new FactoryArray([
                    $factory1->get(),
                    'key2' => $factory2->get(),
                    $factory3->get(),
                ]));

            });

        });

    });

    context('when a value of the of the given array of parsed factories is not an implementation of ParsingResultInterface', function () {

        it('should throw an InvalidArgumentException', function () {

            $test = function () {
                new ParsedFactoryArray([
                    $this->result1->get(),
                    'key2' => 1,
                    $this->result3->get(),
                ]);
            };

            expect($test)->toThrow(new InvalidArgumentException);

        });

    });

});

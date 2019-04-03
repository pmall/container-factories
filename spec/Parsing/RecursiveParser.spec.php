<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\Parsing\ParserInterface;
use Quanta\Container\Parsing\RecursiveParser;
use Quanta\Container\Parsing\ParsedFactoryArray;
use Quanta\Container\Parsing\ParsingResultInterface;

describe('RecursiveParser', function () {

    beforeEach(function () {

        $this->delegate = mock(ParserInterface::class);

        $this->parser = new RecursiveParser($this->delegate->get());

    });

    it('should implement ParserInterface', function () {

        expect($this->parser)->toBeAnInstanceOf(ParserInterface::class);

    });

    describe('->__invoke()', function () {

        context('when the value is not an array', function () {

            it('should return the result of the delegate parser', function () {

                $result = mock(ParsingResultInterface::class);

                $this->delegate->__invoke->with('value')->returns($result);

                $test = ($this->parser)('value');

                expect($test)->toBe($result->get());

            });

        });

        context('when the value is an array', function () {

            it('should return the result of the delegate parser for the given array values as a parsed factory array', function () {

                $result1 = mock(ParsingResultInterface::class);
                $result2 = mock(ParsingResultInterface::class);
                $result3 = mock(ParsingResultInterface::class);

                $this->delegate->__invoke->with('value1')->returns($result1);
                $this->delegate->__invoke->with('value2')->returns($result2);
                $this->delegate->__invoke->with('value3')->returns($result3);

                $test = ($this->parser)(['value1', 'key2' => 'value2', 'value3']);

                expect($test)->toEqual(new ParsedFactoryArray([
                    $result1->get(),
                    'key2' => $result2->get(),
                    $result3->get(),
                ]));

            });

        });

    });

});

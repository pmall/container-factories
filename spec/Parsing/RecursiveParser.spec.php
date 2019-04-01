<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\Parsing\ParserInterface;
use Quanta\Container\Parsing\RecursiveParser;
use Quanta\Container\Parsing\ParsedFactoryArray;
use Quanta\Container\Parsing\ParsedFactoryInterface;

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

                $parsed = mock(ParsedFactoryInterface::class);

                $this->delegate->__invoke->with('value')->returns($parsed);

                $test = ($this->parser)('value');

                expect($test)->toBe($parsed->get());

            });

        });

        context('when the value is an array', function () {

            it('should return the result of the delegate parser for the given array values as a parsed factory array', function () {

                $parsed1 = mock(ParsedFactoryInterface::class);
                $parsed2 = mock(ParsedFactoryInterface::class);
                $parsed3 = mock(ParsedFactoryInterface::class);

                $this->delegate->__invoke->with('value1')->returns($parsed1);
                $this->delegate->__invoke->with('value2')->returns($parsed2);
                $this->delegate->__invoke->with('value3')->returns($parsed3);

                $test = ($this->parser)(['value1', 'key2' => 'value2', 'value3']);

                expect($test)->toEqual(new ParsedFactoryArray([
                    $parsed1->get(),
                    'key2' => $parsed2->get(),
                    $parsed3->get(),
                ]));

            });

        });

    });

});

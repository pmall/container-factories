<?php

use Quanta\Container\Values\Value;
use Quanta\Container\Values\ParsedValue;
use Quanta\Container\Values\ValueFactory;
use Quanta\Container\Values\ParsingFailure;
use Quanta\Container\Values\DummyValueParser;
use Quanta\Container\Values\ValueParserInterface;

describe('DummyValueParser', function () {

    beforeEach(function () {

        $this->parser = new DummyValueParser([
            'k1' => 'value1',
            'k2' => 'value2',
            'k3' => 'value3',
        ]);

    });

    it('should implement ValueParserInterface', function () {

        expect($this->parser)->toBeAnInstanceOf(ValueParserInterface::class);

    });

    describe('->__invoke()', function () {

        context('when the given value is not a string', function () {

            it('should return a ParsingFailure', function () {

                $test = ($this->parser)(new ValueFactory, []);

                expect($test)->toBeAnInstanceOf(ParsingFailure::class);

            });

        });

        context('when the given value is a string', function () {

            context('when the given string is in the map', function () {

                it('should return a ParsedValue wrapped around a Value', function () {

                    $test = ($this->parser)(new ValueFactory, 'k2');

                    expect($test)->toEqual(new ParsedValue(
                        new Value('value2')
                    ));

                });

            });

            context('when the given string is not in the map', function () {

                it('should return a ParsingFailure', function () {

                    $test = ($this->parser)(new ValueFactory, 'k4');

                    expect($test)->toBeAnInstanceOf(ParsingFailure::class);

                });

            });

        });

    });

});

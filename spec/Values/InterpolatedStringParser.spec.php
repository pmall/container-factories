<?php

use Quanta\Container\Values\ParsedValue;
use Quanta\Container\Values\ValueFactory;
use Quanta\Container\Values\ParsingFailure;
use Quanta\Container\Values\InterpolatedString;
use Quanta\Container\Values\ValueParserInterface;
use Quanta\Container\Values\InterpolatedStringParser;

describe('InterpolatedStringParser', function () {

    beforeEach(function () {

        $this->parser = new InterpolatedStringParser;

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

            context('when the given string contains at least one placeholder', function () {

                it('should return a ParsedValue wrapped around an InterpolatedString', function () {

                    $test = ($this->parser)(new ValueFactory, 's1:%{id1}:s2:%{id2}:s3');

                    expect($test)->toEqual(new ParsedValue(
                        new InterpolatedString('s1:%s:s2:%s:s3', 'id1', 'id2')
                    ));

                });

            });

            context('when the given string does not contain a placeholder', function () {

                it('should return a ParsingFailure', function () {

                    $test = ($this->parser)(new ValueFactory, 'value');

                    expect($test)->toBeAnInstanceOf(ParsingFailure::class);

                });

            });

        });

    });

});

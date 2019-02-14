<?php

use Quanta\Container\Values\Reference;
use Quanta\Container\Values\ParsedValue;
use Quanta\Container\Values\ValueFactory;
use Quanta\Container\Values\ParsingFailure;
use Quanta\Container\Values\ReferenceParser;
use Quanta\Container\Values\ValueParserInterface;

describe('ReferenceParser', function () {

    beforeEach(function () {

        $this->parser = new ReferenceParser;

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

            context('when the given string starts with @', function () {

                it('should return a ParsedValue wrapped around a Reference', function () {

                    $test = ($this->parser)(new ValueFactory, '@id');

                    expect($test)->toEqual(new ParsedValue(
                        new Reference('id', false)
                    ));

                });

            });

            context('when the given string does not start with @', function () {

                it('should return a ParsingFailure', function () {

                    $test = ($this->parser)(new ValueFactory, 'value');

                    expect($test)->toBeAnInstanceOf(ParsingFailure::class);

                });

            });

        });

    });

});

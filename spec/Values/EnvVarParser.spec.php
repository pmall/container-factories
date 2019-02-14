<?php

use Quanta\Container\Values\EnvVar;
use Quanta\Container\Values\ParsedValue;
use Quanta\Container\Values\EnvVarParser;
use Quanta\Container\Values\ValueFactory;
use Quanta\Container\Values\ParsingFailure;
use Quanta\Container\Values\ValueParserInterface;

describe('EnvVarParser', function () {

    beforeEach(function () {

        $this->parser = new EnvVarParser;

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

            context('when the given string is formatted as env()', function () {

                context('when there is no argument', function () {

                    it('should return a ParsingFailure', function () {

                        $test = ($this->parser)(new ValueFactory, 'env()');

                        expect($test)->toBeAnInstanceOf(ParsingFailure::class);

                    });

                });

                context('when there is one argument', function () {

                    it('should return a ParsedValue wrapped around an EnvVar', function () {

                        $test = ($this->parser)(new ValueFactory, 'env(NAME)');

                        expect($test)->toEqual(new ParsedValue(
                            new EnvVar('NAME')
                        ));

                    });

                });

                context('when there is two arguments', function () {

                    it('should return a ParsedValue wrapped around an EnvVar', function () {

                        $test = ($this->parser)(new ValueFactory, 'env(NAME, default)');

                        expect($test)->toEqual(new ParsedValue(
                            new EnvVar('NAME', 'default')
                        ));

                    });

                });

                context('when there is three arguments', function () {

                    it('should return a ParsedValue wrapped around an EnvVar', function () {

                        $test = ($this->parser)(new ValueFactory, 'env(NAME, default, int)');

                        expect($test)->toEqual(new ParsedValue(
                            new EnvVar('NAME', 'default', 'int')
                        ));

                    });

                });

                context('when there more than three arguments', function () {

                    it('should return a ParsingFailure', function () {

                        $test = ($this->parser)(new ValueFactory, 'env(NAME, default, int, x)');

                        expect($test)->toBeAnInstanceOf(ParsingFailure::class);

                    });

                });

            });

            context('when the given string is not formatted as env()', function () {

                it('should return a ParsingFailure', function () {

                    $test = ($this->parser)(new ValueFactory, 'value');

                    expect($test)->toBeAnInstanceOf(ParsingFailure::class);

                });

            });

        });

    });

});

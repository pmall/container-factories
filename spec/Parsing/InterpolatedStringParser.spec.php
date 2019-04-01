<?php

use Quanta\Container\InterpolatedString;
use Quanta\Container\Parsing\ParsedFactory;
use Quanta\Container\Parsing\ParsingFailure;
use Quanta\Container\Parsing\ParserInterface;
use Quanta\Container\Parsing\InterpolatedStringParser;

describe('InterpolatedStringParser', function () {

    beforeEach(function () {

        $this->parser = new InterpolatedStringParser;

    });

    it('should implement ParserInterface', function () {

        expect($this->parser)->toBeAnInstanceOf(ParserInterface::class);

    });

    describe('->__invoke()', function () {

        context('when the given value is not a string', function () {

            it('should return a ParsingFailure', function () {

                $test = ($this->parser)([]);

                expect($test)->toEqual(new ParsingFailure([]));

            });

        });

        context('when the given value is a string', function () {

            context('when the given string contains at least one placeholder', function () {

                it('should return a ParsedFactory wrapped around an InterpolatedString', function () {

                    $test = ($this->parser)('s1:%{id1}:s2:%{id2}:s3');

                    expect($test)->toEqual(new ParsedFactory(
                        new InterpolatedString('s1:%s:s2:%s:s3', 'id1', 'id2')
                    ));

                });

            });

            context('when the given string does not contain a placeholder', function () {

                it('should return a ParsingFailure', function () {

                    $test = ($this->parser)('value');

                    expect($test)->toEqual(new ParsingFailure('value'));

                });

            });

        });

    });

});

<?php

use Quanta\Container\Alias;
use Quanta\Container\Parsing\AliasParser;
use Quanta\Container\Parsing\ParsedFactory;
use Quanta\Container\Parsing\ParsingFailure;
use Quanta\Container\Parsing\ParserInterface;

describe('AliasParser', function () {

    beforeEach(function () {

        $this->parser = new AliasParser;

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

            context('when the given string starts with @', function () {

                it('should return a ParsedFactory wrapped around an Alias', function () {

                    $test = ($this->parser)('@id');

                    expect($test)->toEqual(new ParsedFactory(new Alias('id')));

                });

            });

            context('when the given string does not start with @', function () {

                it('should return a ParsingFailure', function () {

                    $test = ($this->parser)('value');

                    expect($test)->toEqual(new ParsingFailure('value'));

                });

            });

        });

    });

});

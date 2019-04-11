<?php

use Quanta\Container\Alias;
use Quanta\Container\Parsing\AliasParser;
use Quanta\Container\Parsing\ParsedFactory;
use Quanta\Container\Parsing\ParsingFailure;
use Quanta\Container\Parsing\StringParserInterface;

describe('AliasParser', function () {

    beforeEach(function () {

        $this->parser = new AliasParser;

    });

    it('should implement StringParserInterface', function () {

        expect($this->parser)->toBeAnInstanceOf(StringParserInterface::class);

    });

    describe('->__invoke()', function () {

        context('when the given string starts with @', function () {

            it('should return a ParsedFactory wrapped around an Alias', function () {

                $test = ($this->parser)('@id');

                expect($test)->toEqual(new ParsedFactory(new Alias('id')));

            });

        });

        context('when the given string does not start with @', function () {

            it('should return a ParsingFailure', function () {

                $test = ($this->parser)('value');

                expect($test)->toEqual(new ParsingFailure);

            });

        });

    });

});

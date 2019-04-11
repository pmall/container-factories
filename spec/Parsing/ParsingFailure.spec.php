<?php

use Quanta\Container\Parsing\ParsingFailure;
use Quanta\Container\Parsing\ParsedFactoryInterface;

describe('ParsingFailure', function () {

    beforeEach(function () {

        $this->result = new ParsingFailure;

    });

    it('should implement ParsedFactoryInterface', function () {

        expect($this->result)->toBeAnInstanceOf(ParsedFactoryInterface::class);

    });

    describe('->isParsed()', function () {

        it('should return false', function () {

            $test = $this->result->isParsed();

            expect($test)->toBeFalsy();

        });

    });

    describe('->factory()', function () {

        it('should return throw a LogicException', function () {

            expect([$this->result, 'factory'])->toThrow(new LogicException);

        });

    });

});

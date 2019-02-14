<?php

use Quanta\Container\Values\ParsingFailure;
use Quanta\Container\Values\ParsedValueInterface;

describe('ParsingFailure', function () {

    beforeEach(function () {

        $this->parsed = new ParsingFailure('message');

    });

    it('should implement ParsedValueInterface', function () {

        expect($this->parsed)->toBeAnInstanceOf(ParsedValueInterface::class);

    });

    describe('->success()', function () {

        it('should return false', function () {

            $test = $this->parsed->success();

            expect($test)->toBeFalsy();

        });

    });

    describe('->value()', function () {

        it('should throw a LogicException', function () {

            expect([$this->parsed, 'value'])->toThrow(new LogicException('message'));

        });

    });

});

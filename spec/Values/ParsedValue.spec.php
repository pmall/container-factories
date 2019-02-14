<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\Values\ParsedValue;
use Quanta\Container\Values\ValueInterface;
use Quanta\Container\Values\ParsedValueInterface;

describe('ParsedValue', function () {

    beforeEach(function () {

        $this->value = mock(ValueInterface::class);

        $this->parsed = new ParsedValue($this->value->get());

    });

    it('should implement ParsedValueInterface', function () {

        expect($this->parsed)->toBeAnInstanceOf(ParsedValueInterface::class);

    });

    describe('->success()', function () {

        it('should return true', function () {

            $test = $this->parsed->success();

            expect($test)->toBeTruthy();

        });

    });

    describe('->value()', function () {

        it('should return the value', function () {

            $test = $this->parsed->value();

            expect($test)->toBe($this->value->get());

        });

    });

});

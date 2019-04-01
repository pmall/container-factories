<?php

use Quanta\Container\Parameter;
use Quanta\Container\Parsing\ParsingFailure;
use Quanta\Container\Parsing\ParsedFactoryInterface;

describe('ParsingFailure', function () {

    beforeEach(function () {

        $this->parsed = new ParsingFailure('value');

    });

    it('should implement ParsedFactoryInterface', function () {

        expect($this->parsed)->toBeAnInstanceOf(ParsedFactoryInterface::class);

    });

    describe('->success()', function () {

        it('should return false', function () {

            $test = $this->parsed->success();

            expect($test)->toBeFalsy();

        });

    });

    describe('->factory()', function () {

        it('should return a parameter from the value', function () {

            $test = $this->parsed->factory();

            expect($test)->toEqual(new Parameter('value'));

        });

    });

});

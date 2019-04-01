<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\FactoryInterface;
use Quanta\Container\Parsing\ParsedFactory;
use Quanta\Container\Parsing\ParsedFactoryInterface;

describe('ParsedFactory', function () {

    beforeEach(function () {

        $this->factory = mock(FactoryInterface::class);

        $this->parsed = new ParsedFactory($this->factory->get());

    });

    it('should implement ParsedFactoryInterface', function () {

        expect($this->parsed)->toBeAnInstanceOf(ParsedFactoryInterface::class);

    });

    describe('->success()', function () {

        it('should return true', function () {

            $test = $this->parsed->success();

            expect($test)->toBeTruthy();

        });

    });

    describe('->factory()', function () {

        it('should return the factory', function () {

            $test = $this->parsed->factory();

            expect($test)->toBe($this->factory->get());

        });

    });

});

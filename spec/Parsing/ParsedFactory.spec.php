<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\FactoryInterface;
use Quanta\Container\Parsing\ParsedFactory;
use Quanta\Container\Parsing\ParsedFactoryInterface;

describe('ParsedFactory', function () {

    beforeEach(function () {

        $this->factory = mock(FactoryInterface::class);

        $this->result = new ParsedFactory($this->factory->get());

    });

    it('should implement ParsedFactoryInterface', function () {

        expect($this->result)->toBeAnInstanceOf(ParsedFactoryInterface::class);

    });

    describe('->isParsed()', function () {

        it('should return true', function () {

            $test = $this->result->isParsed();

            expect($test)->toBeTruthy();

        });

    });

    describe('->factory()', function () {

        it('should return the factory', function () {

            $test = $this->result->factory();

            expect($test)->toBe($this->factory->get());

        });

    });

});

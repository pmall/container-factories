<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\Parameter;
use Quanta\Container\Parsing\ParsedFactory;
use Quanta\Container\Parsing\FallbackParser;
use Quanta\Container\Parsing\ParserInterface;
use Quanta\Container\Parsing\ParsingResultInterface;

describe('FallbackParser', function () {

    beforeEach(function () {

        $this->delegate = mock(ParserInterface::class);

        $this->parser = new FallbackParser($this->delegate->get());

    });

    it('should implement ParserInterface', function () {

        expect($this->parser)->toBeAnInstanceOf(ParserInterface::class);

    });

    describe('->__invoke()', function () {

        beforeEach(function () {

            $this->result = mock(ParsingResultInterface::class);

            $this->delegate->__invoke->with('value')->returns($this->result);

        });

        context('when the delegate successfully parse the given value', function () {

            it('should proxy the delegate', function () {

                $this->result->isParsed->returns(true);

                $test = ($this->parser)('value');

                expect($test)->toBe($this->result->get());

            });

        });

        context('when the delegate fails to parse the given value', function () {

            it('should return the value parsed as a parameter', function () {

                $this->result->isParsed->returns(false);

                $test = ($this->parser)('value');

                expect($test)->toEqual(new ParsedFactory(new Parameter('value')));

            });

        });

    });

});

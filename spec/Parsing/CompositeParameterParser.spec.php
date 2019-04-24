<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\Parsing\ParsingFailure;
use Quanta\Container\Parsing\ParsedFactoryInterface;
use Quanta\Container\Parsing\ParameterParserInterface;
use Quanta\Container\Parsing\CompositeParameterParser;

describe('CompositeParameterParser', function () {

    context('when there is no parser', function () {

        beforeEach(function () {

            $this->parser = new CompositeParameterParser;

        });

        it('should implement ParameterParserInterface', function () {

            expect($this->parser)->toBeAnInstanceOf(ParameterParserInterface::class);

        });

        describe('->__invoke()', function () {

            it('should return a parsing failure', function () {

                $parameter = mock(ReflectionParameter::class);

                $test = $this->parser($parameter->get());

                expect($test)->toEqual(new ParsingFailure);

            });

        });

    });

    context('when there is at least one parser', function () {

        beforeEach(function () {

            $this->parser1 = mock(ParameterParserInterface::class);
            $this->parser2 = mock(ParameterParserInterface::class);
            $this->parser3 = mock(ParameterParserInterface::class);

            $this->parser = new CompositeParameterParser(
                $this->parser1->get(),
                $this->parser2->get(),
                $this->parser3->get()
            );

        });

        it('should implement ParameterParserInterface', function () {

            expect($this->parser)->toBeAnInstanceOf(ParameterParserInterface::class);

        });

        describe('->__invoke()', function () {

            beforeEach(function () {

                $this->parameter = mock(ReflectionParameter::class);

                $this->result1 = mock(ParsedFactoryInterface::class);
                $this->result2 = mock(ParsedFactoryInterface::class);
                $this->result3 = mock(ParsedFactoryInterface::class);

                $this->parser1->__invoke->with($this->parameter)->returns($this->result1);
                $this->parser2->__invoke->with($this->parameter)->returns($this->result2);
                $this->parser3->__invoke->with($this->parameter)->returns($this->result3);

            });

            context('when et least one parser successfully parse the parameter', function () {

                it('should return the first successful parsing result', function () {

                    $this->result1->isParsed->returns(false);
                    $this->result2->isParsed->returns(true);
                    $this->result3->isParsed->returns(true);

                    $test = $this->parser($this->parameter->get());

                    expect($test)->toBe($this->result2->get());

                });

            });

            context('when et least no parser successfully parse the parameter', function () {

                it('should return a parsing failure', function () {

                    $this->result1->isParsed->returns(false);
                    $this->result2->isParsed->returns(false);
                    $this->result3->isParsed->returns(false);

                    $test = $this->parser($this->parameter->get());

                    expect($test)->toEqual(new ParsingFailure);

                });

            });

        });

    });

});

<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\FactoryInterface;
use Quanta\Container\Parsing\ParsedFactory;
use Quanta\Container\Parsing\ParsingFailure;
use Quanta\Container\Parsing\ParserInterface;
use Quanta\Container\Parsing\CompositeParser;

describe('CompositeParser', function () {

    context('when there is no parser', function () {

        beforeEach(function () {

            $this->parser = new CompositeParser;

        });

        it('should implement ParserInterface', function () {

            expect($this->parser)->toBeAnInstanceOf(ParserInterface::class);

        });

        describe('->__invoke()', function () {

            it('should return a ParsingFailure', function () {

                $test = ($this->parser)('value');

                expect($test)->toEqual(new ParsingFailure);

            });

        });

    });

    context('when there is at least one parser', function () {

        beforeEach(function () {

            $this->parser1 = mock(ParserInterface::class);
            $this->parser2 = mock(ParserInterface::class);
            $this->parser3 = mock(ParserInterface::class);
            $this->parser4 = mock(ParserInterface::class);
            $this->parser5 = mock(ParserInterface::class);

            $this->parser = new CompositeParser(...[
                $this->parser1->get(),
                $this->parser2->get(),
                $this->parser3->get(),
                $this->parser4->get(),
                $this->parser5->get(),
            ]);

        });

        it('should implement ParserInterface', function () {

            expect($this->parser)->toBeAnInstanceOf(ParserInterface::class);

        });

        describe('->__invoke()', function () {

            context('when at least one parser returns a successfully parsed value', function () {

                it('should return the first successfully matched value', function () {

                    $factory1 = mock(FactoryInterface::class);
                    $factory2 = mock(FactoryInterface::class);

                    $this->parser1->__invoke->with('value')->returns(new ParsingFailure);
                    $this->parser2->__invoke->with('value')->returns(new ParsedFactory($factory1->get()));
                    $this->parser3->__invoke->with('value')->returns(new ParsingFailure);
                    $this->parser4->__invoke->with('value')->returns(new ParsedFactory($factory2->get()));
                    $this->parser5->__invoke->with('value')->returns(new ParsingFailure);

                    $test = ($this->parser)('value');

                    expect($test)->toEqual(new ParsedFactory($factory1->get()));

                });

            });

            context('when no parser returns a successfully parsed value', function () {

                it('should return a ParsingFailure', function () {

                    $this->parser1->__invoke->with('value')->returns(new ParsingFailure);
                    $this->parser2->__invoke->with('value')->returns(new ParsingFailure);
                    $this->parser3->__invoke->with('value')->returns(new ParsingFailure);
                    $this->parser4->__invoke->with('value')->returns(new ParsingFailure);
                    $this->parser5->__invoke->with('value')->returns(new ParsingFailure);

                    $test = ($this->parser)('value');

                    expect($test)->toEqual(new ParsingFailure);

                });

            });

        });

    });

});

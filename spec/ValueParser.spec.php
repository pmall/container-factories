<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\Parameter;
use Quanta\Container\ValueParser;
use Quanta\Container\FactoryArray;
use Quanta\Container\FactoryInterface;
use Quanta\Container\Parsing\ParsedFactory;
use Quanta\Container\Parsing\ParsingFailure;
use Quanta\Container\Parsing\StringParserInterface;

describe('ValueParser', function () {

    context('when there is no string parser', function () {

        beforeEach(function () {

            $this->parser = new ValueParser;

        });

        describe('->__invoke()', function () {

            context('when the given value is an array', function () {

                it('should return a factory array', function () {

                    $test = ($this->parser)(['value11', 'key12' => 'value12', 'value13', [
                        'value21', 'key22' => 'value22', 'value23']
                    ]);

                    expect($test)->toEqual(new FactoryArray([
                        new Parameter('value11'),
                        'key12' => new Parameter('value12'),
                        new Parameter('value13'),
                        new FactoryArray([
                            new Parameter('value21'),
                            'key22' => new Parameter('value22'),
                            new Parameter('value23'),
                        ])
                    ]));

                });

            });

            context('when the given value is not an array', function () {

                it('should return a Parameter', function () {

                    $test = ($this->parser)('value');

                    expect($test)->toEqual(new Parameter('value'));

                });

            });

        });

    });

    context('when there is at least one string parser', function () {

        beforeEach(function () {

            $this->delegate1 = mock(StringParserInterface::class);
            $this->delegate2 = mock(StringParserInterface::class);
            $this->delegate3 = mock(StringParserInterface::class);

            $this->parser = new ValueParser(
                $this->delegate1->get(),
                $this->delegate2->get(),
                $this->delegate3->get()
            );

        });

        describe('->__invoke()', function () {

            context('when the given value is an array', function () {

                it('should return a factory array', function () {

                    $factory1 = mock(FactoryInterface::class);
                    $factory2 = mock(FactoryInterface::class);
                    $factory3 = mock(FactoryInterface::class);
                    $factory4 = mock(FactoryInterface::class);

                    $this->delegate1->__invoke
                        ->with('value13')
                        ->returns(new ParsingFailure);

                    $this->delegate2->__invoke
                        ->with('value13')
                        ->returns(new ParsedFactory($factory1->get()));

                    $this->delegate3->__invoke
                        ->with('value13')
                        ->returns(new ParsedFactory($factory2->get()));

                    $this->delegate1->__invoke
                        ->with('value22')
                        ->returns(new ParsingFailure);

                    $this->delegate2->__invoke
                        ->with('value22')
                        ->returns(new ParsingFailure);

                    $this->delegate3->__invoke
                        ->with('value22')
                        ->returns(new ParsedFactory($factory4->get()));

                    $test = ($this->parser)([11, 'key12' => 12, 'value13', [
                        21, 'key22' => 'value22', 23]
                    ]);

                    expect($test)->toEqual(new FactoryArray([
                        new Parameter(11),
                        'key12' => new Parameter(12),
                        $factory1->get(),
                        new FactoryArray([
                            new Parameter(21),
                            'key22' => $factory4->get(),
                            new Parameter(23),
                        ])
                    ]));

                });

            });

            context('when the given value is not an array', function () {

                context('when the given value is a string', function () {

                    context('when at least one string parser successfully parse the given string', function () {

                        it('should return the first successfully parsed factory', function () {

                            $factory1 = mock(FactoryInterface::class);
                            $factory2 = mock(FactoryInterface::class);

                            $this->delegate1->__invoke
                                ->with('value')
                                ->returns(new ParsingFailure);

                            $this->delegate2->__invoke
                                ->with('value')
                                ->returns(new ParsedFactory($factory1->get()));

                            $this->delegate3->__invoke
                                ->with('value')
                                ->returns(new ParsedFactory($factory2->get()));

                            $test = ($this->parser)('value');

                            expect($test)->toBe($factory1->get());

                        });

                    });

                    context('when no string parser successfully parse the given string', function () {

                        it('should return a parameter', function () {

                            $this->delegate1->__invoke
                                ->with('value')
                                ->returns(new ParsingFailure);

                            $this->delegate2->__invoke
                                ->with('value')
                                ->returns(new ParsingFailure);

                            $this->delegate3->__invoke
                                ->with('value')
                                ->returns(new ParsingFailure);

                            $test = ($this->parser)('value');

                            expect($test)->toEqual(new Parameter('value'));

                        });

                    });

                });

                context('when the given value is not a string', function () {

                    it('should return a parameter', function () {

                        $test = ($this->parser)(1);

                        expect($test)->toEqual(new Parameter(1));

                    });

                });

            });

        });

    });

});

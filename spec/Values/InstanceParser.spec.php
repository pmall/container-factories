<?php

use Quanta\Container\Values\Value;
use Quanta\Container\Values\Instance;
use Quanta\Container\Values\ParsedValue;
use Quanta\Container\Values\ValueFactory;
use Quanta\Container\Values\InstanceParser;
use Quanta\Container\Values\ParsingFailure;
use Quanta\Container\Values\ValueParserInterface;

describe('InstanceParser', function () {

    beforeEach(function () {

        $this->parser = new InstanceParser;

    });

    it('should implement ValueParserInterface', function () {

        expect($this->parser)->toBeAnInstanceOf(ValueParserInterface::class);

    });

    describe('->__invoke()', function () {

        context('when the given value is not an array', function () {

            it('should return a ParsingFailure', function () {

                $test = ($this->parser)(new ValueFactory, 'value');

                expect($test)->toBeAnInstanceOf(ParsingFailure::class);

            });

        });

        context('when the given value is an array', function () {

            context('when the array has at least two elements', function () {

                context('when the array has no string key', function () {

                    context('when the first element of the array is \'new\'', function () {

                        context('when the second element is a string', function () {

                            context('when there is no more value in the array', function () {

                                it('should return a ParsedValue wrapped around an Instance', function () {

                                    $test = ($this->parser)(new ValueFactory, ['new', SomeClass::class]);

                                    expect($test)->toEqual(new ParsedValue(
                                        new Instance(SomeClass::class)
                                    ));

                                });

                            });

                            context('when there is more values in the array', function () {

                                it('should return a ParsedValue wrapped around an Instance with the extra values parsed as ValueInterface implementations using the factory', function () {

                                    $factory = ValueFactory::withDummyValueParser([
                                        'value1' => 'parsed1',
                                        'value2' => 'parsed2',
                                        'value3' => 'parsed3',
                                    ]);

                                    $test = ($this->parser)($factory, [
                                        'new',
                                        SomeClass::class,
                                        'value1',
                                        'value2',
                                        'value3',
                                    ]);

                                    expect($test)->toEqual(new ParsedValue(
                                        new Instance(...[
                                            SomeClass::class,
                                            new Value('parsed1'),
                                            new Value('parsed2'),
                                            new Value('parsed3'),
                                        ])
                                    ));

                                });

                            });

                        });

                        context('when the second element is not a string', function () {

                            it('should return a ParsingFailure', function () {

                                $test = ($this->parser)(new ValueFactory, ['new', 1]);

                                expect($test)->toBeAnInstanceOf(ParsingFailure::class);

                            });

                        });

                    });

                    context('when the first element of the array is not \'new\'', function () {

                        it('should return a ParsingFailure', function () {

                            $test = ($this->parser)(new ValueFactory, [
                                'value1',
                                'value2',
                            ]);

                            expect($test)->toBeAnInstanceOf(ParsingFailure::class);

                        });

                    });

                });

                context('when the array has string keys', function () {

                    it('should return a ParsingFailure', function () {

                        $test = ($this->parser)(new ValueFactory, [
                            'value1',
                            'k1' => 'value2',
                        ]);

                        expect($test)->toBeAnInstanceOf(ParsingFailure::class);

                    });

                });

            });

            context('when the array has less than two elements', function () {

                it('should return a ParsingFailure', function () {

                    $test = ($this->parser)(new ValueFactory, ['value']);

                    expect($test)->toBeAnInstanceOf(ParsingFailure::class);

                });

            });

        });

    });

});

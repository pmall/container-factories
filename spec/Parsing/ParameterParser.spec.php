<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\Alias;
use Quanta\Container\Parameter;
use Quanta\Container\ValueParser;
use Quanta\Container\Parsing\ParsedFactory;
use Quanta\Container\Parsing\ParsingFailure;
use Quanta\Container\Parsing\ParserInterface;
use Quanta\Container\Parsing\ParameterParser;
use Quanta\Container\Parsing\ParameterParserInterface;

describe('ParameterParser', function () {

    beforeEach(function () {

        $this->delegate = new ValueParser;

        $this->parser = new ParameterParser($this->delegate);

    });

    it('should implement ParameterParserInterface', function () {

        expect($this->parser)->toBeAnInstanceOf(ParameterParserInterface::class);

    });

    describe('->__invoke()', function () {

        beforeEach(function () {

            $this->options = [
                '$parameter1' => 'value1',
                '$parameter2' => 'value2',
                '$parameter3' => 'value3',
                Test\SomeClass1::class => 'value4',
                Test\SomeClass2::class => 'value5',
                Test\SomeClass3::class => 'value6',
            ];

            $this->parameter = mock(ReflectionParameter::class);

        });

        context('when the given parameter is not variadic', function () {

            beforeEach(function () {

                $this->parameter->isVariadic->returns(false);

            });

            context('when the given parameter name is in the option array', function () {

                it('should parse the associated value', function () {

                    $this->parameter->getName->returns('parameter2');

                    $test = ($this->parser)($this->parameter->get(), $this->options);

                    expect($test)->toEqual(new ParsedFactory(
                        ($this->delegate)('value2')
                    ));

                });

            });

            context('when the given parameter name is not in the option array', function () {

                context('when the given parameter is type hinted', function () {

                    beforeEach(function () {

                        $this->type = mock(ReflectionNamedType::class);

                        $this->parameter->getType->returns($this->type);

                    });

                    context('when the given parameter type hint is a class name', function () {

                        context('when the class name is in the option array', function () {

                            beforeEach(function () {

                                $this->type->getName->returns(Test\SomeClass2::class);

                            });

                            context('when the given parameter allows null', function () {

                                it('should parse the class alias as a non nullable alias', function () {

                                    $this->parameter->allowsNull->returns(true);

                                    $test = ($this->parser)($this->parameter->get(), $this->options);

                                    expect($test)->toEqual(new ParsedFactory(
                                        new Alias('value5', false)
                                    ));

                                });

                            });

                            context('when the given parameter does not allow null', function () {

                                it('should parse the class alias as a non nullable alias', function () {

                                    $this->parameter->allowsNull->returns(false);

                                    $test = ($this->parser)($this->parameter->get(), $this->options);

                                    expect($test)->toEqual(new ParsedFactory(
                                        new Alias('value5', false)
                                    ));

                                });

                            });

                        });

                        context('when the class name is not in the option array', function () {

                            beforeEach(function () {

                                $this->type->getName->returns(Test\SomeClass::class);

                            });

                            context('when the given parameter allows null', function () {

                                it('should parse the class name as a nullable alias', function () {

                                    $this->parameter->allowsNull->returns(true);

                                    $test = ($this->parser)($this->parameter->get(), $this->options);

                                    expect($test)->toEqual(new ParsedFactory(
                                        new Alias(Test\SomeClass::class, true)
                                    ));

                                });

                            });

                            context('when the given parameter does not allow null', function () {

                                it('should parse the class name as a non nullable alias', function () {

                                    $this->parameter->allowsNull->returns(false);

                                    $test = ($this->parser)($this->parameter->get(), $this->options);

                                    expect($test)->toEqual(new ParsedFactory(
                                        new Alias(Test\SomeClass::class, false)
                                    ));

                                });

                            });

                        });

                    });

                    context('when the given parameter type hint is not a class name', function () {

                        beforeEach(function () {

                            $this->type->isBuiltIn->returns(true);

                        });

                        context('when the given parameter has a default value', function () {

                            it('should parse the default value as a parameter', function () {

                                $this->parameter->isDefaultValueAvailable->returns(true);
                                $this->parameter->getDefaultValue->returns('default');

                                $test = ($this->parser)($this->parameter->get(), $this->options);

                                expect($test)->toEqual(new ParsedFactory(new Parameter('default')));

                            });

                        });

                        context('when the given parameter does not have a default value', function () {

                            beforeEach(function () {

                                $this->parameter->isDefaultValueAvailable->returns(false);

                            });

                            context('when the given parameter allows null', function () {

                                it('should parse null as a parameter', function () {

                                    $this->parameter->allowsNull->returns(true);

                                    $test = ($this->parser)($this->parameter->get(), $this->options);

                                    expect($test)->toEqual(new ParsedFactory(new Parameter(null)));

                                });

                            });

                            context('when the given parameter does not allow null', function () {

                                it('should return a parsing failure', function () {

                                    $this->parameter->allowsNull->returns(false);

                                    $test = ($this->parser)($this->parameter->get(), $this->options);

                                    expect($test)->toEqual(new ParsingFailure);

                                });

                            });

                        });

                    });

                });

                context('when the given parameter is not type hinted', function () {

                    beforeEach(function () {

                        $this->parameter->getType->returns(null);

                    });

                    context('when the given parameter has a default value', function () {

                        it('should parse the default value as a parameter', function () {

                            $this->parameter->isDefaultValueAvailable->returns(true);
                            $this->parameter->getDefaultValue->returns('default');

                            $test = ($this->parser)($this->parameter->get(), $this->options);

                            expect($test)->toEqual(new ParsedFactory(new Parameter('default')));

                        });

                    });

                    context('when the given parameter does not have a default value', function () {

                        beforeEach(function () {

                            $this->parameter->isDefaultValueAvailable->returns(false);

                        });

                        context('when the given parameter allows null', function () {

                            it('should parse null as a parameter', function () {

                                $this->parameter->allowsNull->returns(true);

                                $test = ($this->parser)($this->parameter->get(), $this->options);

                                expect($test)->toEqual(new ParsedFactory(new Parameter(null)));

                            });

                        });

                        context('when the given parameter does not allow null', function () {

                            it('should return a parsing failure', function () {

                                $this->parameter->allowsNull->returns(false);

                                $test = ($this->parser)($this->parameter->get(), $this->options);

                                expect($test)->toEqual(new ParsingFailure);

                            });

                        });

                    });

                });

            });

        });

        context('when the given parameter is variadic', function () {

            it('should return a parsing failure', function () {

                $this->parameter->isVariadic->returns(true);

                $test = ($this->parser)($this->parameter->get(), $this->options);

                expect($test)->toEqual(new ParsingFailure);

            });

        });

    });

});

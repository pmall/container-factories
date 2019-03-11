<?php

use Quanta\Container\Values\Value;
use Quanta\Container\Values\ArrayValue;
use Quanta\Container\Values\ValueFactory;
use Quanta\Container\Values\DummyValueParser;

require_once __DIR__ . '/../.test/classes.php';

describe('ValueFactory::withDefaultParser()', function () {

    it('should return a value factory with the default value parsers', function () {

        $test = ValueFactory::withDefaultParser();

        expect($test)->toEqual(new ValueFactory(...[
            new Quanta\Container\Values\EnvVarParser,
            new Quanta\Container\Values\ReferenceParser,
            new Quanta\Container\Values\InterpolatedStringParser,
        ]));

    });

});

describe('ValueFactory', function () {

    context('when there is no parser', function () {

        beforeEach(function () {

            $this->factory = new ValueFactory;

        });

        describe('->__invoke()', function () {

            context('when the given value is not an array', function () {

                it('should return a Value wrapped around the given value', function () {

                    $test = ($this->factory)('value');

                    expect($test)->toEqual(new Value('value'));

                });

            });

            context('when the given value is an array', function () {

                context('when the given array is a callable', function () {

                    it('should return a Value wrapped around the given callable', function () {

                        $test = ($this->factory)([Test\TestFactory::class, 'createStatic']);

                        expect($test)->toEqual(new Value([Test\TestFactory::class, 'createStatic']));

                    });

                });

                context('when the given array is not a callable', function () {

                    it('should return an ArrayValue', function () {

                        $test = ($this->factory)(['k1' => 'value1', 'value2', 'k3' => 'value3']);

                        expect($test)->toEqual(new ArrayValue([
                            'k1' => new Value('value1'),
                            new Value('value2'),
                            'k3' => new Value('value3'),
                        ]));

                    });

                });

            });

        });

    });

    context('when there is at least one parser', function () {

        beforeEach(function () {

            $this->factory = new ValueFactory(...[
                new DummyValueParser([
                    'value11' => 'parsed11', 'value12' => 'parsed12', 'value13' => 'parsed13',
                ]),
                new DummyValueParser([
                    'value21' => 'parsed21', 'value22' => 'parsed22', 'value23' => 'parsed23',
                ]),
                new DummyValueParser([
                    'value31' => 'parsed31', 'value32' => 'parsed32', 'value33' => 'parsed33',
                ]),
            ]);

        });

        describe('->__invoke()', function () {

            context('when at least one parser returns a successfully parsed value', function () {

                it('should return the value of the first successfully parsed value', function () {

                    $test = ($this->factory)('value22');

                    expect($test)->toEqual(new Value('parsed22'));

                });

            });

            context('when no parser returns a successfully parsed value', function () {

                context('when the given value is not an array', function () {

                    it('should return a Value wrapped around the given value', function () {

                        $test = ($this->factory)('value');

                        expect($test)->toEqual(new Value('value'));

                    });

                });

                context('when the given value is an array', function () {

                    context('when the given array is a callable', function () {

                        it('should return a Value wrapped around the given callable', function () {

                            $test = ($this->factory)([Test\TestFactory::class, 'createStatic']);

                            expect($test)->toEqual(new Value([Test\TestFactory::class, 'createStatic']));

                        });

                    });

                    context('when the given array is not a callable', function () {

                        it('should return an ArrayValue', function () {

                            $test = ($this->factory)([
                                'k1' => 'value11',
                                'value22',
                                'k3' => 'value33',
                                'value41',
                            ]);

                            expect($test)->toEqual(new ArrayValue([
                                'k1' => new Value('parsed11'),
                                new Value('parsed22'),
                                'k3' => new Value('parsed33'),
                                new Value('value41'),
                            ]));

                        });

                    });

                });

            });

        });

    });

});

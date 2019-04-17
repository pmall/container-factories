<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\DefinitionProxy;
use Quanta\Container\AutowiredInstance;
use Quanta\Container\Parsing\ParameterParserInterface;
use Quanta\Container\Configuration\ConfigurationUnit;
use Quanta\Container\Configuration\ConfigurationInterface;
use Quanta\Container\Configuration\AutowiringConfiguration;

describe('AutowiringConfiguration', function () {

    beforeEach(function () {

        $this->parser = mock(ParameterParserInterface::class);

    });

    context('when the class name collection is empty', function () {

        context('when there is no option array', function () {

            it('should use an empty option array', function () {

                $test = new AutowiringConfiguration($this->parser->get(), []);

                expect($test)->toEqual(new AutowiringConfiguration($this->parser->get(), [], []));

            });

        });

        context('when there is an option array', function () {

            context('when the class name collection is an array', function () {

                beforeEach(function () {

                    $this->configuration = new AutowiringConfiguration(
                        $this->parser->get(),
                        [],
                        []
                    );

                });

                it('should implement ConfigurationInterface', function () {

                    expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

                });

                describe('->unit()', function () {

                    it('should return a configuration unit with an empty autowired factory map', function () {

                        $test = $this->configuration->unit();

                        expect($test)->toEqual(new ConfigurationUnit([]));

                    });

                });

            });

            context('when the class name collection is an iterator', function () {

                beforeEach(function () {

                    $this->configuration = new AutowiringConfiguration(
                        $this->parser->get(),
                        new ArrayIterator([]),
                        []
                    );

                });

                it('should implement ConfigurationInterface', function () {

                    expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

                });

                describe('->unit()', function () {

                    it('should return a configuration unit with an empty autowired factory map', function () {

                        $test = $this->configuration->unit();

                        expect($test)->toEqual(new ConfigurationUnit([]));

                    });

                });

            });

            context('when the class name collection is a traversable', function () {

                beforeEach(function () {

                    $this->configuration = new AutowiringConfiguration(
                        $this->parser->get(),
                        new class implements IteratorAggregate {
                            public function getIterator() { yield from []; }
                        },
                        []
                    );

                });

                it('should implement ConfigurationInterface', function () {

                    expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

                });

                describe('->unit()', function () {

                    it('should return a configuration unit with an empty autowired factory map', function () {

                        $test = $this->configuration->unit();

                        expect($test)->toEqual(new ConfigurationUnit([]));

                    });

                });

            });

        });

    });

    context('when the class name collection is not empty', function () {

        beforeEach(function () {

            // ensure the iterable can return value of any type without failure.
            // only strings are used as class names.
            $this->classes = [
                true, 1, 1.1, [], new class {}, tmpfile(), null,
                Test\SomeClass1::class,
                Test\SomeClass2::class,
                Test\SomeClass3::class,
                Test\Ns1\SomeClass::class,
                Test\Ns2\SomeClass::class,
                Test\Ns3\SomeClass::class,
            ];

        });

        context('when there is no option array', function () {

            it('should use an empty option array', function () {

                $test = new AutowiringConfiguration($this->parser->get(), $this->classes);

                expect($test)->toEqual(new AutowiringConfiguration(
                    $this->parser->get(),
                    $this->classes,
                    []
                ));

            });

        });

        context('when there is an option array', function () {

            context('when the option array is empty', function () {

                context('when the class name collection is an array', function () {

                    beforeEach(function () {

                        $this->configuration = new AutowiringConfiguration(
                            $this->parser->get(),
                            $this->classes,
                            []
                        );

                    });

                    it('should implement ConfigurationInterface', function () {

                        expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

                    });

                    describe('->unit()', function () {

                        it('should return a configuration unit with an autowired factory map', function () {

                            $test = $this->configuration->unit();

                            expect($test)->toEqual(new ConfigurationUnit([
                                Test\SomeClass1::class => new DefinitionProxy(
                                    new AutowiredInstance($this->parser->get(), Test\SomeClass1::class)
                                ),
                                Test\SomeClass2::class => new DefinitionProxy(
                                    new AutowiredInstance($this->parser->get(), Test\SomeClass2::class)
                                ),
                                Test\SomeClass3::class => new DefinitionProxy(
                                    new AutowiredInstance($this->parser->get(), Test\SomeClass3::class)
                                ),
                                Test\Ns1\SomeClass::class => new DefinitionProxy(
                                    new AutowiredInstance($this->parser->get(), Test\Ns1\SomeClass::class)
                                ),
                                Test\Ns2\SomeClass::class => new DefinitionProxy(
                                    new AutowiredInstance($this->parser->get(), Test\Ns2\SomeClass::class)
                                ),
                                Test\Ns3\SomeClass::class => new DefinitionProxy(
                                    new AutowiredInstance($this->parser->get(), Test\Ns3\SomeClass::class)
                                ),
                            ]));

                        });

                    });

                });

                context('when the class name collection is an iterator', function () {

                    beforeEach(function () {

                        $this->configuration = new AutowiringConfiguration(
                            $this->parser->get(),
                            new ArrayIterator($this->classes),
                            []
                        );

                    });

                    it('should implement ConfigurationInterface', function () {

                        expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

                    });

                    describe('->unit()', function () {

                        it('should return a configuration unit with an autowired factory map', function () {

                            $test = $this->configuration->unit();

                            expect($test)->toEqual(new ConfigurationUnit([
                                Test\SomeClass1::class => new DefinitionProxy(
                                    new AutowiredInstance($this->parser->get(), Test\SomeClass1::class)
                                ),
                                Test\SomeClass2::class => new DefinitionProxy(
                                    new AutowiredInstance($this->parser->get(), Test\SomeClass2::class)
                                ),
                                Test\SomeClass3::class => new DefinitionProxy(
                                    new AutowiredInstance($this->parser->get(), Test\SomeClass3::class)
                                ),
                                Test\Ns1\SomeClass::class => new DefinitionProxy(
                                    new AutowiredInstance($this->parser->get(), Test\Ns1\SomeClass::class)
                                ),
                                Test\Ns2\SomeClass::class => new DefinitionProxy(
                                    new AutowiredInstance($this->parser->get(), Test\Ns2\SomeClass::class)
                                ),
                                Test\Ns3\SomeClass::class => new DefinitionProxy(
                                    new AutowiredInstance($this->parser->get(), Test\Ns3\SomeClass::class)
                                ),
                            ]));

                        });

                    });

                });

                context('when the class name collection is a traversable', function () {

                    beforeEach(function () {

                        $this->configuration = new AutowiringConfiguration(
                            $this->parser->get(),
                            new class ($this->classes) implements IteratorAggregate {
                                private $classes;
                                public function __construct($classes) {
                                    $this->classes = $classes;
                                }
                                public function getIterator() {
                                    yield from $this->classes;
                                }
                            },
                            []
                        );

                    });

                    it('should implement ConfigurationInterface', function () {

                        expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

                    });

                    describe('->unit()', function () {

                        it('should return a configuration unit with an autowired factory map', function () {

                            $test = $this->configuration->unit();

                            expect($test)->toEqual(new ConfigurationUnit([
                                Test\SomeClass1::class => new DefinitionProxy(
                                    new AutowiredInstance($this->parser->get(), Test\SomeClass1::class)
                                ),
                                Test\SomeClass2::class => new DefinitionProxy(
                                    new AutowiredInstance($this->parser->get(), Test\SomeClass2::class)
                                ),
                                Test\SomeClass3::class => new DefinitionProxy(
                                    new AutowiredInstance($this->parser->get(), Test\SomeClass3::class)
                                ),
                                Test\Ns1\SomeClass::class => new DefinitionProxy(
                                    new AutowiredInstance($this->parser->get(), Test\Ns1\SomeClass::class)
                                ),
                                Test\Ns2\SomeClass::class => new DefinitionProxy(
                                    new AutowiredInstance($this->parser->get(), Test\Ns2\SomeClass::class)
                                ),
                                Test\Ns3\SomeClass::class => new DefinitionProxy(
                                    new AutowiredInstance($this->parser->get(), Test\Ns3\SomeClass::class)
                                ),
                            ]));

                        });

                    });

                });

            });

            context('when the option array is not empty', function () {

                context('when all the values of the option array are arrays', function () {

                    beforeEach(function () {

                        $this->options = [
                            '*' => [
                                '$parameter1' => 'value11',
                            ],
                            Test\SomeClass2::class => [
                                '$parameter2' => 'value22',
                                '$parameter3' => 'value23',
                            ],
                            Test\SomeClass3::class => [
                                '$parameter1' => 'value31',
                                '$parameter2' => 'value32',
                                '$parameter3' => 'value33',
                            ],
                            'Test\\*\\SomeClass' => [
                                '$parameter1' => 'value41',
                            ],
                            Test\Ns2\SomeClass::class => [
                                '$parameter2' => 'value52',
                                '$parameter3' => 'value53',
                            ],
                            Test\Ns3\SomeClass::class => [
                                '$parameter1' => 'value61',
                                '$parameter2' => 'value62',
                                '$parameter3' => 'value63',
                            ],
                        ];

                    });

                    context('when the class name collection is an array', function () {

                        beforeEach(function () {

                            $this->configuration = new AutowiringConfiguration(
                                $this->parser->get(),
                                $this->classes,
                                $this->options
                            );

                        });

                        it('should implement ConfigurationInterface', function () {

                            expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

                        });

                        describe('->unit()', function () {

                            it('should return a configuration unit with an autowired factory map', function () {

                                $test = $this->configuration->unit();

                                expect($test)->toEqual(new ConfigurationUnit([
                                    Test\SomeClass1::class => new DefinitionProxy(
                                        new AutowiredInstance($this->parser->get(), Test\SomeClass1::class, [
                                            '$parameter1' => 'value11',
                                        ])
                                    ),
                                    Test\SomeClass2::class => new DefinitionProxy(
                                        new AutowiredInstance($this->parser->get(), Test\SomeClass2::class, [
                                            '$parameter1' => 'value11',
                                            '$parameter2' => 'value22',
                                            '$parameter3' => 'value23',
                                        ])
                                    ),
                                    Test\SomeClass3::class => new DefinitionProxy(
                                        new AutowiredInstance($this->parser->get(), Test\SomeClass3::class, [
                                            '$parameter1' => 'value31',
                                            '$parameter2' => 'value32',
                                            '$parameter3' => 'value33',
                                        ])
                                    ),
                                    Test\Ns1\SomeClass::class => new DefinitionProxy(
                                        new AutowiredInstance($this->parser->get(),Test\Ns1\SomeClass::class, [
                                            '$parameter1' => 'value41',
                                        ])
                                    ),
                                    Test\Ns2\SomeClass::class => new DefinitionProxy(
                                        new AutowiredInstance($this->parser->get(),Test\Ns2\SomeClass::class, [
                                            '$parameter1' => 'value41',
                                            '$parameter2' => 'value52',
                                            '$parameter3' => 'value53',
                                        ])
                                    ),
                                    Test\Ns3\SomeClass::class => new DefinitionProxy(
                                        new AutowiredInstance($this->parser->get(),Test\Ns3\SomeClass::class, [
                                            '$parameter1' => 'value61',
                                            '$parameter2' => 'value62',
                                            '$parameter3' => 'value63',
                                        ])
                                    ),
                                ]));

                            });

                        });

                    });

                    context('when the class name collection is an iterator', function () {

                        beforeEach(function () {

                            $this->configuration = new AutowiringConfiguration(
                                $this->parser->get(),
                                new ArrayIterator($this->classes),
                                $this->options
                            );

                        });

                        it('should implement ConfigurationInterface', function () {

                            expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

                        });

                        describe('->unit()', function () {

                            it('should return a configuration unit with an autowired factory map', function () {

                                $test = $this->configuration->unit();

                                expect($test)->toEqual(new ConfigurationUnit([
                                    Test\SomeClass1::class => new DefinitionProxy(
                                        new AutowiredInstance($this->parser->get(), Test\SomeClass1::class, [
                                            '$parameter1' => 'value11',
                                        ])
                                    ),
                                    Test\SomeClass2::class => new DefinitionProxy(
                                        new AutowiredInstance($this->parser->get(), Test\SomeClass2::class, [
                                            '$parameter1' => 'value11',
                                            '$parameter2' => 'value22',
                                            '$parameter3' => 'value23',
                                        ])
                                    ),
                                    Test\SomeClass3::class => new DefinitionProxy(
                                        new AutowiredInstance($this->parser->get(), Test\SomeClass3::class, [
                                            '$parameter1' => 'value31',
                                            '$parameter2' => 'value32',
                                            '$parameter3' => 'value33',
                                        ])
                                    ),
                                    Test\Ns1\SomeClass::class => new DefinitionProxy(
                                        new AutowiredInstance($this->parser->get(),Test\Ns1\SomeClass::class, [
                                            '$parameter1' => 'value41',
                                        ])
                                    ),
                                    Test\Ns2\SomeClass::class => new DefinitionProxy(
                                        new AutowiredInstance($this->parser->get(),Test\Ns2\SomeClass::class, [
                                            '$parameter1' => 'value41',
                                            '$parameter2' => 'value52',
                                            '$parameter3' => 'value53',
                                        ])
                                    ),
                                    Test\Ns3\SomeClass::class => new DefinitionProxy(
                                        new AutowiredInstance($this->parser->get(),Test\Ns3\SomeClass::class, [
                                            '$parameter1' => 'value61',
                                            '$parameter2' => 'value62',
                                            '$parameter3' => 'value63',
                                        ])
                                    ),
                                ]));

                            });

                        });

                    });

                    context('when the class name collection is a traversable', function () {

                        beforeEach(function () {

                            $this->configuration = new AutowiringConfiguration(
                                $this->parser->get(),
                                new class ($this->classes) implements IteratorAggregate {
                                    private $classes;
                                    public function __construct($classes) {
                                        $this->classes = $classes;
                                    }
                                    public function getIterator() {
                                        yield from $this->classes;
                                    }
                                },
                                $this->options
                            );

                        });

                        it('should implement ConfigurationInterface', function () {

                            expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

                        });

                        describe('->unit()', function () {

                            it('should return a configuration unit with an autowired factory map', function () {

                                $test = $this->configuration->unit();

                                expect($test)->toEqual(new ConfigurationUnit([
                                    Test\SomeClass1::class => new DefinitionProxy(
                                        new AutowiredInstance($this->parser->get(), Test\SomeClass1::class, [
                                            '$parameter1' => 'value11',
                                        ])
                                    ),
                                    Test\SomeClass2::class => new DefinitionProxy(
                                        new AutowiredInstance($this->parser->get(), Test\SomeClass2::class, [
                                            '$parameter1' => 'value11',
                                            '$parameter2' => 'value22',
                                            '$parameter3' => 'value23',
                                        ])
                                    ),
                                    Test\SomeClass3::class => new DefinitionProxy(
                                        new AutowiredInstance($this->parser->get(), Test\SomeClass3::class, [
                                            '$parameter1' => 'value31',
                                            '$parameter2' => 'value32',
                                            '$parameter3' => 'value33',
                                        ])
                                    ),
                                    Test\Ns1\SomeClass::class => new DefinitionProxy(
                                        new AutowiredInstance($this->parser->get(),Test\Ns1\SomeClass::class, [
                                            '$parameter1' => 'value41',
                                        ])
                                    ),
                                    Test\Ns2\SomeClass::class => new DefinitionProxy(
                                        new AutowiredInstance($this->parser->get(),Test\Ns2\SomeClass::class, [
                                            '$parameter1' => 'value41',
                                            '$parameter2' => 'value52',
                                            '$parameter3' => 'value53',
                                        ])
                                    ),
                                    Test\Ns3\SomeClass::class => new DefinitionProxy(
                                        new AutowiredInstance($this->parser->get(),Test\Ns3\SomeClass::class, [
                                            '$parameter1' => 'value61',
                                            '$parameter2' => 'value62',
                                            '$parameter3' => 'value63',
                                        ])
                                    ),
                                ]));

                            });

                        });

                    });

                });

                context('when a value of the option array is not an array', function () {

                    it('should throw an InvalidArgumentException', function () {

                        $test = function () {
                            new AutowiringConfiguration($this->parser->get(), $this->classes, [
                                Test\Ns1\SomeClass::class => [],
                                Test\Ns2\SomeClass::class => 1,
                                Test\Ns3\SomeClass::class => [],
                            ]);
                        };

                        expect($test)->toThrow(new InvalidArgumentException);

                    });

                });

            });

        });

    });

});

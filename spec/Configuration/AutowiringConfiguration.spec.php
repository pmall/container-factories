<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\DefinitionProxy;
use Quanta\Container\AutowiredInstance;
use Quanta\Container\Parsing\ParameterParser;
use Quanta\Container\Parsing\ParameterParserInterface;
use Quanta\Container\Parsing\CompositeParameterParser;
use Quanta\Container\Configuration\ConfigurationInterface;
use Quanta\Container\Configuration\AutowiringConfiguration;

describe('AutowiringConfiguration', function () {

    context('when the class name collection is empty', function () {

        context('when there is no option array', function () {

            it('should use an empty option array', function () {

                $test = new AutowiringConfiguration([]);

                expect($test)->toEqual(new AutowiringConfiguration([], []));

            });

        });

        context('when there is an option array', function () {

            context('when the class name collection is an array', function () {

                beforeEach(function () {

                    $this->configuration = new AutowiringConfiguration([], []);

                });

                it('should implement ConfigurationInterface', function () {

                    expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

                });

                describe('->factories()', function () {

                    it('should return a an empty array', function () {

                        $test = $this->configuration->factories();

                        expect($test)->toEqual([]);

                    });

                });

                describe('->mappers()', function () {

                    it('should return a an empty array', function () {

                        $test = $this->configuration->mappers();

                        expect($test)->toEqual([]);

                    });

                });

                describe('->extensions()', function () {

                    it('should return a an empty array', function () {

                        $test = $this->configuration->extensions();

                        expect($test)->toEqual([]);

                    });

                });

            });

            context('when the class name collection is an iterator', function () {

                beforeEach(function () {

                    $this->configuration = new AutowiringConfiguration(
                        new ArrayIterator([]),
                        []
                    );

                });

                it('should implement ConfigurationInterface', function () {

                    expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

                });

                describe('->factories()', function () {

                    it('should return a an empty array', function () {

                        $test = $this->configuration->factories();

                        expect($test)->toEqual([]);

                    });

                });

                describe('->mappers()', function () {

                    it('should return a an empty array', function () {

                        $test = $this->configuration->mappers();

                        expect($test)->toEqual([]);

                    });

                });

                describe('->extensions()', function () {

                    it('should return a an empty array', function () {

                        $test = $this->configuration->extensions();

                        expect($test)->toEqual([]);

                    });

                });

            });

            context('when the class name collection is a traversable', function () {

                beforeEach(function () {

                    $this->configuration = new AutowiringConfiguration(
                        new class implements IteratorAggregate {
                            public function getIterator() { yield from []; }
                        },
                        []
                    );

                });

                it('should implement ConfigurationInterface', function () {

                    expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

                });

                describe('->factories()', function () {

                    it('should return a an empty array', function () {

                        $test = $this->configuration->factories();

                        expect($test)->toEqual([]);

                    });

                });

                describe('->mappers()', function () {

                    it('should return a an empty array', function () {

                        $test = $this->configuration->mappers();

                        expect($test)->toEqual([]);

                    });

                });

                describe('->extensions()', function () {

                    it('should return a an empty array', function () {

                        $test = $this->configuration->extensions();

                        expect($test)->toEqual([]);

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

                $test = new AutowiringConfiguration($this->classes);

                expect($test)->toEqual(new AutowiringConfiguration(
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
                            $this->classes,
                            []
                        );

                    });

                    it('should implement ConfigurationInterface', function () {

                        expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

                    });

                    describe('->factories()', function () {

                        it('should return an autowired factory map', function () {

                            $test = $this->configuration->factories();

                            expect($test)->toEqual([
                                Test\SomeClass1::class => new DefinitionProxy(
                                    new AutowiredInstance(
                                        Test\SomeClass1::class,
                                        new ParameterParser
                                    )
                                ),
                                Test\SomeClass2::class => new DefinitionProxy(
                                    new AutowiredInstance(
                                        Test\SomeClass2::class,
                                        new ParameterParser
                                    )
                                ),
                                Test\SomeClass3::class => new DefinitionProxy(
                                    new AutowiredInstance(
                                        Test\SomeClass3::class,
                                        new ParameterParser
                                    )
                                ),
                                Test\Ns1\SomeClass::class => new DefinitionProxy(
                                    new AutowiredInstance(
                                        Test\Ns1\SomeClass::class,
                                        new ParameterParser
                                    )
                                ),
                                Test\Ns2\SomeClass::class => new DefinitionProxy(
                                    new AutowiredInstance(
                                        Test\Ns2\SomeClass::class,
                                        new ParameterParser
                                    )
                                ),
                                Test\Ns3\SomeClass::class => new DefinitionProxy(
                                    new AutowiredInstance(
                                        Test\Ns3\SomeClass::class,
                                        new ParameterParser
                                    )
                                ),
                            ]);

                        });

                    });

                    describe('->mappers()', function () {

                        it('should return a an empty array', function () {

                            $test = $this->configuration->mappers();

                            expect($test)->toEqual([]);

                        });

                    });

                    describe('->extensions()', function () {

                        it('should return a an empty array', function () {

                            $test = $this->configuration->extensions();

                            expect($test)->toEqual([]);

                        });

                    });

                });

                context('when the class name collection is an iterator', function () {

                    beforeEach(function () {

                        $this->configuration = new AutowiringConfiguration(
                            new ArrayIterator($this->classes),
                            []
                        );

                    });

                    it('should implement ConfigurationInterface', function () {

                        expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

                    });

                    describe('->factories()', function () {

                        it('should return an autowired factory map', function () {

                            $test = $this->configuration->factories();

                            expect($test)->toEqual([
                                Test\SomeClass1::class => new DefinitionProxy(
                                    new AutowiredInstance(
                                        Test\SomeClass1::class,
                                        new ParameterParser
                                    )
                                ),
                                Test\SomeClass2::class => new DefinitionProxy(
                                    new AutowiredInstance(
                                        Test\SomeClass2::class,
                                        new ParameterParser
                                    )
                                ),
                                Test\SomeClass3::class => new DefinitionProxy(
                                    new AutowiredInstance(
                                        Test\SomeClass3::class,
                                        new ParameterParser
                                    )
                                ),
                                Test\Ns1\SomeClass::class => new DefinitionProxy(
                                    new AutowiredInstance(
                                        Test\Ns1\SomeClass::class,
                                        new ParameterParser
                                    )
                                ),
                                Test\Ns2\SomeClass::class => new DefinitionProxy(
                                    new AutowiredInstance(
                                        Test\Ns2\SomeClass::class,
                                        new ParameterParser
                                    )
                                ),
                                Test\Ns3\SomeClass::class => new DefinitionProxy(
                                    new AutowiredInstance(
                                        Test\Ns3\SomeClass::class,
                                        new ParameterParser
                                    )
                                ),
                            ]);

                        });

                    });

                    describe('->mappers()', function () {

                        it('should return a an empty array', function () {

                            $test = $this->configuration->mappers();

                            expect($test)->toEqual([]);

                        });

                    });

                    describe('->extensions()', function () {

                        it('should return a an empty array', function () {

                            $test = $this->configuration->extensions();

                            expect($test)->toEqual([]);

                        });

                    });

                });

                context('when the class name collection is a traversable', function () {

                    beforeEach(function () {

                        $this->configuration = new AutowiringConfiguration(
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

                    describe('->factories()', function () {

                        it('should return an autowired factory map', function () {

                            $test = $this->configuration->factories();

                            expect($test)->toEqual([
                                Test\SomeClass1::class => new DefinitionProxy(
                                    new AutowiredInstance(
                                        Test\SomeClass1::class,
                                        new ParameterParser
                                    )
                                ),
                                Test\SomeClass2::class => new DefinitionProxy(
                                    new AutowiredInstance(
                                        Test\SomeClass2::class,
                                        new ParameterParser
                                    )
                                ),
                                Test\SomeClass3::class => new DefinitionProxy(
                                    new AutowiredInstance(
                                        Test\SomeClass3::class,
                                        new ParameterParser
                                    )
                                ),
                                Test\Ns1\SomeClass::class => new DefinitionProxy(
                                    new AutowiredInstance(
                                        Test\Ns1\SomeClass::class,
                                        new ParameterParser
                                    )
                                ),
                                Test\Ns2\SomeClass::class => new DefinitionProxy(
                                    new AutowiredInstance(
                                        Test\Ns2\SomeClass::class,
                                        new ParameterParser
                                    )
                                ),
                                Test\Ns3\SomeClass::class => new DefinitionProxy(
                                    new AutowiredInstance(
                                        Test\Ns3\SomeClass::class,
                                        new ParameterParser
                                    )
                                ),
                            ]);

                        });

                    });

                    describe('->mappers()', function () {

                        it('should return a an empty array', function () {

                            $test = $this->configuration->mappers();

                            expect($test)->toEqual([]);

                        });

                    });

                    describe('->extensions()', function () {

                        it('should return a an empty array', function () {

                            $test = $this->configuration->extensions();

                            expect($test)->toEqual([]);

                        });

                    });

                });

            });

            context('when the option array is not empty', function () {

                context('when all the values of the option array are arrays', function () {

                    beforeEach(function () {

                        $this->parser1 = mock(ParameterParserInterface::class);
                        $this->parser2 = mock(ParameterParserInterface::class);
                        $this->parser3 = mock(ParameterParserInterface::class);
                        $this->parser4 = mock(ParameterParserInterface::class);
                        $this->parser5 = mock(ParameterParserInterface::class);
                        $this->parser6 = mock(ParameterParserInterface::class);

                        $this->options = [
                            '*' => $this->parser1->get(),
                            Test\SomeClass2::class => $this->parser2->get(),
                            Test\SomeClass3::class => $this->parser3->get(),
                            'Test\\*\\SomeClass' => $this->parser4->get(),
                            Test\Ns2\SomeClass::class => $this->parser5->get(),
                            Test\Ns3\SomeClass::class => $this->parser6->get(),
                        ];

                    });

                    context('when the class name collection is an array', function () {

                        beforeEach(function () {

                            $this->configuration = new AutowiringConfiguration(
                                $this->classes,
                                $this->options
                            );

                        });

                        it('should implement ConfigurationInterface', function () {

                            expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

                        });

                        describe('->factories()', function () {

                            it('should return an autowired factory map', function () {

                                $test = $this->configuration->factories();

                                expect($test)->toEqual([
                                    Test\SomeClass1::class => new DefinitionProxy(
                                        new AutowiredInstance(
                                            Test\SomeClass1::class,
                                            new CompositeParameterParser(
                                                $this->parser1->get(),
                                                new ParameterParser
                                            )
                                        )
                                    ),
                                    Test\SomeClass2::class => new DefinitionProxy(
                                        new AutowiredInstance(
                                            Test\SomeClass2::class,
                                            new CompositeParameterParser(
                                                $this->parser2->get(),
                                                $this->parser1->get(),
                                                new ParameterParser
                                            )
                                        )
                                    ),
                                    Test\SomeClass3::class => new DefinitionProxy(
                                        new AutowiredInstance(
                                            Test\SomeClass3::class,
                                            new CompositeParameterParser(
                                                $this->parser3->get(),
                                                $this->parser1->get(),
                                                new ParameterParser
                                            )
                                        )
                                    ),
                                    Test\Ns1\SomeClass::class => new DefinitionProxy(
                                        new AutowiredInstance(
                                            Test\Ns1\SomeClass::class,
                                            new CompositeParameterParser(
                                                $this->parser4->get(),
                                                $this->parser1->get(),
                                                new ParameterParser
                                            ))
                                    ),
                                    Test\Ns2\SomeClass::class => new DefinitionProxy(
                                        new AutowiredInstance(
                                            Test\Ns2\SomeClass::class,
                                            new CompositeParameterParser(
                                                $this->parser5->get(),
                                                $this->parser4->get(),
                                                $this->parser1->get(),
                                                new ParameterParser
                                            )
                                        )
                                    ),
                                    Test\Ns3\SomeClass::class => new DefinitionProxy(
                                        new AutowiredInstance(
                                            Test\Ns3\SomeClass::class,
                                            new CompositeParameterParser(
                                                $this->parser6->get(),
                                                $this->parser4->get(),
                                                $this->parser1->get(),
                                                new ParameterParser
                                            )
                                        )
                                    ),
                                ]);

                            });

                        });

                        describe('->mappers()', function () {

                            it('should return a an empty array', function () {

                                $test = $this->configuration->mappers();

                                expect($test)->toEqual([]);

                            });

                        });

                        describe('->extensions()', function () {

                            it('should return a an empty array', function () {

                                $test = $this->configuration->extensions();

                                expect($test)->toEqual([]);

                            });

                        });

                    });

                    context('when the class name collection is an iterator', function () {

                        beforeEach(function () {

                            $this->configuration = new AutowiringConfiguration(
                                new ArrayIterator($this->classes),
                                $this->options
                            );

                        });

                        it('should implement ConfigurationInterface', function () {

                            expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

                        });

                        describe('->factories()', function () {

                            it('should return an autowired factory map', function () {

                                $test = $this->configuration->factories();

                                expect($test)->toEqual([
                                    Test\SomeClass1::class => new DefinitionProxy(
                                        new AutowiredInstance(
                                            Test\SomeClass1::class,
                                            new CompositeParameterParser(
                                                $this->parser1->get(),
                                                new ParameterParser
                                            )
                                        )
                                    ),
                                    Test\SomeClass2::class => new DefinitionProxy(
                                        new AutowiredInstance(
                                            Test\SomeClass2::class,
                                            new CompositeParameterParser(
                                                $this->parser2->get(),
                                                $this->parser1->get(),
                                                new ParameterParser
                                            )
                                        )
                                    ),
                                    Test\SomeClass3::class => new DefinitionProxy(
                                        new AutowiredInstance(
                                            Test\SomeClass3::class,
                                            new CompositeParameterParser(
                                                $this->parser3->get(),
                                                $this->parser1->get(),
                                                new ParameterParser
                                            )
                                        )
                                    ),
                                    Test\Ns1\SomeClass::class => new DefinitionProxy(
                                        new AutowiredInstance(
                                            Test\Ns1\SomeClass::class,
                                            new CompositeParameterParser(
                                                $this->parser4->get(),
                                                $this->parser1->get(),
                                                new ParameterParser
                                            ))
                                    ),
                                    Test\Ns2\SomeClass::class => new DefinitionProxy(
                                        new AutowiredInstance(
                                            Test\Ns2\SomeClass::class,
                                            new CompositeParameterParser(
                                                $this->parser5->get(),
                                                $this->parser4->get(),
                                                $this->parser1->get(),
                                                new ParameterParser
                                            )
                                        )
                                    ),
                                    Test\Ns3\SomeClass::class => new DefinitionProxy(
                                        new AutowiredInstance(
                                            Test\Ns3\SomeClass::class,
                                            new CompositeParameterParser(
                                                $this->parser6->get(),
                                                $this->parser4->get(),
                                                $this->parser1->get(),
                                                new ParameterParser
                                            )
                                        )
                                    ),
                                ]);

                            });

                        });

                        describe('->mappers()', function () {

                            it('should return a an empty array', function () {

                                $test = $this->configuration->mappers();

                                expect($test)->toEqual([]);

                            });

                        });

                        describe('->extensions()', function () {

                            it('should return a an empty array', function () {

                                $test = $this->configuration->extensions();

                                expect($test)->toEqual([]);

                            });

                        });

                    });

                    context('when the class name collection is a traversable', function () {

                        beforeEach(function () {

                            $this->configuration = new AutowiringConfiguration(
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

                        describe('->factories()', function () {

                            it('should return an autowired factory map', function () {

                                $test = $this->configuration->factories();

                                expect($test)->toEqual([
                                    Test\SomeClass1::class => new DefinitionProxy(
                                        new AutowiredInstance(
                                            Test\SomeClass1::class,
                                            new CompositeParameterParser(
                                                $this->parser1->get(),
                                                new ParameterParser
                                            )
                                        )
                                    ),
                                    Test\SomeClass2::class => new DefinitionProxy(
                                        new AutowiredInstance(
                                            Test\SomeClass2::class,
                                            new CompositeParameterParser(
                                                $this->parser2->get(),
                                                $this->parser1->get(),
                                                new ParameterParser
                                            )
                                        )
                                    ),
                                    Test\SomeClass3::class => new DefinitionProxy(
                                        new AutowiredInstance(
                                            Test\SomeClass3::class,
                                            new CompositeParameterParser(
                                                $this->parser3->get(),
                                                $this->parser1->get(),
                                                new ParameterParser
                                            )
                                        )
                                    ),
                                    Test\Ns1\SomeClass::class => new DefinitionProxy(
                                        new AutowiredInstance(
                                            Test\Ns1\SomeClass::class,
                                            new CompositeParameterParser(
                                                $this->parser4->get(),
                                                $this->parser1->get(),
                                                new ParameterParser
                                            ))
                                    ),
                                    Test\Ns2\SomeClass::class => new DefinitionProxy(
                                        new AutowiredInstance(
                                            Test\Ns2\SomeClass::class,
                                            new CompositeParameterParser(
                                                $this->parser5->get(),
                                                $this->parser4->get(),
                                                $this->parser1->get(),
                                                new ParameterParser
                                            )
                                        )
                                    ),
                                    Test\Ns3\SomeClass::class => new DefinitionProxy(
                                        new AutowiredInstance(
                                            Test\Ns3\SomeClass::class,
                                            new CompositeParameterParser(
                                                $this->parser6->get(),
                                                $this->parser4->get(),
                                                $this->parser1->get(),
                                                new ParameterParser
                                            )
                                        )
                                    ),
                                ]);

                            });

                        });

                        describe('->mappers()', function () {

                            it('should return a an empty array', function () {

                                $test = $this->configuration->mappers();

                                expect($test)->toEqual([]);

                            });

                        });

                        describe('->extensions()', function () {

                            it('should return a an empty array', function () {

                                $test = $this->configuration->extensions();

                                expect($test)->toEqual([]);

                            });

                        });

                    });

                });

                context('when a value of the option array is not an array', function () {

                    it('should throw an InvalidArgumentException', function () {

                        $test = function () {
                            new AutowiringConfiguration($this->classes, [
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

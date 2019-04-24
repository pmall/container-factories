<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\Alias;
use Quanta\Container\Invokable;
use Quanta\Container\ValueParser;
use Quanta\Container\Configuration\Tagging;
use Quanta\Container\Configuration\PhpFileConfiguration;
use Quanta\Container\Configuration\ConfigurationInterface;

require_once __DIR__ . '/../.test/classes.php';

describe('PhpFileConfiguration', function () {

    beforeEach(function () {

        $this->parser = new ValueParser;

        $this->path = __DIR__ . '/../.test/config.php';

        $this->configuration = new PhpFileConfiguration($this->parser, $this->path);

        if (file_exists($this->path)) unlink($this->path);

    });

    beforeEach(function () {

        $this->put = function ($value) {
            file_put_contents($this->path, sprintf('<?php return %s;', var_export($value, true)));
        };

    });

    afterEach(function () {

        if (file_exists($this->path)) unlink($this->path);

    });

    context('when the file contents is valid', function () {

        it('should implement ConfigurationInterface', function () {

            expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

        });

        describe('->factories()', function () {

            it('should cache the file contents', function () {

                $this->put([
                    'factories' => [
                        'id' => [Test\TestFactory::class, 'createStatic'],
                    ],
                    'tags' => [
                        'id' => ['id1', 'id2', 'id3'],
                    ],
                    'extensions' => [
                        'id' => [Test\TestFactory::class, 'createStatic'],
                    ],
                    'key' => 'extra keys are not a problem',
                ]);

                $this->configuration->factories();

                $this->put([]);

                $test1 = $this->configuration->factories();
                $test2 = $this->configuration->mappers();
                $test3 = $this->configuration->extensions();

                expect($test1)->toEqual(['id' => [Test\TestFactory::class, 'createStatic']]);
                expect($test2)->toEqual(['id' => new Tagging\Entries('id1', 'id2', 'id3')]);
                expect($test3)->toEqual(['id' => [[Test\TestFactory::class, 'createStatic']]]);

            });

            context('when there is no parameters, aliases, invokables and factories arrays', function () {

                it('should return an empty array', function () {

                    $this->put(['key' => 'extra keys are not a problem']);

                    $test = $this->configuration->factories();

                    expect($test)->toEqual([]);

                });

            });

            context('when there is parameters, aliases, invokables and factories arrays', function () {

                context('when the parameters, aliases, invokables and factories arrays are empty', function () {

                    it('should return an empty array', function () {

                        $this->put([
                            'parameters' => [],
                            'aliases' => [],
                            'invokables' => [],
                            'factories' => [],
                            'key' => 'extra keys are not a problem',
                        ]);

                        $test = $this->configuration->factories();

                        expect($test)->toEqual([]);

                    });

                });

                context('when the parameters, aliases, invokables and factories arrays are not empty', function () {

                    it('should return an empty array', function () {

                        $this->put([
                            'parameters' => [
                                'id1' => 'parameter1',
                                'id2' => 'parameter2',
                                'id3' => 'parameter3',
                            ],
                            'aliases' => [
                                'id2' => SomeClass1::class,
                                'id3' => SomeClass2::class,
                                'id4' => SomeClass3::class,
                            ],
                            'invokables' => [
                                'id3' => SomeClass1::class,
                                'id4' => SomeClass2::class,
                                'id5' => SomeClass3::class,
                            ],
                            'factories' => [
                                'id4' => [Test\TestFactory::class, 'createStatic1'],
                                'id5' => [Test\TestFactory::class, 'createStatic2'],
                                'id6' => [Test\TestFactory::class, 'createStatic3'],
                            ],
                            'key' => 'extra keys are not a problem',
                        ]);

                        $test = $this->configuration->factories();

                        expect($test)->toEqual([
                            'id1' => ($this->parser)('parameter1'),
                            'id2' => new Alias(SomeClass1::class),
                            'id3' => new Invokable(SomeClass1::class),
                            'id4' => [Test\TestFactory::class, 'createStatic1'],
                            'id5' => [Test\TestFactory::class, 'createStatic2'],
                            'id6' => [Test\TestFactory::class, 'createStatic3'],
                        ]);

                    });

                });

            });

        });

        describe('->mappers()', function () {

            it('should cache the file contents', function () {

                $this->put([
                    'factories' => [
                        'id' => [Test\TestFactory::class, 'createStatic'],
                    ],
                    'tags' => [
                        'id' => ['id1', 'id2', 'id3'],
                    ],
                    'extensions' => [
                        'id' => [Test\TestFactory::class, 'createStatic'],
                    ],
                    'key' => 'extra keys are not a problem',
                ]);

                $this->configuration->mappers();

                $this->put([]);

                $test1 = $this->configuration->factories();
                $test2 = $this->configuration->mappers();
                $test3 = $this->configuration->extensions();

                expect($test1)->toEqual(['id' => [Test\TestFactory::class, 'createStatic']]);
                expect($test2)->toEqual(['id' => new Tagging\Entries('id1', 'id2', 'id3')]);
                expect($test3)->toEqual(['id' => [[Test\TestFactory::class, 'createStatic']]]);

            });

            context('when there is no tags an mappers arrays', function () {

                it('should return an empty array', function () {

                    $this->put(['key' => 'extra keys are not a problem']);

                    $test = $this->configuration->mappers();

                    expect($test)->toEqual([]);

                });

            });

            context('when there is tags and mappers array', function () {

                context('when tags and mappers arrays are empty', function () {

                    it('should return an empty array', function () {

                        $this->put([
                            'tags' => [],
                            'mappers' => [],
                            'key' => 'extra keys are not a problem',
                        ]);

                        $test = $this->configuration->mappers();

                        expect($test)->toEqual([]);

                    });

                });

                context('when tags and mappers arrays are not empty', function () {

                    it('should return the merged array of mappers', function () {

                        $this->put([
                            'tags' => [
                                'id1' => ['id11', 'id12', 'id13'],
                                'id2' => ['id21', 'id22', 'id23'],
                                'id3' => ['id31', 'id32', 'id33'],
                            ],
                            'mappers' => [
                                'id2' => SomeClass1::class,
                                'id3' => SomeClass2::class,
                                'id4' => SomeClass3::class,
                            ],
                            'key' => 'extra keys are not a problem',
                        ]);

                        $test = $this->configuration->mappers();

                        expect($test)->toEqual([
                            'id1' => new Tagging\Entries('id11', 'id12', 'id13'),
                            'id2' => new Tagging\CompositeTagging(
                                new Tagging\Entries('id21', 'id22', 'id23'),
                                new Tagging\Implementations(SomeClass1::class)
                            ),
                            'id3' => new Tagging\CompositeTagging(
                                new Tagging\Entries('id31', 'id32', 'id33'),
                                new Tagging\Implementations(SomeClass2::class)
                            ),
                            'id4' => new Tagging\Implementations(SomeClass3::class),
                        ]);

                    });

                });

            });

        });

        describe('->extensions()', function () {

            it('should cache the file contents', function () {

                $this->put([
                    'factories' => [
                        'id' => [Test\TestFactory::class, 'createStatic'],
                    ],
                    'tags' => [
                        'id' => ['id1', 'id2', 'id3'],
                    ],
                    'extensions' => [
                        'id' => [Test\TestFactory::class, 'createStatic'],
                    ],
                    'key' => 'extra keys are not a problem',
                ]);

                $this->configuration->extensions();

                $this->put([]);

                $test1 = $this->configuration->factories();
                $test2 = $this->configuration->mappers();
                $test3 = $this->configuration->extensions();

                expect($test1)->toEqual(['id' => [Test\TestFactory::class, 'createStatic']]);
                expect($test2)->toEqual(['id' => new Tagging\Entries('id1', 'id2', 'id3')]);
                expect($test3)->toEqual(['id' => [[Test\TestFactory::class, 'createStatic']]]);

            });

            context('when there is no extensions array', function () {

                it('should return an empty array', function () {

                    $this->put(['key' => 'extra keys are not a problem']);

                    $test = $this->configuration->extensions();

                    expect($test)->toEqual([]);

                });

            });

            context('when there is an extensions array', function () {

                context('when the extensions array is empty', function () {

                    it('should return an empty array', function () {

                        $this->put([
                            'extensions' => [],
                            'key' => 'extra keys are not a problem',
                        ]);

                        $test = $this->configuration->extensions();

                        expect($test)->toEqual([]);

                    });

                });

                context('when the extensions array is not empty', function () {

                    it('should return the extensions array wrapped inside arrays', function () {

                        $this->put([
                            'extensions' => [
                                'id1' => [Test\TestFactory::class, 'createStatic1'],
                                'id2' => [Test\TestFactory::class, 'createStatic2'],
                                'id3' => [Test\TestFactory::class, 'createStatic3'],
                            ],
                            'key' => 'extra keys are not a problem',
                        ]);

                        $test = $this->configuration->extensions();

                        expect($test)->toEqual([
                            'id1' => [[Test\TestFactory::class, 'createStatic1']],
                            'id2' => [[Test\TestFactory::class, 'createStatic2']],
                            'id3' => [[Test\TestFactory::class, 'createStatic3']],
                        ]);

                    });

                });

            });

        });

    });

    context('when the file contents is not valid', function () {

        it('should implement ConfigurationInterface', function () {

            expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

        });

        describe('->factories()', function () {

            context('when the file does not return an array', function () {

                it('should throw an UnexpectedValueException', function () {

                    $this->put(1);

                    expect([$this->configuration, 'factories'])->toThrow(new UnexpectedValueException);

                });

            });

            context('when the file returns an array', function () {

                context('when the parameters key is not an array', function () {

                    it('should throw an UnexpectedValueException', function () {

                        $this->put(['parameters' => 1]);

                        expect([$this->configuration, 'factories'])->toThrow(new UnexpectedValueException);

                    });

                });

                context('when the aliases key is not an array', function () {

                    it('should throw an UnexpectedValueException', function () {

                        $this->put(['aliases' => 1]);

                        expect([$this->configuration, 'factories'])->toThrow(new UnexpectedValueException);

                    });

                });

                context('when a value of the aliases array is not a string', function () {

                    it('should throw an UnexpectedValueException', function () {

                        $this->put([
                            'aliases' => [
                                'alias1' => 'id1',
                                'alias2' => 1,
                                'alias3' => 'id3',
                            ],
                        ]);

                        expect([$this->configuration, 'factories'])->toThrow(new UnexpectedValueException);

                    });

                });

                context('when the invokables key is not an array', function () {

                    it('should throw an UnexpectedValueException', function () {

                        $this->put(['invokables' => 1]);

                        expect([$this->configuration, 'factories'])->toThrow(new UnexpectedValueException);

                    });

                });

                context('when a value of the invokables array is not a string', function () {

                    it('should throw an UnexpectedValueException', function () {

                        $this->put([
                            'invokables' => [
                                'id1' => SomeClass1::class,
                                'id2' => 1,
                                'id3' => SomeClass2::class,
                            ],
                        ]);

                        expect([$this->configuration, 'factories'])->toThrow(new UnexpectedValueException);

                    });

                });

                context('when the factories key is not an array', function () {

                    it('should throw an UnexpectedValueException', function () {

                        $this->put(['factories' => 1]);

                        expect([$this->configuration, 'factories'])->toThrow(new UnexpectedValueException);

                    });

                });

                context('when a value of the factories array is not a callable', function () {

                    it('should throw an UnexpectedValueException', function () {

                        $this->put([
                            'factories' => [
                                'id1' => [Test\TestFactory::class, 'createStatic1'],
                                'id2' => 1,
                                'id3' => [Test\TestFactory::class, 'createStatic3'],
                            ],
                        ]);

                        expect([$this->configuration, 'factories'])->toThrow(new UnexpectedValueException);

                    });

                });

            });

        });

        describe('->mappers()', function () {

            context('when the file does not return an array', function () {

                it('should throw an UnexpectedValueException', function () {

                    $this->put(1);

                    expect([$this->configuration, 'mappers'])->toThrow(new UnexpectedValueException);

                });

            });

            context('when the file returns an array', function () {

                context('when the tags key is not an array', function () {

                    it('should throw an UnexpectedValueException', function () {

                        $this->put(['tags' => 1]);

                        expect([$this->configuration, 'mappers'])->toThrow(new UnexpectedValueException);

                    });

                });

                context('when a value of the tags array is not an array', function () {

                    it('should throw an UnexpectedValueException', function () {

                        $this->put(['tags' => [
                            'id1' => ['id11', 'id12', 'id13'],
                            'id2' => 1,
                            'id3' => ['id31', 'id32', 'id33'],
                        ]]);

                        expect([$this->configuration, 'mappers'])->toThrow(new UnexpectedValueException);

                    });

                });

                context('when a value of the tags array is not an array of strings', function () {

                    it('should throw an UnexpectedValueException', function () {

                        $this->put(['tags' => [
                            'id1' => ['id11', 'id12', 'id13'],
                            'id2' => ['id21', 1, 'id23'],
                            'id3' => ['id31', 'id32', 'id33'],
                        ]]);

                        expect([$this->configuration, 'mappers'])->toThrow(new UnexpectedValueException);

                    });

                });

            });

            context('when the file returns an array', function () {

                context('when the mappers key is not an array', function () {

                    it('should throw an UnexpectedValueException', function () {

                        $this->put(['mappers' => 1]);

                        expect([$this->configuration, 'mappers'])->toThrow(new UnexpectedValueException);

                    });

                });

            });

            context('when a value of the mappers array is not a string', function () {

                it('should throw an UnexpectedValueException', function () {

                    $this->put(['mappers' => [
                        'id1' => SomeClass1::class,
                        'id2' => 1,
                        'id3' => SomeClass2::class,
                    ]]);

                    expect([$this->configuration, 'mappers'])->toThrow(new UnexpectedValueException);

                });

            });

        });

        describe('->extensions()', function () {

            context('when the file does not return an array', function () {

                it('should throw an UnexpectedValueException', function () {

                    $this->put(1);

                    expect([$this->configuration, 'extensions'])->toThrow(new UnexpectedValueException);

                });

            });

            context('when the file returns an array', function () {

                context('when the extensions key is not an array', function () {

                    it('should throw an UnexpectedValueException', function () {

                        $this->put(['extensions' => 1]);

                        expect([$this->configuration, 'extensions'])->toThrow(new UnexpectedValueException);

                    });

                });

                context('when a value of the extensions array is not a callable', function () {

                    it('should throw an UnexpectedValueException', function () {

                        $this->put([
                            'extensions' => [
                                'id1' => [Test\TestFactory::class, 'createStatic1'],
                                'id2' => 1,
                                'id3' => [Test\TestFactory::class, 'createStatic3'],
                            ],
                        ]);

                        expect([$this->configuration, 'extensions'])->toThrow(new UnexpectedValueException);

                    });

                });

            });

        });

    });

});

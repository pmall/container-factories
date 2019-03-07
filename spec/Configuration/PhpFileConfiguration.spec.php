<?php

use Quanta\Container\FactoryMap;
use Quanta\Container\TaggingPass;
use Quanta\Container\ExtensionPass;
use Quanta\Container\ProcessedFactoryMap;
use Quanta\Container\Tagging;
use Quanta\Container\Values\Value;
use Quanta\Container\Values\ValueFactory;
use Quanta\Container\Factories\Tag;
use Quanta\Container\Factories\Alias;
use Quanta\Container\Factories\Factory;
use Quanta\Container\Factories\Invokable;
use Quanta\Container\Factories\Extension;
use Quanta\Container\Configuration\PhpFileConfiguration;

require_once __DIR__ . '/../.test/classes.php';

describe('PhpFileConfiguration', function () {

    beforeEach(function () {

        $this->factory = ValueFactory::withDummyValueParser([]);

    });

    context('when the file does not return an array', function () {

        it('should throw an UnexpectedValueException', function () {

            $configuration = new PhpFileConfiguration($this->factory, ...[
                __DIR__ . '/../.test/config/not_array.php'
            ]);

            expect([$configuration, 'map'])->toThrow(new UnexpectedValueException);

        });

    });

    context('when the file returns an array', function () {

        describe('when the configuration is valid', function () {

            it('should return a ProcessedFactoryMap', function () {

                $configuration = new PhpFileConfiguration($this->factory, ...[
                    __DIR__ . '/../.test/config/valid.php'
                ]);

                $test = $configuration->map();

                expect($test)->toEqual(
                    new ProcessedFactoryMap(
                        new FactoryMap([
                            'id01' => new Factory(new Value('parameter1')),
                            'id02' => new Alias('alias1'),
                            'id03' => new Invokable(Test\TestInvokable::class),
                            'id04' => new Test\TestFactory('factory1'),
                            'id05' => new Test\TestFactory('factory2'),
                        ]), ...[
                            new TaggingPass('id05', new Tagging\Entries('tag11', 'tag12', 'tag13')),
                            new TaggingPass('id06', new Tagging\Entries('tag21', 'tag22', 'tag23')),
                            new TaggingPass('id07', new Tagging\Implementations(Test\SomeInterface1::class)),
                            new TaggingPass('id08', new Tagging\Implementations(Test\SomeInterface2::class)),
                            new ExtensionPass('id09', new Test\TestFactory('extension1')),
                            new ExtensionPass('id10', new Test\TestFactory('extension2')),
                            new Test\TestProcessingPass('pass1'),
                            new Test\TestProcessingPass('pass2'),
                            new Test\TestProcessingPass('pass3'),
                        ]
                    )
                );

            });

        });

        describe('when the parameters key is not an array', function () {

            it('should throw an UnexpectedValueException', function () {

                $configuration = new PhpFileConfiguration($this->factory, ...[
                    __DIR__ . '/../.test/config/entries/parameters/not_array.php'
                ]);

                expect([$configuration, 'map'])->toThrow(new UnexpectedValueException);

            });

        });

        describe('when the aliases key is not an array', function () {

            it('should throw an UnexpectedValueException', function () {

                $configuration = new PhpFileConfiguration($this->factory, ...[
                    __DIR__ . '/../.test/config/entries/aliases/not_array.php'
                ]);

                expect([$configuration, 'map'])->toThrow(new UnexpectedValueException);

            });

        });

        describe('when a value of the array of aliases is not a string', function () {

            it('should throw an UnexpectedValueException', function () {

                $configuration = new PhpFileConfiguration($this->factory, ...[
                    __DIR__ . '/../.test/config/entries/aliases/not_array_of_strings.php'
                ]);

                expect([$configuration, 'map'])->toThrow(new UnexpectedValueException);

            });

        });

        describe('when the invokables key is not an array', function () {

            it('should throw an UnexpectedValueException', function () {

                $configuration = new PhpFileConfiguration($this->factory, ...[
                    __DIR__ . '/../.test/config/entries/invokables/not_array.php'
                ]);

                expect([$configuration, 'map'])->toThrow(new UnexpectedValueException);

            });

        });

        describe('when a value of the array of invokables is not a string', function () {

            it('should throw an UnexpectedValueException', function () {

                $configuration = new PhpFileConfiguration($this->factory, ...[
                    __DIR__ . '/../.test/config/entries/invokables/not_array_of_strings.php'
                ]);

                expect([$configuration, 'map'])->toThrow(new UnexpectedValueException);

            });

        });

        describe('when the factories key is not an array', function () {

            it('should throw an UnexpectedValueException', function () {

                $configuration = new PhpFileConfiguration($this->factory, ...[
                    __DIR__ . '/../.test/config/entries/factories/not_array.php'
                ]);

                expect([$configuration, 'map'])->toThrow(new UnexpectedValueException);

            });

        });

        describe('when a value of the array of factories is not a callable', function () {

            it('should throw an UnexpectedValueException', function () {

                $configuration = new PhpFileConfiguration($this->factory, ...[
                    __DIR__ . '/../.test/config/entries/factories/not_array_of_callables.php'
                ]);

                expect([$configuration, 'map'])->toThrow(new UnexpectedValueException);

            });

        });

        describe('when the tags key is not an array', function () {

            it('should throw an UnexpectedValueException', function () {

                $configuration = new PhpFileConfiguration($this->factory, ...[
                    __DIR__ . '/../.test/config/entries/tags/not_array.php'
                ]);

                expect([$configuration, 'map'])->toThrow(new UnexpectedValueException);

            });

        });

        describe('when a value of the array of tags is not an array', function () {

            it('should throw an UnexpectedValueException', function () {

                $configuration = new PhpFileConfiguration($this->factory, ...[
                    __DIR__ . '/../.test/config/entries/tags/not_array_of_arrays.php'
                ]);

                expect([$configuration, 'map'])->toThrow(new UnexpectedValueException);

            });

        });

        describe('when a value of the array of tags is not an array of strings', function () {

            it('should throw an UnexpectedValueException', function () {

                $configuration = new PhpFileConfiguration($this->factory, ...[
                    __DIR__ . '/../.test/config/entries/tags/not_array_of_arrays_of_strings.php'
                ]);

                expect([$configuration, 'map'])->toThrow(new UnexpectedValueException);

            });

        });

        describe('when the mappers key is not an array', function () {

            it('should throw an UnexpectedValueException', function () {

                $configuration = new PhpFileConfiguration($this->factory, ...[
                    __DIR__ . '/../.test/config/entries/mappers/not_array.php'
                ]);

                expect([$configuration, 'map'])->toThrow(new UnexpectedValueException);

            });

        });

        describe('when a value of the array of mappers is not a string', function () {

            it('should throw an UnexpectedValueException', function () {

                $configuration = new PhpFileConfiguration($this->factory, ...[
                    __DIR__ . '/../.test/config/entries/mappers/not_array_of_strings.php'
                ]);

                expect([$configuration, 'map'])->toThrow(new UnexpectedValueException);

            });

        });

        describe('when the extensions key is not an array', function () {

            it('should throw an UnexpectedValueException', function () {

                $configuration = new PhpFileConfiguration($this->factory, ...[
                    __DIR__ . '/../.test/config/entries/extensions/not_array.php'
                ]);

                expect([$configuration, 'map'])->toThrow(new UnexpectedValueException);

            });

        });

        describe('when a value of the array of extensions is not a callable', function () {

            it('should throw an UnexpectedValueException', function () {

                $configuration = new PhpFileConfiguration($this->factory, ...[
                    __DIR__ . '/../.test/config/entries/extensions/not_array_of_callables.php'
                ]);

                expect([$configuration, 'map'])->toThrow(new UnexpectedValueException);

            });

        });

        describe('when the passes key is not an array', function () {

            it('should throw an UnexpectedValueException', function () {

                $configuration = new PhpFileConfiguration($this->factory, ...[
                    __DIR__ . '/../.test/config/entries/passes/not_array.php'
                ]);

                expect([$configuration, 'map'])->toThrow(new UnexpectedValueException);

            });

        });

        describe('when a value of the array of passes is not an implementation of ProcessingPassInterface', function () {

            it('should throw an UnexpectedValueException', function () {

                $configuration = new PhpFileConfiguration($this->factory, ...[
                    __DIR__ . '/../.test/config/entries/passes/not_array_of_passes.php'
                ]);

                expect([$configuration, 'map'])->toThrow(new UnexpectedValueException);

            });

        });

    });

});

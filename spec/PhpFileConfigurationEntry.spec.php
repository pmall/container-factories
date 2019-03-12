<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\Configuration;
use Quanta\Container\PhpFileConfigurationEntry;
use Quanta\Container\ConfigurationEntryInterface;
use Quanta\Container\Maps\FactoryMap;
use Quanta\Container\Values\Value;
use Quanta\Container\Values\ValueFactory;
use Quanta\Container\Values\DummyValueParser;
use Quanta\Container\Passes\Tagging;
use Quanta\Container\Passes\TaggingPass;
use Quanta\Container\Passes\ExtensionPass;
use Quanta\Container\Passes\MergedProcessingPass;
use Quanta\Container\Factories\Tag;
use Quanta\Container\Factories\Alias;
use Quanta\Container\Factories\Factory;
use Quanta\Container\Factories\Invokable;
use Quanta\Container\Factories\Extension;

require_once __DIR__ . '/.test/classes.php';

describe('PhpFileConfigurationEntry::instance()', function () {

    it('should return a new PhpFileConfigurationEntry with the given value factory and path', function () {

        $factory = new ValueFactory;

        $test = PhpFileConfigurationEntry::instance($factory, 'path');

        expect($test)->toEqual(new PhpFileConfigurationEntry($factory, 'path'));

    });

});

describe('PhpFileConfigurationEntry', function () {

    beforeEach(function () {

        $this->factory = new ValueFactory(
            new DummyValueParser([
                'parameter1' => 'parsed1',
                'parameter2' => 'parsed2',
            ])
        );

        $this->path = tempnam(sys_get_temp_dir(), 'quanta');

        $this->entry = new PhpFileConfigurationEntry($this->factory, $this->path);

        file_put_contents($this->path, '');

    });

    it('should implement ConfigurationEntryInterface', function () {

        expect($this->entry)->toBeAnInstanceOf(ConfigurationEntryInterface::class);

    });

    describe('->configuration()', function () {

        context('when the file does not return an array', function () {

            it('should throw an UnexpectedValueException', function () {

                copy(__DIR__ . '/.test/config/not_array.php', $this->path);

                expect([$this->entry, 'configuration'])->toThrow(new UnexpectedValueException);

            });

        });

        context('when the file returns an array', function () {

            context('when the configuration is valid', function () {

                it('should return a Configuration', function () {

                    copy(__DIR__ . '/.test/config/valid.php', $this->path);

                    $test = $this->entry->configuration();

                    expect($test)->toEqual(
                        new Configuration(
                            new FactoryMap([
                                'id1' => new Factory(new Value('parsed1')),
                                'id2' => new Alias('alias1'),
                                'id3' => new Invokable(Test\TestInvokable::class),
                                'id4' => new Test\TestFactory('factory1'),
                                'id5' => new Test\TestFactory('factory2'),
                            ]),
                            new MergedProcessingPass(
                                new TaggingPass('id5', new Tagging\Entries('tag11', 'tag12', 'tag13')),
                                new TaggingPass('id6', new Tagging\Entries('tag21', 'tag22', 'tag23')),
                                new TaggingPass('id6', new Tagging\Implementations(Test\SomeInterface1::class)),
                                new TaggingPass('id7', new Tagging\Implementations(Test\SomeInterface2::class)),
                                new ExtensionPass('id7', new Test\TestFactory('extension1')),
                                new ExtensionPass('id8', new Test\TestFactory('extension2')),
                                new Test\TestProcessingPass('pass1'),
                                new Test\TestProcessingPass('pass2'),
                                new Test\TestProcessingPass('pass3')
                            )
                        )
                    );

                });

            });

            context('when the parameters key is not an array', function () {

                it('should throw an UnexpectedValueException', function () {

                    copy(__DIR__ . '/.test/config/parameters/not_array.php', $this->path);

                    expect([$this->entry, 'configuration'])->toThrow(new UnexpectedValueException);

                });

            });

            context('when the aliases key is not an array', function () {

                it('should throw an UnexpectedValueException', function () {

                    copy(__DIR__ . '/.test/config/aliases/not_array.php', $this->path);

                    expect([$this->entry, 'configuration'])->toThrow(new UnexpectedValueException);

                });

            });

            context('when a value of the array of aliases is not a string', function () {

                it('should throw an UnexpectedValueException', function () {

                    copy(__DIR__ . '/.test/config/aliases/not_array_of_strings.php', $this->path);

                    expect([$this->entry, 'configuration'])->toThrow(new UnexpectedValueException);

                });

            });

            context('when the invokables key is not an array', function () {

                it('should throw an UnexpectedValueException', function () {

                    copy(__DIR__ . '/.test/config/invokables/not_array.php', $this->path);

                    expect([$this->entry, 'configuration'])->toThrow(new UnexpectedValueException);

                });

            });

            context('when a value of the array of invokables is not a string', function () {

                it('should throw an UnexpectedValueException', function () {

                    copy(__DIR__ . '/.test/config/invokables/not_array_of_strings.php', $this->path);

                    expect([$this->entry, 'configuration'])->toThrow(new UnexpectedValueException);

                });

            });

            context('when the factories key is not an array', function () {

                it('should throw an UnexpectedValueException', function () {

                    copy(__DIR__ . '/.test/config/factories/not_array.php', $this->path);

                    expect([$this->entry, 'configuration'])->toThrow(new UnexpectedValueException);

                });

            });

            context('when a value of the array of factories is not a callable', function () {

                it('should throw an UnexpectedValueException', function () {

                    copy(__DIR__ . '/.test/config/factories/not_array_of_callables.php', $this->path);

                    expect([$this->entry, 'configuration'])->toThrow(new UnexpectedValueException);

                });

            });

            context('when the tags key is not an array', function () {

                it('should throw an UnexpectedValueException', function () {

                    copy(__DIR__ . '/.test/config/tags/not_array.php', $this->path);

                    expect([$this->entry, 'configuration'])->toThrow(new UnexpectedValueException);

                });

            });

            context('when a value of the array of tags is not an array', function () {

                it('should throw an UnexpectedValueException', function () {

                    copy(__DIR__ . '/.test/config/tags/not_array_of_arrays.php', $this->path);

                    expect([$this->entry, 'configuration'])->toThrow(new UnexpectedValueException);

                });

            });

            context('when a value of the array of tags is not an array of strings', function () {

                it('should throw an UnexpectedValueException', function () {

                    copy(__DIR__ . '/.test/config/tags/not_array_of_arrays_of_strings.php', $this->path);

                    expect([$this->entry, 'configuration'])->toThrow(new UnexpectedValueException);

                });

            });

            context('when the mappers key is not an array', function () {

                it('should throw an UnexpectedValueException', function () {

                    copy(__DIR__ . '/.test/config/mappers/not_array.php', $this->path);

                    expect([$this->entry, 'configuration'])->toThrow(new UnexpectedValueException);

                });

            });

            context('when a value of the array of mappers is not a string', function () {

                it('should throw an UnexpectedValueException', function () {

                    copy(__DIR__ . '/.test/config/mappers/not_array_of_strings.php', $this->path);

                    expect([$this->entry, 'configuration'])->toThrow(new UnexpectedValueException);

                });

            });

            context('when the extensions key is not an array', function () {

                it('should throw an UnexpectedValueException', function () {

                    copy(__DIR__ . '/.test/config/extensions/not_array.php', $this->path);

                    expect([$this->entry, 'configuration'])->toThrow(new UnexpectedValueException);

                });

            });

            context('when a value of the array of extensions is not a callable', function () {

                it('should throw an UnexpectedValueException', function () {

                    copy(__DIR__ . '/.test/config/extensions/not_array_of_callables.php', $this->path);

                    expect([$this->entry, 'configuration'])->toThrow(new UnexpectedValueException);

                });

            });

            context('when the passes key is not an array', function () {

                it('should throw an UnexpectedValueException', function () {

                    copy(__DIR__ . '/.test/config/passes/not_array.php', $this->path);

                    expect([$this->entry, 'configuration'])->toThrow(new UnexpectedValueException);

                });

            });

            context('when a value of the array of passes is not an implementation of ProcessingPassInterface', function () {

                it('should throw an UnexpectedValueException', function () {

                    copy(__DIR__ . '/.test/config/passes/not_array_of_passes.php', $this->path);

                    expect([$this->entry, 'configuration'])->toThrow(new UnexpectedValueException);

                });

            });

        });

    });

});

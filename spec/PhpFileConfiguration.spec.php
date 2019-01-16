<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\PhpFileConfiguration;
use Quanta\Container\ConfigurationInterface;
use Quanta\Container\ConfigurationEntryInterface;

use Quanta\Container\Factories\Tag;
use Quanta\Container\Factories\Alias;
use Quanta\Container\Factories\Parameter;
use Quanta\Container\Factories\Extension;

use Quanta\Container\Values\Value;
use Quanta\Container\Values\ValueFactory;
use Quanta\Container\Values\EnvVarParser;
use Quanta\Container\Values\InstanceParser;
use Quanta\Container\Values\ValueFactoryInterface;
use Quanta\Container\Values\InterpolatedStringParser;

use Quanta\Container\Passes\ReverseTagging;
use Quanta\Container\Passes\ConfigurationPassInterface;

require_once __DIR__ . '/.test/classes.php';

describe('PhpFileConfiguration::withDefaultValueParser()', function () {

    it('should return a php file configuration using the default value parsers', function () {

        $patterns = ['pattern1', 'pattern2', 'pattern3'];

        $test = PhpFileConfiguration::withDefaultValueParser(...$patterns);

        expect($test)->toEqual(new PhpFileConfiguration(
            ValueFactory::withDefaultValueParser(),
            ...$patterns
        ));

    });

});

describe('PhpFileConfiguration', function () {

    beforeEach(function () {

        $this->factory = mock(ValueFactoryInterface::class);

    });

    context('when all the files are valid', function () {

        beforeEach(function () {

            $this->configuration = new PhpFileConfiguration($this->factory->get(), ...[
                __DIR__ . '/.test/config/valid/*.php',
                __DIR__ . '/.test/config/valid/only/parameters.php',
                __DIR__ . '/.test/config/valid/only/aliases.php',
                __DIR__ . '/.test/config/valid/only/factories.php',
                __DIR__ . '/.test/config/valid/only/extensions.php',
                __DIR__ . '/.test/config/valid/only/tags.php',
                __DIR__ . '/.test/config/valid/only/metadata.php',
                __DIR__ . '/.test/config/valid/only/passes.php',
                __DIR__ . '/.test/config/valid/only/mappers.php',
            ]);

        });

        it('should implement ConfigurationInterface', function () {

            expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

        });

        describe('->entries()', function () {

            it('should return one service provider per php file', function () {

                $this->factory->__invoke->does(function (string $value) {
                    return new Value($value);
                });

                $test = $this->configuration->entries();

                expect($test)->toBeAn('array');
                expect($test)->toHaveLength(10);

                // 0 is valid_full1.php
                expect($test[0])->toBeAnInstanceOf(ConfigurationEntryInterface::class);
                expect($test[0]->factories()->factories())->toEqual([
                    'id1' => new Parameter(new Value('parameter11')),
                    'id2' => new Alias('alias11'),
                    'id3' => new Test\TestFactory('factory11'),
                    'id4' => new Test\TestFactory('factory12'),
                ]);
                expect($test[0]->extensions()->factories())->toEqual([
                    'id4' => new Test\TestFactory('extension11'),
                    'id5' => new Tag(['tag111', 'tag112']),
                    'id6' => new Tag(['tag121', 'tag122']),
                ]);
                expect($test[0]->metadata())->toEqual([
                    'id1' => ['k111' => 'm111', 'k112' => 'm112'],
                    'id2' => ['k121' => 'm111', 'k122' => 'm122'],
                ]);
                expect($test[0]->passes())->toEqual([
                    new Test\TestConfigurationPass('pass11'),
                    new Test\TestConfigurationPass('pass12'),
                    new ReverseTagging('mapper11', Test\SomeInterface1::class),
                    new ReverseTagging('mapper12', Test\SomeInterface2::class),
                ]);

                // 1 is valid_full2.php
                expect($test[1])->toBeAnInstanceOf(ConfigurationEntryInterface::class);
                expect($test[1]->factories()->factories())->toEqual([
                    'id1' => new Parameter(new Value('parameter21')),
                    'id2' => new Alias('alias21'),
                    'id3' => new Test\TestFactory('factory21'),
                    'id4' => new Test\TestFactory('factory22'),
                ]);
                expect($test[1]->extensions()->factories())->toEqual([
                    'id4' => new Test\TestFactory('extension21'),
                    'id5' => new Tag(['tag211', 'tag212']),
                    'id6' => new Tag(['tag221', 'tag222']),
                ]);
                expect($test[1]->metadata())->toEqual([
                    'id1' => ['k211' => 'm211', 'k212' => 'm212'],
                    'id2' => ['k221' => 'm211', 'k222' => 'm222'],
                ]);
                expect($test[1]->passes())->toEqual([
                    new Test\TestConfigurationPass('pass21'),
                    new Test\TestConfigurationPass('pass22'),
                    new ReverseTagging('mapper21', Test\SomeInterface1::class),
                    new ReverseTagging('mapper22', Test\SomeInterface2::class),
                ]);

                // 2 is valid_only_parameters.php
                expect($test[2])->toBeAnInstanceOf(ConfigurationEntryInterface::class);
                expect($test[2]->factories()->factories())->toEqual([
                    'id1' => new Parameter(new Value('parameter31')),
                    'id2' => new Parameter(new Value('parameter32')),
                ]);
                expect($test[2]->extensions()->factories())->toEqual([]);
                expect($test[2]->metadata())->toEqual([]);
                expect($test[2]->passes())->toEqual([]);

                // 3 is valid_only_aliases.php
                expect($test[3])->toBeAnInstanceOf(ConfigurationEntryInterface::class);
                expect($test[3]->factories()->factories())->toEqual([
                    'id1' => new Alias('alias31'),
                    'id2' => new Alias('alias32'),
                ]);
                expect($test[3]->extensions()->factories())->toEqual([]);
                expect($test[3]->metadata())->toEqual([]);
                expect($test[3]->passes())->toEqual([]);

                // 4 is valid_only_factories.php
                expect($test[4])->toBeAnInstanceOf(ConfigurationEntryInterface::class);
                expect($test[4]->factories()->factories())->toEqual([
                    'id1' => new Test\TestFactory('factory31'),
                    'id2' => new Test\TestFactory('factory32'),
                ]);
                expect($test[4]->extensions()->factories())->toEqual([]);
                expect($test[4]->metadata())->toEqual([]);
                expect($test[4]->passes())->toEqual([]);

                // 5 is valid_only_extensions.php
                expect($test[5])->toBeAnInstanceOf(ConfigurationEntryInterface::class);
                expect($test[5]->factories()->factories())->toEqual([]);
                expect($test[5]->extensions()->factories())->toEqual([
                    'id1' => new Test\TestFactory('extension31'),
                    'id2' => new Test\TestFactory('extension32'),
                ]);
                expect($test[5]->metadata())->toEqual([]);
                expect($test[5]->passes())->toEqual([]);

                // 6 is valid_only_tags.php
                expect($test[6])->toBeAnInstanceOf(ConfigurationEntryInterface::class);
                expect($test[6]->factories()->factories())->toEqual([]);
                expect($test[6]->extensions()->factories())->toEqual([
                    'id1' => new Tag(['tag311', 'tag312']),
                    'id2' => new Tag(['tag321', 'tag322']),
                ]);
                expect($test[6]->metadata())->toEqual([]);
                expect($test[6]->passes())->toEqual([]);

                // 7 is valid_only_metadata.php
                expect($test[7])->toBeAnInstanceOf(ConfigurationEntryInterface::class);
                expect($test[7]->factories()->factories())->toEqual([]);
                expect($test[7]->extensions()->factories())->toEqual([]);
                expect($test[7]->metadata())->toEqual([
                    'id1' => ['k311' => 'm311', 'k312' => 'm312'],
                    'id2' => ['k321' => 'm311', 'k322' => 'm322'],
                ]);
                expect($test[7]->passes())->toEqual([]);

                // 8 is valid_only_passes.php
                expect($test[8])->toBeAnInstanceOf(ConfigurationEntryInterface::class);
                expect($test[8]->factories()->factories())->toEqual([]);
                expect($test[8]->extensions()->factories())->toEqual([]);
                expect($test[8]->metadata())->toEqual([]);
                expect($test[8]->passes())->toEqual([
                    new Test\TestConfigurationPass('pass31'),
                    new Test\TestConfigurationPass('pass32'),
                ]);

                // 9 is valid_only_mappers.php
                expect($test[9])->toBeAnInstanceOf(ConfigurationEntryInterface::class);
                expect($test[9]->factories()->factories())->toEqual([]);
                expect($test[9]->extensions()->factories())->toEqual([]);
                expect($test[9]->metadata())->toEqual([]);
                expect($test[9]->passes())->toEqual([
                    new ReverseTagging('mapper31', Test\SomeInterface1::class),
                    new ReverseTagging('mapper32', Test\SomeInterface2::class),
                ]);

            });

        });

    });

    context('when a file is not valid', function () {

        beforeEach(function () {

            $this->test = function (string $file, string $exception, string ...$strs) {
                $configuration = new PhpFileConfiguration($this->factory->get(), ...[
                    __DIR__ . '/.test/config/valid/full1.php',
                    $file,
                    __DIR__ . '/.test/config/valid/full2.php',
                ]);

                try { $configuration->entries(); }

                catch (Throwable $e) {
                    $test = $e;
                }

                expect(get_class($e))->toEqual($exception);
                expect($e->getMessage())->toContain($file);

                foreach ($strs as $str) {
                    expect($e->getMessage())->toContain($str);
                }
            };

        });

        context('when a value returned by a file is not an array', function () {

            describe('->entries()', function () {

                it('should throw an UnexpectedValueException containing array', function () {

                    $file = __DIR__ . '/.test/config/invalid/not_array.php';

                    $this->test($file, UnexpectedValueException::class, 'array');

                });

            });

        });

        context('when all the values returned by the files are arrays', function () {

            context('when the parameters returned by a file is not an array', function () {

                describe('->entries()', function () {

                    it('should throw an UnexpectedValueException containing array and \'parameters\'', function () {

                        $file = __DIR__ . '/.test/config/invalid/parameters.php';

                        $this->test($file, UnexpectedValueException::class, 'array', '\'parameters\'');

                    });

                });

            });

            context('when the aliases returned by a file is not an array', function () {

                describe('->entries()', function () {

                    it('should throw an UnexpectedValueException containing array and \'aliases\'', function () {

                        $file = __DIR__ . '/.test/config/invalid/aliases.php';

                        $this->test($file, UnexpectedValueException::class, 'array', '\'aliases\'');

                    });

                });

            });

            context('when the aliases returned by a file are not only strings', function () {

                describe('->entries()', function () {

                    it('should throw an UnexpectedValueException containing string and \'aliases\'', function () {

                        $file = __DIR__ . '/.test/config/invalid/entry/aliases.php';

                        $this->test($file, UnexpectedValueException::class, 'string', '\'aliases\'');

                    });

                });

            });

            context('when the factories returned by a file is not an array', function () {

                describe('->entries()', function () {

                    it('should throw an UnexpectedValueException containing array and \'factories\'', function () {

                        $file = __DIR__ . '/.test/config/invalid/factories.php';

                        $this->test($file, UnexpectedValueException::class, 'array', '\'factories\'');

                    });

                });

            });

            context('when the factories returned by a file are not only callables', function () {

                describe('->entries()', function () {

                    it('should throw an UnexpectedValueException containing callable and \'factories\'', function () {

                        $file = __DIR__ . '/.test/config/invalid/entry/factories.php';

                        $this->test($file, UnexpectedValueException::class, 'callable', '\'factories\'');

                    });

                });

            });

            context('when the extensions returned by a file is not an array', function () {

                describe('->entries()', function () {

                    it('should throw an UnexpectedValueException containing array and \'extensions\'', function () {

                        $file = __DIR__ . '/.test/config/invalid/extensions.php';

                        $this->test($file, UnexpectedValueException::class, 'array', '\'extensions\'');

                    });

                });

            });

            context('when the extensions returned by a file are not only callables', function () {

                describe('->entries()', function () {

                    it('should throw an UnexpectedValueException containing callable and \'extensions\'', function () {

                        $file = __DIR__ . '/.test/config/invalid/entry/extensions.php';

                        $this->test($file, UnexpectedValueException::class, 'callable', '\'extensions\'');

                    });

                });

            });

            context('when the tags returned by a file is not an array', function () {

                describe('->entries()', function () {

                    it('should throw an UnexpectedValueException containing array and \'tags\'', function () {

                        $file = __DIR__ . '/.test/config/invalid/tags.php';

                        $this->test($file, UnexpectedValueException::class, 'array', '\'tags\'');

                    });

                });

            });

            context('when the tags returned by a file are not only arrays', function () {

                describe('->entries()', function () {

                    it('should throw an UnexpectedValueException containing array and \'tags\'', function () {

                        $file = __DIR__ . '/.test/config/invalid/entry/tags.php';

                        $this->test($file, UnexpectedValueException::class, 'array', '\'tags\'');

                    });

                });

            });

            context('when a taged alias returned by a file is not a string', function () {

                describe('->entries()', function () {

                    it('should throw an UnexpectedValueException containing the tag name and string', function () {

                        $file = __DIR__ . '/.test/config/invalid/entry/tags_alias.php';

                        $this->test($file, UnexpectedValueException::class, '\'id2\'', 'string');

                    });

                });

            });

            context('when the metadata returned by a file is not an array', function () {

                describe('->entries()', function () {

                    it('should throw an UnexpectedValueException containing array and \'metadata\'', function () {

                        $file = __DIR__ . '/.test/config/invalid/metadata.php';

                        $this->test($file, UnexpectedValueException::class, 'array', '\'metadata\'');

                    });

                });

            });

            context('when the metadata returned by a file are not only arrays', function () {

                describe('->entries()', function () {

                    it('should throw an UnexpectedValueException containing array and \'metadata\'', function () {

                        $file = __DIR__ . '/.test/config/invalid/entry/metadata.php';

                        $this->test($file, UnexpectedValueException::class, 'array', '\'metadata\'');

                    });

                });

            });

            context('when the passes returned by a file is not an array', function () {

                describe('->entries()', function () {

                    it('should throw an UnexpectedValueException containing array and \'passes\'', function () {

                        $file = __DIR__ . '/.test/config/invalid/passes.php';

                        $this->test($file, UnexpectedValueException::class, 'array', '\'passes\'');

                    });

                });

            });

            context('when the passes returned by a file are not only configuration pass implementations', function () {

                describe('->entries()', function () {

                    it('should throw an UnexpectedValueException containing ConfigurationPassInterface and \'passes\'', function () {

                        $file = __DIR__ . '/.test/config/invalid/entry/passes.php';

                        $this->test($file, UnexpectedValueException::class, ConfigurationPassInterface::class, '\'passes\'');

                    });

                });

            });

            context('when the mappers returned by a file is not an array', function () {

                describe('->entries()', function () {

                    it('should throw an UnexpectedValueException containing array and \'mappers\'', function () {

                        $file = __DIR__ . '/.test/config/invalid/mappers.php';

                        $this->test($file, UnexpectedValueException::class, 'array', '\'mappers\'');

                    });

                });

            });

            context('when the mappers returned by a file are not only strings', function () {

                describe('->entries()', function () {

                    it('should throw an UnexpectedValueException containing string and \'mappers\'', function () {

                        $file = __DIR__ . '/.test/config/invalid/entry/mappers.php';

                        $this->test($file, UnexpectedValueException::class, 'string', '\'mappers\'');

                    });

                });

            });

        });

    });

});

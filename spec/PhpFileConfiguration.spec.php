<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\PhpFileConfiguration;
use Quanta\Container\ConfigurationInterface;
use Quanta\Container\TaggedServiceProviderInterface;

use Quanta\Container\FactoryMap;
use Quanta\Container\Factories\Tag;
use Quanta\Container\Factories\Alias;
use Quanta\Container\Factories\Parameter;
use Quanta\Container\Factories\Extension;

use Quanta\Container\Values\Value;
use Quanta\Container\Values\ValueFactoryInterface;

require_once __DIR__ . '/.test/classes.php';

describe('PhpFileConfiguration', function () {

    beforeEach(function () {

        $this->factory = mock(ValueFactoryInterface::class);

    });

    context('when all the files are valid', function () {

        beforeEach(function () {

            $this->configuration = new PhpFileConfiguration($this->factory->get(), ...[
                __DIR__ . '/.test/config/valid_*.php',
            ]);

        });

        it('should implement ConfigurationInterface', function () {

            expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

        });

        describe('->providers()', function () {

            it('should return one service provider per php file', function () {

                $this->factory->__invoke->does(function (string $value) {
                    return new Value($value);
                });

                $test = $this->configuration->providers();

                expect($test)->toBeAn('array');
                expect($test)->toHaveLength(7);

                // 0 is valid_full1.php
                expect($test[0])->toBeAnInstanceOf(TaggedServiceProviderInterface::class);
                expect($test[0]->factories())->toEqual(new FactoryMap([
                    'id1' => new Parameter(new Value('parameter11')),
                    'id2' => new Alias('alias11'),
                    'id3' => new Test\TestFactory('factory11'),
                    'id4' => new Test\TestFactory('factory12'),
                ]));
                expect($test[0]->extensions())->toEqual(new FactoryMap([
                    'id4' => new Test\TestFactory('extension11'),
                    'id5' => new Test\TestFactory('extension12'),
                    'id6' => new Tag('alias121', 'alias122'),
                ]));

                // 1 is valid_full2.php
                expect($test[1])->toBeAnInstanceOf(TaggedServiceProviderInterface::class);
                expect($test[1]->factories())->toEqual(new FactoryMap([
                    'id1' => new Parameter(new Value('parameter21')),
                    'id2' => new Alias('alias21'),
                    'id3' => new Test\TestFactory('factory21'),
                    'id4' => new Test\TestFactory('factory22'),
                ]));
                expect($test[1]->extensions())->toEqual(new FactoryMap([
                    'id4' => new Test\TestFactory('extension21'),
                    'id5' => new Test\TestFactory('extension22'),
                    'id6' => new Tag('alias221', 'alias222'),
                ]));

                // 2 is valid_only_aliases.php
                expect($test[2])->toBeAnInstanceOf(TaggedServiceProviderInterface::class);
                expect($test[2]->factories())->toEqual(new FactoryMap([
                    'id1' => new Alias('alias31'),
                    'id2' => new Alias('alias32'),
                ]));
                expect($test[2]->extensions())->toEqual(new FactoryMap([]));

                // 3 is valid_only_extensions.php
                expect($test[3])->toBeAnInstanceOf(TaggedServiceProviderInterface::class);
                expect($test[3]->factories())->toEqual(new FactoryMap([]));
                expect($test[3]->extensions())->toEqual(new FactoryMap([
                    'id1' => new Test\TestFactory('extension31'),
                    'id2' => new Test\TestFactory('extension32'),
                ]));

                // 4 is valid_only_factories.php
                expect($test[4])->toBeAnInstanceOf(TaggedServiceProviderInterface::class);
                expect($test[4]->factories())->toEqual(new FactoryMap([
                    'id1' => new Test\TestFactory('factory31'),
                    'id2' => new Test\TestFactory('factory32'),
                ]));
                expect($test[4]->extensions())->toEqual(new FactoryMap([]));

                // 5 is valid_only_parameters.php
                expect($test[5])->toBeAnInstanceOf(TaggedServiceProviderInterface::class);
                expect($test[5]->factories())->toEqual(new FactoryMap([
                    'id1' => new Parameter(new Value('parameter31')),
                    'id2' => new Parameter(new Value('parameter32')),
                ]));
                expect($test[5]->extensions())->toEqual(new FactoryMap([]));

                // 6 is valid_only_tags.php
                expect($test[6])->toBeAnInstanceOf(TaggedServiceProviderInterface::class);
                expect($test[6]->factories())->toEqual(new FactoryMap([]));
                expect($test[6]->extensions())->toEqual(new FactoryMap([
                    'id1' => new Tag('alias311', 'alias312'),
                    'id2' => new Tag('alias321', 'alias322'),
                ]));

            });

        });

    });

    context('when a value returned by a file is not an array', function () {

        beforeEach(function () {

            $this->configuration = new PhpFileConfiguration($this->factory->get(), ...[
                __DIR__ . '/.test/config/valid_full1.php',
                __DIR__ . '/.test/config/invalid_not_array.php',
                __DIR__ . '/.test/config/valid_full2.php',
            ]);

        });

        it('should implement ConfigurationInterface', function () {

            expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

        });

        describe('->providers()', function () {

            it('should throw an UnexpectedValueException containing the file path', function () {

                try { $this->configuration->providers(); }

                catch (UnexpectedValueException $e) {
                    $test = $e->getMessage();
                }

                expect($test)->toContain(__DIR__ . '/.test/config/invalid_not_array.php');

            });

        });

    });

    context('when all the values returned by the files are arrays', function () {

        context('when a value of an array returned by a file is not an array', function () {

            beforeEach(function () {

                $this->configuration = new PhpFileConfiguration($this->factory->get(), ...[
                    __DIR__ . '/.test/config/valid_full1.php',
                    __DIR__ . '/.test/config/invalid_not_containing_only_array.php',
                    __DIR__ . '/.test/config/valid_full2.php',
                ]);

            });

            it('should implement ConfigurationInterface', function () {

                expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

            });

            describe('->providers()', function () {

                it('should throw an UnexpectedValueException containing the file path', function () {

                    try { $this->configuration->providers(); }

                    catch (UnexpectedValueException $e) {
                        $test = $e->getMessage();
                    }

                    expect($test)->toContain(__DIR__ . '/.test/config/invalid_not_containing_only_array.php');

                });

            });

        });

        context('when all the values of the arrays returned by the files are arrays', function () {

            context('when the \'aliases\' key of an array returned by a file does not contain only strings', function () {

                beforeEach(function () {

                    $this->configuration = new PhpFileConfiguration($this->factory->get(), ...[
                        __DIR__ . '/.test/config/valid_full1.php',
                        __DIR__ . '/.test/config/invalid_aliases.php',
                        __DIR__ . '/.test/config/valid_full2.php',
                    ]);

                });

                it('should implement ConfigurationInterface', function () {

                    expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

                });

                describe('->providers()', function () {

                    it('should throw an UnexpectedValueException containing aliases and the file path', function () {

                        try { $this->configuration->providers(); }

                        catch (UnexpectedValueException $e) {
                            $test = $e->getMessage();
                        }

                        expect($test)->toContain('aliases');
                        expect($test)->toContain(__DIR__ . '/.test/config/invalid_aliases.php');

                    });

                });

            });

            context('when the \'factories\' key of an array returned by a file does not contain only callables', function () {

                beforeEach(function () {

                    $this->configuration = new PhpFileConfiguration($this->factory->get(), ...[
                        __DIR__ . '/.test/config/valid_full1.php',
                        __DIR__ . '/.test/config/invalid_factories.php',
                        __DIR__ . '/.test/config/valid_full2.php',
                    ]);

                });

                it('should implement ConfigurationInterface', function () {

                    expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

                });

                describe('->providers()', function () {

                    it('should throw an UnexpectedValueException containing factories and the file path', function () {

                        try { $this->configuration->providers(); }

                        catch (UnexpectedValueException $e) {
                            $test = $e->getMessage();
                        }

                        expect($test)->toContain('factories');
                        expect($test)->toContain(__DIR__ . '/.test/config/invalid_factories.php');

                    });

                });

            });

            context('when the \'extensions\' key of an array returned by a file does not contain only callables', function () {

                beforeEach(function () {

                    $this->configuration = new PhpFileConfiguration($this->factory->get(), ...[
                        __DIR__ . '/.test/config/valid_full1.php',
                        __DIR__ . '/.test/config/invalid_extensions.php',
                        __DIR__ . '/.test/config/valid_full2.php',
                    ]);

                });

                it('should implement ConfigurationInterface', function () {

                    expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

                });

                describe('->providers()', function () {

                    it('should throw an UnexpectedValueException containing extensions and the file path', function () {

                        try { $this->configuration->providers(); }

                        catch (UnexpectedValueException $e) {
                            $test = $e->getMessage();
                        }

                        expect($test)->toContain('extensions');
                        expect($test)->toContain(__DIR__ . '/.test/config/invalid_extensions.php');

                    });

                });

            });

            context('when the \'tags\' key of an array returned by a file does not contain only arrays', function () {

                beforeEach(function () {

                    $this->configuration = new PhpFileConfiguration($this->factory->get(), ...[
                        __DIR__ . '/.test/config/valid_full1.php',
                        __DIR__ . '/.test/config/invalid_tags_not_array.php',
                        __DIR__ . '/.test/config/valid_full2.php',
                    ]);

                });

                it('should implement ConfigurationInterface', function () {

                    expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

                });

                describe('->providers()', function () {

                    it('should throw an UnexpectedValueException containing tags and the file path', function () {

                        try { $this->configuration->providers(); }

                        catch (UnexpectedValueException $e) {
                            $test = $e->getMessage();
                        }

                        expect($test)->toContain('tags');
                        expect($test)->toContain(__DIR__ . '/.test/config/invalid_tags_not_array.php');

                    });

                });

            });

            context('when a value associated with a tag is not an array', function () {

                beforeEach(function () {

                    $this->configuration = new PhpFileConfiguration($this->factory->get(), ...[
                        __DIR__ . '/.test/config/valid_full1.php',
                        __DIR__ . '/.test/config/invalid_tags_not_string.php',
                        __DIR__ . '/.test/config/valid_full2.php',
                    ]);

                });

                describe('->providers()', function () {

                    it('should throw an UnexpectedValueException containing the tag name and the file path', function () {

                        try { $this->configuration->providers(); }

                        catch (UnexpectedValueException $e) {
                            $test = $e->getMessage();
                        }

                        expect($test)->toContain('id2');
                        expect($test)->toContain(__DIR__ . '/.test/config/invalid_tags_not_string.php');

                    });

                });

            });

        });

    });

});

<?php

use Quanta\Container\MergedConfigurationEntry;
use Quanta\Container\PhpFileConfigurationEntry;
use Quanta\Container\PhpFileConfigurationSource;
use Quanta\Container\ConfigurationSourceInterface;
use Quanta\Container\Values\ValueFactory;

describe('PhpFileConfigurationSource', function () {

    beforeEach(function () {

        $this->factory = new ValueFactory;

    });

    context('when there is no pattern', function () {

        beforeEach(function () {

            $this->source = new PhpFileConfigurationSource($this->factory);

        });

        it('should implement ConfigurationSourceInterface', function () {

            expect($this->source)->toBeAnInstanceOf(ConfigurationSourceInterface::class);

        });

        describe('->entry()', function () {

            it('should return an empty MergedConfigurationEntry', function () {

                $test = $this->source->entry();

                expect($test)->toEqual(new MergedConfigurationEntry);

            });

        });

    });

    context('when there is at least one pattern', function () {

        beforeEach(function () {

            $this->source = new PhpFileConfigurationSource($this->factory, ...[
                __DIR__ . '/.test/config/*.php',
                __DIR__ . '/.test/config/factories/*.php',
                __DIR__ . '/.test/config/extensions/*.php',
            ]);

        });

        it('should implement ConfigurationSourceInterface', function () {

            expect($this->source)->toBeAnInstanceOf(ConfigurationSourceInterface::class);

        });

        describe('->entry()', function () {

            it('should create php file configuration entries from the files matched by the patterns and merge them', function () {

                $test = $this->source->entry();

                expect($test)->toEqual(
                    new MergedConfigurationEntry(...[
                        new PhpFileConfigurationEntry($this->factory, ...[
                            __DIR__ . '/.test/config/not_array.php',
                        ]),
                        new PhpFileConfigurationEntry($this->factory, ...[
                            __DIR__ . '/.test/config/valid.php',
                        ]),
                        new PhpFileConfigurationEntry($this->factory, ...[
                            __DIR__ . '/.test/config/factories/not_array.php',
                        ]),
                        new PhpFileConfigurationEntry($this->factory, ...[
                            __DIR__ . '/.test/config/factories/not_array_of_callables.php',
                        ]),
                        new PhpFileConfigurationEntry($this->factory, ...[
                            __DIR__ . '/.test/config/extensions/not_array.php',
                        ]),
                        new PhpFileConfigurationEntry($this->factory, ...[
                            __DIR__ . '/.test/config/extensions/not_array_of_callables.php',
                        ]),
                    ])
                );

            });

        });

    });

});

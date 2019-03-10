<?php

use Quanta\Container\MergedConfiguration;
use Quanta\Container\PhpFileConfiguration;
use Quanta\Container\PhpFileConfigurationSource;
use Quanta\Container\ConfigurationSourceInterface;
use Quanta\Container\Values\ValueFactory;

describe('PhpFileConfigurationSource', function () {

    beforeEach(function () {

        $this->factory = ValueFactory::withDummyValueParser([]);

    });

    context('when there is no pattern', function () {

        beforeEach(function () {

            $this->source = new PhpFileConfigurationSource($this->factory);

        });

        it('should implement ConfigurationSourceInterface', function () {

            expect($this->source)->toBeAnInstanceOf(ConfigurationSourceInterface::class);

        });

        describe('->configuration()', function () {

            it('should return an empty MergedConfiguration', function () {

                $test = $this->source->configuration();

                expect($test)->toEqual(new MergedConfiguration);

            });

        });

    });

    context('when there is at least one pattern', function () {

        beforeEach(function () {

            $this->source = new PhpFileConfigurationSource($this->factory, ...[
                __DIR__ . '/.test/config/*.php',
                __DIR__ . '/.test/config/factories/*.php',
            ]);

        });

        it('should implement ConfigurationSourceInterface', function () {

            expect($this->source)->toBeAnInstanceOf(ConfigurationSourceInterface::class);

        });

        describe('->configuration()', function () {

            it('should return an empty MergedConfiguration', function () {

                $test = $this->source->configuration();

                expect($test)->toEqual(
                    new MergedConfiguration(...[
                        new PhpFileConfiguration($this->factory, ...[
                            __DIR__ . '/.test/config/not_array.php',
                        ]),
                        new PhpFileConfiguration($this->factory, ...[
                            __DIR__ . '/.test/config/valid.php',
                        ]),
                        new PhpFileConfiguration($this->factory, ...[
                            __DIR__ . '/.test/config/factories/not_array.php',
                        ]),
                        new PhpFileConfiguration($this->factory, ...[
                            __DIR__ . '/.test/config/factories/not_array_of_callables.php',
                        ]),
                    ])
                );

            });

        });

    });

});

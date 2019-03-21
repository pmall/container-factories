<?php

use Quanta\Container\PhpFileCollection;
use Quanta\Container\MergedConfiguration;
use Quanta\Container\PhpFileConfiguration;
use Quanta\Container\ConfigurationSourceInterface;
use Quanta\Container\Values\ValueFactory;

describe('PhpFileCollection', function () {

    beforeEach(function () {

        $this->factory = new ValueFactory;

    });

    context('when there is no pattern', function () {

        beforeEach(function () {

            $this->source = new PhpFileCollection($this->factory);

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

            $this->source = new PhpFileCollection($this->factory, ...[
                __DIR__ . '/.test/config/*.php',
                __DIR__ . '/.test/config/factories/*.php',
                __DIR__ . '/.test/config/extensions/*.php',
            ]);

        });

        it('should implement ConfigurationSourceInterface', function () {

            expect($this->source)->toBeAnInstanceOf(ConfigurationSourceInterface::class);

        });

        describe('->configuration()', function () {

            it('should create php file configuration entries from the files matched by the patterns and merge them', function () {

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
                        new PhpFileConfiguration($this->factory, ...[
                            __DIR__ . '/.test/config/extensions/not_array.php',
                        ]),
                        new PhpFileConfiguration($this->factory, ...[
                            __DIR__ . '/.test/config/extensions/not_array_of_callables.php',
                        ]),
                    ])
                );

            });

        });

    });

});

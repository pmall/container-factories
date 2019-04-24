<?php

use Quanta\Container\ValueParser;
use Quanta\Container\Configuration\PhpFileCollection;
use Quanta\Container\Configuration\MergedConfiguration;
use Quanta\Container\Configuration\PhpFileConfiguration;
use Quanta\Container\Configuration\ConfigurationSourceInterface;

describe('PhpFileCollection', function () {

    beforeEach(function () {

        $this->parser = new ValueParser;

    });

    context('when there is no pattern', function () {

        beforeEach(function () {

            $this->source = new PhpFileCollection($this->parser);

        });

        it('should implement ConfigurationSourceInterface', function () {

            expect($this->source)->toBeAnInstanceOf(ConfigurationSourceInterface::class);

        });

        describe('->configuration()', function () {

            it('should return an empty merged configuration', function () {

                $test = $this->source->configuration();

                expect($test)->toEqual(new MergedConfiguration);

            });

        });

    });

    context('when there is at least one pattern', function () {

        beforeEach(function () {

            $this->source = new PhpFileCollection($this->parser, ...[
                __DIR__ . '/../.test/config1/test*.php',
                __DIR__ . '/../.test/config2/test*.php',
                __DIR__ . '/../.test/config3/test*.php',
            ]);

        });

        it('should implement ConfigurationSourceInterface', function () {

            expect($this->source)->toBeAnInstanceOf(ConfigurationSourceInterface::class);

        });

        describe('->configuration()', function () {

            it('should return a merged configuration', function () {

                $test = $this->source->configuration();

                expect($test)->toEqual(new MergedConfiguration(
                    new PhpFileConfiguration($this->parser, __DIR__ . '/../.test/config1/test1.php'),
                    new PhpFileConfiguration($this->parser, __DIR__ . '/../.test/config1/test2.php'),
                    new PhpFileConfiguration($this->parser, __DIR__ . '/../.test/config1/test3.php'),
                    new PhpFileConfiguration($this->parser, __DIR__ . '/../.test/config2/test1.php'),
                    new PhpFileConfiguration($this->parser, __DIR__ . '/../.test/config2/test2.php'),
                    new PhpFileConfiguration($this->parser, __DIR__ . '/../.test/config2/test3.php'),
                    new PhpFileConfiguration($this->parser, __DIR__ . '/../.test/config3/test1.php'),
                    new PhpFileConfiguration($this->parser, __DIR__ . '/../.test/config3/test2.php'),
                    new PhpFileConfiguration($this->parser, __DIR__ . '/../.test/config3/test3.php')
                ));

            });

        });

    });

});

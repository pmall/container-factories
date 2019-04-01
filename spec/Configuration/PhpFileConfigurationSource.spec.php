<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\Parsing\ParserInterface;
use Quanta\Container\Configuration\PhpFileConfiguration;
use Quanta\Container\Configuration\ConfigurationInterface;
use Quanta\Container\Configuration\ArrayConfigurationUnit;
use Quanta\Container\Configuration\MergedConfigurationUnit;

describe('PhpFileConfiguration', function () {

    beforeEach(function () {

        $this->parser = mock(ParserInterface::class);

    });

    context('when there is no pattern', function () {

        beforeEach(function () {

            $this->configuration = new PhpFileConfiguration($this->parser->get());

        });

        it('should implement ConfigurationInterface', function () {

            expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

        });

        describe('->unit()', function () {

            it('should return an empty merged configuration unit', function () {

                $test = $this->configuration->unit();

                expect($test)->toEqual(new MergedConfigurationUnit);

            });

        });

    });

    context('when there is at least one pattern', function () {

        context('when all the files matching by the patterns are returning arrays', function () {

            beforeEach(function () {

                $this->configuration = new PhpFileConfiguration($this->parser->get(), ...[
                    __DIR__ . '/../.test/config1/test*.valid.php',
                    __DIR__ . '/../.test/config2/test*.valid.php',
                ]);

            });

            it('should implement ConfigurationInterface', function () {

                expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

            });

            describe('->unit()', function () {

                it('should return a merged configuration unit from the files', function () {

                    $test = $this->configuration->unit();

                    expect($test)->toEqual(
                        new MergedConfigurationUnit(...[
                            new ArrayConfigurationUnit($this->parser->get(), [
                                'key11' => 'value11',
                            ], realpath(__DIR__ . '/../.test/config1/test1.valid.php')),
                            new ArrayConfigurationUnit($this->parser->get(), [
                                'key13' => 'value13',
                            ], realpath(__DIR__ . '/../.test/config1/test3.valid.php')),
                            new ArrayConfigurationUnit($this->parser->get(), [
                                'key21' => 'value21',
                            ], realpath(__DIR__ . '/../.test/config2/test1.valid.php')),
                            new ArrayConfigurationUnit($this->parser->get(), [
                                'key23' => 'value23',
                            ], realpath(__DIR__ . '/../.test/config2/test3.valid.php')),
                        ])
                    );

                });

            });

        });

        context('when a file matched by the patterns is not returning an array', function () {

            beforeEach(function () {

                $this->configuration = new PhpFileConfiguration($this->parser->get(), ...[
                    __DIR__ . '/../.test/config1/test*.php',
                    __DIR__ . '/../.test/config2/test*.php',
                ]);

            });

            it('should implement ConfigurationInterface', function () {

                expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

            });

            describe('->unit()', function () {

                it('should throw an UnexpectedValueException', function () {

                    expect([$this->configuration, 'unit'])->toThrow(new UnexpectedValueException);

                });

            });

        });

    });

});

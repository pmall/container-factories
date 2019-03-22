<?php

use Quanta\Container\Configuration;
use Quanta\Container\ParameterConfigurationSource;
use Quanta\Container\ConfigurationSourceInterface;
use Quanta\Container\Maps\FactoryMap;
use Quanta\Container\Values\Value;
use Quanta\Container\Values\ValueFactory;
use Quanta\Container\Values\DummyValueParser;
use Quanta\Container\Factories\Factory;

describe('ParameterConfigurationSource', function () {

    beforeEach(function () {

        $this->factory = new ValueFactory(
            new DummyValueParser([
                'parameter1' => 'parsed1',
                'parameter2' => 'parsed2',
                'parameter3' => 'parsed3',
            ])
        );

    });

    context('when the parameter array is empty', function () {

        beforeEach(function () {

            $this->source = new ParameterConfigurationSource($this->factory, []);

        });

        it('should implement ConfigurationSourceInterface', function () {

            expect($this->source)->toBeAnInstanceOf(ConfigurationSourceInterface::class);

        });

        describe('->configuration()', function () {

            it('should return a configuration providing an empty array', function () {

                $test = $this->source->configuration();

                expect($test)->toEqual(new Configuration(
                    new FactoryMap([])
                ));

            });

        });

    });

    context('when the parameter array is not empty', function () {

        beforeEach(function () {

            $this->source = new ParameterConfigurationSource($this->factory, [
                'id1' => 'parameter1',
                'id2' => 'parameter2',
                'id3' => 'parameter3',
            ]);

        });

        it('should implement ConfigurationSourceInterface', function () {

            expect($this->source)->toBeAnInstanceOf(ConfigurationSourceInterface::class);

        });

        describe('->configuration()', function () {

            it('should return a configuration providing the parsed array of parameters', function () {

                $test = $this->source->configuration();

                expect($test)->toEqual(new Configuration(
                    new FactoryMap([
                        'id1' => new Factory(new Value('parsed1')),
                        'id2' => new Factory(new Value('parsed2')),
                        'id3' => new Factory(new Value('parsed3')),
                    ])
                ));

            });

        });

    });

});

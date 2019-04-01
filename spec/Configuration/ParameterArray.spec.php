<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\FactoryMap;
use Quanta\Container\ParameterFactoryMap;
use Quanta\Container\Parsing\ParserInterface;
use Quanta\Container\Configuration\ParameterArray;
use Quanta\Container\Configuration\ConfigurationUnit;
use Quanta\Container\Configuration\ConfigurationInterface;

describe('ParameterArray', function () {

    beforeEach(function () {

        $this->parser = mock(ParserInterface::class);

    });

    context('when the parameter array is empty', function () {

        beforeEach(function () {

            $this->configuration = new ParameterArray($this->parser->get(), []);

        });

        it('should implement ConfigurationInterface', function () {

            expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

        });

        describe('->unit()', function () {

            it('should return a configuration unit providing an empty parameter factory map', function () {

                $test = $this->configuration->unit();

                expect($test)->toEqual(new ConfigurationUnit(
                    new ParameterFactoryMap($this->parser->get(), [])
                ));

            });

        });

    });

    context('when the parameter array is not empty', function () {

        beforeEach(function () {

            $this->configuration = new ParameterArray($this->parser->get(), [
                'id1' => 'parameter1',
                'id2' => 'parameter2',
                'id3' => 'parameter3',
            ]);

        });

        it('should implement ConfigurationInterface', function () {

            expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

        });

        describe('->unit()', function () {

            it('should return a configuration unit providing the parsed array of parameters', function () {

                $test = $this->configuration->unit();

                expect($test)->toEqual(new ConfigurationUnit(
                    new ParameterFactoryMap($this->parser->get(), [
                        'id1' => 'parameter1',
                        'id2' => 'parameter2',
                        'id3' => 'parameter3',
                    ])
                ));

            });

        });

    });

});

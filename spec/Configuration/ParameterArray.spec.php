<?php

use Quanta\Container\FactoryMap;
use Quanta\Container\ValueParser;
use Quanta\Container\Configuration\ParameterArray;
use Quanta\Container\Configuration\ConfigurationUnit;
use Quanta\Container\Configuration\ConfigurationInterface;

describe('ParameterArray', function () {

    beforeEach(function () {

        $this->parser = new ValueParser;

    });

    context('when the parameter array is empty', function () {

        beforeEach(function () {

            $this->configuration = new ParameterArray($this->parser, []);

        });

        it('should implement ConfigurationInterface', function () {

            expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

        });

        describe('->unit()', function () {

            it('should return a configuration unit providing an empty parameter factory map', function () {

                $test = $this->configuration->unit();

                expect($test)->toEqual(new ConfigurationUnit(
                    new FactoryMap([])
                ));

            });

        });

    });

    context('when the parameter array is not empty', function () {

        beforeEach(function () {

            $this->configuration = new ParameterArray($this->parser, [
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
                    new FactoryMap([
                        'id1' => ($this->parser)('parameter1'),
                        'id2' => ($this->parser)('parameter2'),
                        'id3' => ($this->parser)('parameter3'),
                    ])
                ));

            });

        });

    });

});

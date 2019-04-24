<?php

use Quanta\Container\ValueParser;
use Quanta\Container\Configuration\ParameterArray;
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

        describe('->factories()', function () {

            it('should return an empty array', function () {

                $test = $this->configuration->factories();

                expect($test)->toEqual([]);

            });

        });

        describe('->mappers()', function () {

            it('should return an empty array', function () {

                $test = $this->configuration->mappers();

                expect($test)->toEqual([]);

            });

        });

        describe('->extensions()', function () {

            it('should return an empty array', function () {

                $test = $this->configuration->extensions();

                expect($test)->toEqual([]);

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

        describe('->factories()', function () {

            it('should return the parsed array of parameters', function () {

                $test = $this->configuration->factories();

                expect($test)->toEqual([
                    'id1' => ($this->parser)('parameter1'),
                    'id2' => ($this->parser)('parameter2'),
                    'id3' => ($this->parser)('parameter3'),
                ]);

            });

        });

        describe('->mappers()', function () {

            it('should return an empty array', function () {

                $test = $this->configuration->mappers();

                expect($test)->toEqual([]);

            });

        });

        describe('->extensions()', function () {

            it('should return an empty array', function () {

                $test = $this->configuration->extensions();

                expect($test)->toEqual([]);

            });

        });

    });

});

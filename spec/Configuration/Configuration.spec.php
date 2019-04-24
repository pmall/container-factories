<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\Configuration\Configuration;
use Quanta\Container\Configuration\ConfigurationInterface;
use Quanta\Container\Configuration\ConfigurationUnitInterface;

describe('Configuration', function () {

    context('when a value of the associative array of factories is not a callable', function () {

        it('should throw an InvalidArgumentException', function () {

            $test = function () {
                new Configuration([
                    'id1' => function () {},
                    'id2' => 1,
                    'id3' => function () {},
                ], [], []);
            };

            expect($test)->toThrow(new InvalidArgumentException);

        });

    });

    context('when a value of the associative array of mappers is not a callable', function () {

        it('should throw an InvalidArgumentException', function () {

            $test = function () {
                new Configuration([], [
                    'id1' => function () {},
                    'id2' => 1,
                    'id3' => function () {},
                ], []);
            };

            expect($test)->toThrow(new InvalidArgumentException);

        });

    });

    context('when a value of the associative array of extensions is not a callable', function () {

        it('should throw an InvalidArgumentException', function () {

            $test = function () {
                new Configuration([], [], [
                    'id1' => function () {},
                    'id2' => 1,
                    'id3' => function () {},
                ]);
            };

            expect($test)->toThrow(new InvalidArgumentException);

        });

    });

    context('when all the values of the associative arrays are callables', function () {

        beforeEach(function () {

            $this->configuration = new Configuration([
                'id1' => $this->factory1 = function () {},
                'id2' => $this->factory2 = function () {},
                'id3' => $this->factory3 = function () {},
            ], [
                'id1' => $this->mapper1 = function () {},
                'id2' => $this->mapper2 = function () {},
                'id3' => $this->mapper3 = function () {},
            ], [
                'id1' => $this->extension1 = function () {},
                'id2' => $this->extension2 = function () {},
                'id3' => $this->extension3 = function () {},
            ]);

        });

        it('should implement ConfigurationInterface', function () {

            expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

        });

        describe('->factories()', function () {

            it('should return the associative array of factories', function () {

                $test = $this->configuration->factories();

                expect($test)->toEqual([
                    'id1' => $this->factory1,
                    'id2' => $this->factory2,
                    'id3' => $this->factory3,
                ]);

            });

        });

        describe('->mappers()', function () {

            it('should return the associative array of mappers', function () {

                $test = $this->configuration->mappers();

                expect($test)->toEqual([
                    'id1' => $this->mapper1,
                    'id2' => $this->mapper2,
                    'id3' => $this->mapper3,
                ]);

            });

        });

        describe('->extensions()', function () {

            it('should return the associative array of extensions wrapped inside arrays', function () {

                $test = $this->configuration->extensions();

                expect($test)->toEqual([
                    'id1' => [$this->extension1],
                    'id2' => [$this->extension2],
                    'id3' => [$this->extension3],
                ]);

            });

        });

    });

});

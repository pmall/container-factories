<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\Configuration\MergedConfiguration;
use Quanta\Container\Configuration\ConfigurationInterface;
use Quanta\Container\Configuration\Tagging\CompositeTagging;

describe('MergedConfiguration', function () {

    context('when there is no configuration', function () {

        beforeEach(function () {

            $this->configuration = new MergedConfiguration;

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

    context('when there is at least one configuration', function () {

        beforeEach(function () {

            $this->configuration1 = mock(ConfigurationInterface::class);
            $this->configuration2 = mock(ConfigurationInterface::class);
            $this->configuration3 = mock(ConfigurationInterface::class);

            $this->configuration = new MergedConfiguration(...[
                $this->configuration1->get(),
                $this->configuration2->get(),
                $this->configuration3->get(),
            ]);

        });

        it('should implement ConfigurationInterface', function () {

            expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

        });

        describe('->factories()', function () {

            it('should merge associative array of factories provided by the configurations', function () {

                $this->configuration1->factories->returns([
                    'id1' => $factory11 = function () {},
                    'id2' => $factory12 = function () {},
                    'id3' => $factory13 = function () {},
                ]);

                $this->configuration2->factories->returns([
                    'id2' => $factory22 = function () {},
                    'id3' => $factory23 = function () {},
                    'id4' => $factory24 = function () {},
                ]);

                $this->configuration3->factories->returns([
                    'id3' => $factory33 = function () {},
                    'id4' => $factory34 = function () {},
                    'id5' => $factory35 = function () {},
                ]);

                $test = $this->configuration->factories();

                expect($test)->toEqual([
                    'id1' => $factory11,
                    'id2' => $factory22,
                    'id3' => $factory33,
                    'id4' => $factory34,
                    'id5' => $factory35,
                ]);

            });

        });

        describe('->mappers()', function () {

            it('should merge associative array of factories provided by the configurations', function () {

                $this->configuration1->mappers->returns([
                    'id1' => $mapper11 = function () {},
                    'id2' => $mapper12 = function () {},
                    'id3' => $mapper13 = function () {},
                ]);

                $this->configuration2->mappers->returns([
                    'id2' => $mapper22 = function () {},
                    'id3' => $mapper23 = function () {},
                    'id4' => $mapper24 = function () {},
                ]);

                $this->configuration3->mappers->returns([
                    'id3' => $mapper33 = function () {},
                    'id4' => $mapper34 = function () {},
                    'id5' => $mapper35 = function () {},
                ]);

                $test = $this->configuration->mappers();

                expect($test)->toEqual([
                    'id1' => $mapper11,
                    'id2' => new CompositeTagging($mapper12, $mapper22),
                    'id3' => new CompositeTagging($mapper13, $mapper23, $mapper33),
                    'id4' => new CompositeTagging($mapper24, $mapper34),
                    'id5' => $mapper35,
                ]);

            });

        });

        describe('->extensions()', function () {

            it('should merge associative array of factories provided by the configurations', function () {

                $this->configuration1->extensions->returns([
                    'id1' => [$mapper111 = function () {}, $mapper112 = function () {}],
                    'id2' => [$mapper121 = function () {}, $mapper122 = function () {}],
                    'id3' => [$mapper131 = function () {}, $mapper132 = function () {}],
                ]);

                $this->configuration2->extensions->returns([
                    'id2' => [$mapper221 = function () {}, $mapper222 = function () {}],
                    'id3' => [$mapper231 = function () {}, $mapper232 = function () {}],
                    'id4' => [$mapper241 = function () {}, $mapper242 = function () {}],
                ]);

                $this->configuration3->extensions->returns([
                    'id3' => [$mapper331 = function () {}, $mapper332 = function () {}],
                    'id4' => [$mapper341 = function () {}, $mapper342 = function () {}],
                    'id5' => [$mapper351 = function () {}, $mapper352 = function () {}],
                ]);

                $test = $this->configuration->extensions();

                expect($test)->toEqual([
                    'id1' => [$mapper111, $mapper112],
                    'id2' => [$mapper121, $mapper122, $mapper221, $mapper222],
                    'id3' => [$mapper131, $mapper132, $mapper231, $mapper232, $mapper331, $mapper332],
                    'id4' => [$mapper241, $mapper242, $mapper341, $mapper342],
                    'id5' => [$mapper351, $mapper352],
                ]);

            });

        });

    });

});

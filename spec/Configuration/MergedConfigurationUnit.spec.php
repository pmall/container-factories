<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\MergedProcessingPass;
use Quanta\Container\ProcessingPassInterface;
use Quanta\Container\Configuration\MergedConfigurationUnit;
use Quanta\Container\Configuration\ConfigurationUnitInterface;

describe('MergedConfigurationUnit', function () {

    context('when there is no configuration unit', function () {

        beforeEach(function () {

            $this->unit = new MergedConfigurationUnit;

        });

        it('should implement ConfigurationUnitInterface', function () {

            expect($this->unit)->toBeAnInstanceOf(ConfigurationUnitInterface::class);

        });

        describe('->factories()', function () {

            it('should return an empty array', function () {

                $test = $this->unit->factories();

                expect($test)->toEqual([]);

            });

        });

        describe('->pass()', function () {

            it('should return an empty merged processing pass', function () {

                $test = $this->unit->pass();

                expect($test)->toEqual(new MergedProcessingPass);

            });

        });

    });

    context('when there is at least one configuration unit', function () {

        beforeEach(function () {

            $this->unit1 = mock(ConfigurationUnitInterface::class);
            $this->unit2 = mock(ConfigurationUnitInterface::class);
            $this->unit3 = mock(ConfigurationUnitInterface::class);

            $this->unit = new MergedConfigurationUnit(...[
                $this->unit1->get(),
                $this->unit2->get(),
                $this->unit3->get(),
            ]);

        });

        it('should implement ConfigurationUnitInterface', function () {

            expect($this->unit)->toBeAnInstanceOf(ConfigurationUnitInterface::class);

        });

        describe('->factories()', function () {

            it('should return a merged associative array of factories', function () {

                $this->unit1->factories->returns([
                    'id1' => $factory11 = function () {},
                    'id2' => $factory12 = function () {},
                    'id3' => $factory13 = function () {},
                ]);
                $this->unit2->factories->returns([
                    'id2' => $factory22 = function () {},
                    'id3' => $factory23 = function () {},
                    'id4' => $factory24 = function () {},
                ]);
                $this->unit3->factories->returns([
                    'id3' => $factory33 = function () {},
                    'id4' => $factory34 = function () {},
                    'id5' => $factory35 = function () {},
                ]);

                $test = $this->unit->factories();

                expect($test)->toEqual([
                    'id1' => $factory11,
                    'id2' => $factory22,
                    'id3' => $factory33,
                    'id4' => $factory34,
                    'id5' => $factory35,
                ]);

            });

        });

        describe('->pass()', function () {

            it('should return a merged processing pass', function () {

                $pass1 = mock(ProcessingPassInterface::class);
                $pass2 = mock(ProcessingPassInterface::class);
                $pass3 = mock(ProcessingPassInterface::class);

                $this->unit1->pass->returns($pass1);
                $this->unit2->pass->returns($pass2);
                $this->unit3->pass->returns($pass3);

                $test = $this->unit->pass();

                expect($test)->toEqual(new MergedProcessingPass(...[
                    $pass1->get(),
                    $pass2->get(),
                    $pass3->get(),
                ]));

            });

        });

    });

});

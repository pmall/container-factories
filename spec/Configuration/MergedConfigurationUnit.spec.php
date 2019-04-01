<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\MergedFactoryMap;
use Quanta\Container\FactoryMapInterface;
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

        describe('->map()', function () {

            it('should return an empty merged factory map', function () {

                $test = $this->unit->map();

                expect($test)->toEqual(new MergedFactoryMap);

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

        describe('->map()', function () {

            it('should merge the factory maps provided by the configuration units', function () {

                $map1 = mock(FactoryMapInterface::class);
                $map2 = mock(FactoryMapInterface::class);
                $map3 = mock(FactoryMapInterface::class);

                $this->unit1->map->returns($map1);
                $this->unit2->map->returns($map2);
                $this->unit3->map->returns($map3);

                $test = $this->unit->map();

                expect($test)->toEqual(new MergedFactoryMap(...[
                    $map1->get(),
                    $map2->get(),
                    $map3->get(),
                ]));

            });

        });

        describe('->pass()', function () {

            it('should merge the processing passes provided by the configuration units', function () {

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

<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\Configuration\MergedConfiguration;
use Quanta\Container\Configuration\ConfigurationInterface;
use Quanta\Container\Configuration\MergedConfigurationUnit;
use Quanta\Container\Configuration\ConfigurationUnitInterface;

describe('MergedConfiguration', function () {

    context('when there is no configuration', function () {

        beforeEach(function () {

            $this->configuration = new MergedConfiguration;

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

        describe('->unit()', function () {

            it('should merge the configuration units provided by the configurations', function () {

                $unit1 = mock(ConfigurationUnitInterface::class);
                $unit2 = mock(ConfigurationUnitInterface::class);
                $unit3 = mock(ConfigurationUnitInterface::class);

                $this->configuration1->unit->returns($unit1);
                $this->configuration2->unit->returns($unit2);
                $this->configuration3->unit->returns($unit3);

                $test = $this->configuration->unit();

                expect($test)->toEqual(new MergedConfigurationUnit(...[
                    $unit1->get(),
                    $unit2->get(),
                    $unit3->get(),
                ]));

            });

        });

    });

});

<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\MergedConfiguration;
use Quanta\Container\ConfiguredFactoryMap;
use Quanta\Container\ConfigurationInterface;
use Quanta\Container\Maps\MergedFactoryMap;
use Quanta\Container\Maps\FactoryMapInterface;
use Quanta\Container\Passes\MergedProcessingPass;
use Quanta\Container\Passes\ProcessingPassInterface;

describe('MergedConfiguration', function () {

    context('when there is no configuration', function () {

        beforeEach(function () {

            $this->configuration = new MergedConfiguration;

        });

        it('should implement ConfigurationInterface', function () {

            expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

        });

        describe('->map()', function () {

            it('should return an empty configured factory map', function () {

                $test = $this->configuration->map();

                expect($test)->toEqual(new ConfiguredFactoryMap(
                    new MergedFactoryMap,
                    new MergedProcessingPass
                ));

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

        describe('->map()', function () {

            it('should merge the configured factory maps provided by the configurations', function () {

                $delegate1 = mock(FactoryMapInterface::class);
                $delegate2 = mock(FactoryMapInterface::class);
                $delegate3 = mock(FactoryMapInterface::class);

                $processing1 = mock(ProcessingPassInterface::class);
                $processing2 = mock(ProcessingPassInterface::class);
                $processing3 = mock(ProcessingPassInterface::class);

                $map1 = new ConfiguredFactoryMap($delegate1->get(), $processing1->get());
                $map2 = new ConfiguredFactoryMap($delegate2->get(), $processing2->get());
                $map3 = new ConfiguredFactoryMap($delegate3->get(), $processing3->get());

                $this->configuration1->map->returns($map1);
                $this->configuration2->map->returns($map2);
                $this->configuration3->map->returns($map3);

                $test = $this->configuration->map();

                expect($test)->toEqual(new ConfiguredFactoryMap(
                    new MergedFactoryMap($delegate1->get(), $delegate2->get(), $delegate3->get()),
                    new MergedProcessingPass($processing1->get(), $processing2->get(), $processing3->get())
                ));

            });

        });

    });

});

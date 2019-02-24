<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\MergedFactoryMap;
use Quanta\Container\ProcessedFactoryMap;
use Quanta\Container\FactoryMapInterface;
use Quanta\Container\ProcessingPassInterface;
use Quanta\Container\Configuration\MergedConfiguration;
use Quanta\Container\Configuration\ConfigurationInterface;

describe('MergedConfiguration', function () {

    context('when there is no configuration', function () {

        beforeEach(function () {

            $this->configuration = new MergedConfiguration;

        });

        it('should implement ConfigurationInterface', function () {

            expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

        });

        describe('->map()', function () {

            it('should return an empty processed factory map', function () {

                $test = $this->configuration->map();

                expect($test)->toEqual(new ProcessedFactoryMap(
                    new MergedFactoryMap
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

            it('should merge the processed factory maps provided by the configurations', function () {

                $map1 = mock(FactoryMapInterface::class);
                $map2 = mock(FactoryMapInterface::class);
                $map3 = mock(FactoryMapInterface::class);

                $pass11 = mock(ProcessingPassInterface::class);
                $pass12 = mock(ProcessingPassInterface::class);
                $pass13 = mock(ProcessingPassInterface::class);
                $pass21 = mock(ProcessingPassInterface::class);
                $pass22 = mock(ProcessingPassInterface::class);
                $pass23 = mock(ProcessingPassInterface::class);
                $pass31 = mock(ProcessingPassInterface::class);
                $pass32 = mock(ProcessingPassInterface::class);
                $pass33 = mock(ProcessingPassInterface::class);

                $this->configuration1->map->returns(new ProcessedFactoryMap($map1->get(), ...[
                    $pass11->get(),
                    $pass12->get(),
                    $pass13->get(),
                ]));

                $this->configuration2->map->returns(new ProcessedFactoryMap($map2->get(), ...[
                    $pass21->get(),
                    $pass22->get(),
                    $pass23->get(),
                ]));

                $this->configuration3->map->returns(new ProcessedFactoryMap($map3->get(), ...[
                    $pass31->get(),
                    $pass32->get(),
                    $pass33->get(),
                ]));


                $test = $this->configuration->map();

                expect($test)->toEqual(new ProcessedFactoryMap(
                    new MergedFactoryMap($map1->get(), $map2->get(), $map3->get()),
                    $pass11->get(),
                    $pass12->get(),
                    $pass13->get(),
                    $pass21->get(),
                    $pass22->get(),
                    $pass23->get(),
                    $pass31->get(),
                    $pass32->get(),
                    $pass33->get()
                ));

            });

        });

    });

});

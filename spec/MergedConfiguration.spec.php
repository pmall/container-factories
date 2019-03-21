<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\ConfigurationEntry;
use Quanta\Container\MergedConfiguration;
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

        describe('->entry()', function () {

            it('should return an empty configuration entry', function () {

                $test = $this->configuration->entry();

                expect($test)->toEqual(new ConfigurationEntry(
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

        describe('->entry()', function () {

            it('should merge the configuration provided by the configuration entries', function () {

                $map1 = mock(FactoryMapInterface::class);
                $map2 = mock(FactoryMapInterface::class);
                $map3 = mock(FactoryMapInterface::class);

                $pass1 = mock(ProcessingPassInterface::class);
                $pass2 = mock(ProcessingPassInterface::class);
                $pass3 = mock(ProcessingPassInterface::class);

                $entry1 = new ConfigurationEntry($map1->get(), $pass1->get());
                $entry2 = new ConfigurationEntry($map2->get(), $pass2->get());
                $entry3 = new ConfigurationEntry($map3->get(), $pass3->get());

                $this->configuration1->entry->returns($entry1);
                $this->configuration2->entry->returns($entry2);
                $this->configuration3->entry->returns($entry3);

                $test = $this->configuration->entry();

                expect($test)->toEqual(new ConfigurationEntry(
                    new MergedFactoryMap($map1->get(), $map2->get(), $map3->get()),
                    new MergedProcessingPass($pass1->get(), $pass2->get(), $pass3->get())
                ));

            });

        });

    });

});

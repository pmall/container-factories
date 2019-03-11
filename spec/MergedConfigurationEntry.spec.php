<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\Configuration;
use Quanta\Container\MergedConfigurationEntry;
use Quanta\Container\ConfigurationEntryInterface;
use Quanta\Container\Maps\MergedFactoryMap;
use Quanta\Container\Maps\FactoryMapInterface;
use Quanta\Container\Passes\MergedProcessingPass;
use Quanta\Container\Passes\ProcessingPassInterface;

describe('MergedConfigurationEntry', function () {

    context('when there is no configuration entry', function () {

        beforeEach(function () {

            $this->entry = new MergedConfigurationEntry;

        });

        it('should implement ConfigurationEntryInterface', function () {

            expect($this->entry)->toBeAnInstanceOf(ConfigurationEntryInterface::class);

        });

        describe('->configuration()', function () {

            it('should return an empty configuration', function () {

                $test = $this->entry->configuration();

                expect($test)->toEqual(new Configuration(
                    new MergedFactoryMap,
                    new MergedProcessingPass
                ));

            });

        });

    });

    context('when there is at least one configuration entry', function () {

        beforeEach(function () {

            $this->entry1 = mock(ConfigurationEntryInterface::class);
            $this->entry2 = mock(ConfigurationEntryInterface::class);
            $this->entry3 = mock(ConfigurationEntryInterface::class);

            $this->entry = new MergedConfigurationEntry(...[
                $this->entry1->get(),
                $this->entry2->get(),
                $this->entry3->get(),
            ]);

        });

        it('should implement ConfigurationEntryInterface', function () {

            expect($this->entry)->toBeAnInstanceOf(ConfigurationEntryInterface::class);

        });

        describe('->configuration()', function () {

            it('should merge the configuration provided by the configuration entries', function () {

                $map1 = mock(FactoryMapInterface::class);
                $map2 = mock(FactoryMapInterface::class);
                $map3 = mock(FactoryMapInterface::class);

                $pass1 = mock(ProcessingPassInterface::class);
                $pass2 = mock(ProcessingPassInterface::class);
                $pass3 = mock(ProcessingPassInterface::class);

                $configuration1 = new Configuration($map1->get(), $pass1->get());
                $configuration2 = new Configuration($map2->get(), $pass2->get());
                $configuration3 = new Configuration($map3->get(), $pass3->get());

                $this->entry1->configuration->returns($configuration1);
                $this->entry2->configuration->returns($configuration2);
                $this->entry3->configuration->returns($configuration3);

                $test = $this->entry->configuration();

                expect($test)->toEqual(new Configuration(
                    new MergedFactoryMap($map1->get(), $map2->get(), $map3->get()),
                    new MergedProcessingPass($pass1->get(), $pass2->get(), $pass3->get())
                ));

            });

        });

    });

});

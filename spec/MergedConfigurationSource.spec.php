<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\Configuration;
use Quanta\Container\MergedFactoryMap;
use Quanta\Container\FactoryMapInterface;
use Quanta\Container\ConfigurationInterface;
use Quanta\Container\MergedConfigurationSource;
use Quanta\Container\ConfigurationPassInterface;
use Quanta\Container\ConfigurationSourceInterface;

describe('MergedConfigurationSource', function () {

    context('when there is no configuration source', function () {

        beforeEach(function () {

            $this->source = new MergedConfigurationSource;

        });

        it('should implement ConfigurationSourceInterface', function () {

            expect($this->source)->toBeAnInstanceOf(ConfigurationSourceInterface::class);

        });

        describe('->configurations()', function () {

            it('should return an empty configuration', function () {

                $test = $this->source->configuration();

                expect($test)->toEqual(new Configuration(new MergedFactoryMap));

            });

        });

    });

    context('when there is at least one configuration source', function () {

        beforeEach(function () {

            $this->source1 = mock(ConfigurationSourceInterface::class);
            $this->source2 = mock(ConfigurationSourceInterface::class);
            $this->source3 = mock(ConfigurationSourceInterface::class);

            $this->source = new MergedConfigurationSource(...[
                $this->source1->get(),
                $this->source2->get(),
                $this->source3->get(),
            ]);

        });

        it('should implement ConfigurationInterface', function () {

            expect($this->source)->toBeAnInstanceOf(ConfigurationSourceInterface::class);

        });

        describe('->configurations()', function () {

            it('should return a merged configuration', function () {

                $source1 = mock(ConfigurationSourceInterface::class);
                $source2 = mock(ConfigurationSourceInterface::class);
                $source3 = mock(ConfigurationSourceInterface::class);

                $configuration1 = mock(ConfigurationInterface::class);
                $configuration2 = mock(ConfigurationInterface::class);
                $configuration3 = mock(ConfigurationInterface::class);

                $map1 = mock(FactoryMapInterface::class);
                $map2 = mock(FactoryMapInterface::class);
                $map3 = mock(FactoryMapInterface::class);

                $pass1 = mock(ConfigurationPassInterface::class);
                $pass2 = mock(ConfigurationPassInterface::class);
                $pass3 = mock(ConfigurationPassInterface::class);
                $pass4 = mock(ConfigurationPassInterface::class);
                $pass5 = mock(ConfigurationPassInterface::class);
                $pass6 = mock(ConfigurationPassInterface::class);
                $pass7 = mock(ConfigurationPassInterface::class);
                $pass8 = mock(ConfigurationPassInterface::class);
                $pass9 = mock(ConfigurationPassInterface::class);

                $this->source1->configuration->returns($configuration1);
                $this->source2->configuration->returns($configuration2);
                $this->source3->configuration->returns($configuration3);

                $configuration1->map->returns($map1);
                $configuration2->map->returns($map2);
                $configuration3->map->returns($map3);

                $configuration1->passes->returns([$pass1->get(), $pass2->get(), $pass3->get()]);
                $configuration2->passes->returns([$pass4->get(), $pass5->get(), $pass6->get()]);
                $configuration3->passes->returns([$pass7->get(), $pass8->get(), $pass9->get()]);

                $test = $this->source->configuration();

                expect($test)->toEqual(new Configuration(
                    new MergedFactoryMap($map1->get(), $map2->get(), $map3->get()),
                    $pass1->get(),
                    $pass2->get(),
                    $pass3->get(),
                    $pass4->get(),
                    $pass5->get(),
                    $pass6->get(),
                    $pass7->get(),
                    $pass8->get(),
                    $pass9->get()
                ));

            });

        });

    });

});

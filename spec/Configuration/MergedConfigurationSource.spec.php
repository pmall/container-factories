<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\Configuration\MergedConfiguration;
use Quanta\Container\Configuration\ConfigurationInterface;
use Quanta\Container\Configuration\MergedConfigurationSource;
use Quanta\Container\Configuration\ConfigurationSourceInterface;

describe('MergedConfigurationSource', function () {

    context('when there is no configuration source', function () {

        beforeEach(function () {

            $this->source = new MergedConfigurationSource;

        });

        it('should implement ConfigurationSourceInterface', function () {

            expect($this->source)->toBeAnInstanceOf(ConfigurationSourceInterface::class);

        });

        describe('->configuration()', function () {

            it('should return an empty merged configuration', function () {

                $test = $this->source->configuration();

                expect($test)->toEqual(new MergedConfiguration);

            });

        });

    });

    context('when there is at least one configuration source', function () {

        beforeEach(function () {

            $this->source1 = mock(ConfigurationSourceInterface::class);
            $this->source2 = mock(ConfigurationSourceInterface::class);
            $this->source3 = mock(ConfigurationSourceInterface::class);

            $this->source = new MergedConfigurationSource(
                $this->source1->get(),
                $this->source2->get(),
                $this->source3->get()
            );

        });

        it('should implement ConfigurationSourceInterface', function () {

            expect($this->source)->toBeAnInstanceOf(ConfigurationSourceInterface::class);

        });

        describe('->configuration()', function () {

            it('should return a merged configuration', function () {

                $configuration1 = mock(ConfigurationInterface::class);
                $configuration2 = mock(ConfigurationInterface::class);
                $configuration3 = mock(ConfigurationInterface::class);

                $this->source1->configuration->returns($configuration1);
                $this->source2->configuration->returns($configuration2);
                $this->source3->configuration->returns($configuration3);

                $test = $this->source->configuration();

                expect($test)->toEqual(new MergedConfiguration(
                    $configuration1->get(),
                    $configuration2->get(),
                    $configuration3->get()
                ));

            });

        });

    });

});

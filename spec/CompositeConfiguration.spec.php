<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\ConfigurationInterface;
use Quanta\Container\CompositeConfiguration;
use Quanta\Container\TaggedServiceProviderInterface;

describe('CompositeConfiguration', function () {

    context('when there is no configuration', function () {

        beforeEach(function () {

            $this->configuration = new CompositeConfiguration;

        });

        it('should implement ConfigurationInterface', function () {

            expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

        });

        describe('->providers()', function () {

            it('should return an empty array', function () {

                $test = $this->configuration->providers();

                expect($test)->toEqual([]);

            });

        });

    });

    context('when there is at least one configuration', function () {

        beforeEach(function () {

            $this->delegate1 = mock(ConfigurationInterface::class);
            $this->delegate2 = mock(ConfigurationInterface::class);
            $this->delegate3 = mock(ConfigurationInterface::class);

            $this->configuration = new CompositeConfiguration(...[
                $this->delegate1->get(),
                $this->delegate2->get(),
                $this->delegate3->get(),
            ]);

        });

        it('should implement ConfigurationInterface', function () {

            expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

        });

        describe('->providers()', function () {

            it('should return all the tagged service providers returned by all the configurations ->provide() methods', function () {

                $provider11 = mock(TaggedServiceProviderInterface::class);
                $provider12 = mock(TaggedServiceProviderInterface::class);
                $provider13 = mock(TaggedServiceProviderInterface::class);
                $provider21 = mock(TaggedServiceProviderInterface::class);
                $provider22 = mock(TaggedServiceProviderInterface::class);
                $provider23 = mock(TaggedServiceProviderInterface::class);
                $provider31 = mock(TaggedServiceProviderInterface::class);
                $provider32 = mock(TaggedServiceProviderInterface::class);
                $provider33 = mock(TaggedServiceProviderInterface::class);

                $this->delegate1->providers->returns([
                    $provider11->get(),
                    $provider12->get(),
                    $provider13->get(),
                ]);

                $this->delegate2->providers->returns([
                    $provider21->get(),
                    $provider22->get(),
                    $provider23->get(),
                ]);

                $this->delegate3->providers->returns([
                    $provider31->get(),
                    $provider32->get(),
                    $provider33->get(),
                ]);

                $test = $this->configuration->providers();

                expect($test)->toEqual([
                    $provider11->get(),
                    $provider12->get(),
                    $provider13->get(),
                    $provider21->get(),
                    $provider22->get(),
                    $provider23->get(),
                    $provider31->get(),
                    $provider32->get(),
                    $provider33->get(),
                ]);

            });

        });

    });

});

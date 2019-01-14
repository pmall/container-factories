<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\MergedConfiguration;
use Quanta\Container\ConfigurationInterface;
use Quanta\Container\ConfigurationEntryInterface;

describe('MergedConfiguration', function () {

    context('when there is no configuration', function () {

        beforeEach(function () {

            $this->configuration = new MergedConfiguration;

        });

        it('should implement ConfigurationInterface', function () {

            expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

        });

        describe('->entries()', function () {

            it('should return an empty array', function () {

                $test = $this->configuration->entries();

                expect($test)->toEqual([]);

            });

        });

    });

    context('when there is at least one configuration', function () {

        beforeEach(function () {

            $this->delegate1 = mock(ConfigurationInterface::class);
            $this->delegate2 = mock(ConfigurationInterface::class);
            $this->delegate3 = mock(ConfigurationInterface::class);

            $this->configuration = new MergedConfiguration(...[
                $this->delegate1->get(),
                $this->delegate2->get(),
                $this->delegate3->get(),
            ]);

        });

        it('should implement ConfigurationInterface', function () {

            expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

        });

        describe('->entries()', function () {

            it('should return all the configuration entries returned by all the configurations ->entries() methods', function () {

                $entry11 = mock(ConfigurationEntryInterface::class);
                $entry12 = mock(ConfigurationEntryInterface::class);
                $entry13 = mock(ConfigurationEntryInterface::class);
                $entry21 = mock(ConfigurationEntryInterface::class);
                $entry22 = mock(ConfigurationEntryInterface::class);
                $entry23 = mock(ConfigurationEntryInterface::class);
                $entry31 = mock(ConfigurationEntryInterface::class);
                $entry32 = mock(ConfigurationEntryInterface::class);
                $entry33 = mock(ConfigurationEntryInterface::class);

                $this->delegate1->entries->returns([
                    $entry11->get(),
                    $entry12->get(),
                    $entry13->get(),
                ]);

                $this->delegate2->entries->returns([
                    $entry21->get(),
                    $entry22->get(),
                    $entry23->get(),
                ]);

                $this->delegate3->entries->returns([
                    $entry31->get(),
                    $entry32->get(),
                    $entry33->get(),
                ]);

                $test = $this->configuration->entries();

                expect($test)->toEqual([
                    $entry11->get(),
                    $entry12->get(),
                    $entry13->get(),
                    $entry21->get(),
                    $entry22->get(),
                    $entry23->get(),
                    $entry31->get(),
                    $entry32->get(),
                    $entry33->get(),
                ]);

            });

        });

    });

});

<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\MergedConfigurationEntry;
use Quanta\Container\MergedConfigurationSource;
use Quanta\Container\ConfigurationEntryInterface;
use Quanta\Container\ConfigurationSourceInterface;

describe('MergedConfigurationSource', function () {

    context('when there is no configuration source', function () {

        beforeEach(function () {

            $this->source = new MergedConfigurationSource;

        });

        it('should implement ConfigurationSourceInterface', function () {

            expect($this->source)->toBeAnInstanceOf(ConfigurationSourceInterface::class);

        });

        describe('->entry()', function () {

            it('should return an empty merged configuration entry', function () {

                $test = $this->source->entry();

                expect($test)->toEqual(new MergedConfigurationEntry);

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

        it('should implement ConfigurationSourceInterface', function () {

            expect($this->source)->toBeAnInstanceOf(ConfigurationSourceInterface::class);

        });

        describe('->entry()', function () {

            it('should merge the configuration entries provided by the configuration sources', function () {

                $entry1 = mock(ConfigurationEntryInterface::class);
                $entry2 = mock(ConfigurationEntryInterface::class);
                $entry3 = mock(ConfigurationEntryInterface::class);

                $this->source1->entry->returns($entry1->get());
                $this->source2->entry->returns($entry2->get());
                $this->source3->entry->returns($entry3->get());

                $test = $this->source->entry();

                expect($test)->toEqual(new MergedConfigurationEntry(...[
                    $entry1->get(),
                    $entry2->get(),
                    $entry3->get(),
                ]));

            });

        });

    });

});

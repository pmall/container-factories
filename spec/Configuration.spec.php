<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\Configuration;
use Quanta\Container\ConfigurationEntry;
use Quanta\Container\ConfigurationInterface;
use Quanta\Container\Maps\FactoryMapInterface;
use Quanta\Container\Passes\MergedProcessingPass;
use Quanta\Container\Passes\ProcessingPassInterface;

describe('Configuration', function () {

    beforeEach(function () {

        $this->map = mock(FactoryMapInterface::class);

    });

    context('when there is no processing pass', function () {

        beforeEach(function () {

            $this->configuration = new Configuration($this->map->get());

        });

        it('should implement ConfigurationInterface', function () {

            expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

        });

        describe('->entry()', function () {

            it('should return a configuration entry with an empty merged processing pass', function () {

                $test = $this->configuration->entry();

                expect($test)->toEqual(new ConfigurationEntry(
                    $this->map->get(),
                    new MergedProcessingPass
                ));

            });

        });

    });

    context('when there is at least one processing pass', function () {

        beforeEach(function () {

            $this->pass1 = mock(ProcessingPassInterface::class);
            $this->pass2 = mock(ProcessingPassInterface::class);
            $this->pass3 = mock(ProcessingPassInterface::class);

            $this->configuration = new Configuration($this->map->get(), ...[
                $this->pass1->get(),
                $this->pass2->get(),
                $this->pass3->get(),
            ]);

        });

        it('should implement ConfigurationInterface', function () {

            expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

        });

        describe('->entry()', function () {

            it('should return a configuration entry with the merged processing pass', function () {

                $test = $this->configuration->entry();

                expect($test)->toEqual(new ConfigurationEntry(
                    $this->map->get(),
                    new MergedProcessingPass(
                        $this->pass1->get(),
                        $this->pass2->get(),
                        $this->pass3->get()
                    )
                ));

            });

        });

    });

});

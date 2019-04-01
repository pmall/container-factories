<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\FactoryMapInterface;
use Quanta\Container\ProcessingPassInterface;
use Quanta\Container\Configuration\Configuration;
use Quanta\Container\Configuration\ConfigurationUnit;
use Quanta\Container\Configuration\ConfigurationInterface;

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

        describe('->unit()', function () {

            it('should return a configuration unit with no processing pass', function () {

                $test = $this->configuration->unit();

                expect($test)->toEqual(new ConfigurationUnit($this->map->get()));

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

        describe('->unit()', function () {

            it('should return a configuration unit with the processing passes', function () {

                $test = $this->configuration->unit();

                expect($test)->toEqual(new ConfigurationUnit($this->map->get(), ...[
                    $this->pass1->get(),
                    $this->pass2->get(),
                    $this->pass3->get()
                ]));

            });

        });

    });

});

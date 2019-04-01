<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\FactoryMapInterface;
use Quanta\Container\MergedProcessingPass;
use Quanta\Container\ProcessingPassInterface;
use Quanta\Container\Configuration\ConfigurationUnit;
use Quanta\Container\Configuration\ConfigurationUnitInterface;

describe('ConfigurationUnit', function () {

    beforeEach(function () {

        $this->map = mock(FactoryMapInterface::class);

    });

    context('when there is no processing pass', function () {

        beforeEach(function () {

            $this->unit = new ConfigurationUnit($this->map->get());

        });

        it('should implement ConfigurationUnitInterface', function () {

            expect($this->unit)->toBeAnInstanceOf(ConfigurationUnitInterface::class);

        });

        describe('->map()', function () {

            it('should return the factory map', function () {

                $test = $this->unit->map();

                expect($test)->toBe($this->map->get());

            });

        });

        describe('->pass()', function () {

            it('should return an empty merged processing pass', function () {

                $test = $this->unit->pass();

                expect($test)->toEqual(new MergedProcessingPass);

            });

        });

    });

    context('when there is at least one processing pass', function () {

        beforeEach(function () {

            $this->pass1 = mock(ProcessingPassInterface::class);
            $this->pass2 = mock(ProcessingPassInterface::class);
            $this->pass3 = mock(ProcessingPassInterface::class);

            $this->unit = new ConfigurationUnit($this->map->get(), ...[
                $this->pass1->get(),
                $this->pass2->get(),
                $this->pass3->get(),
            ]);

        });

        it('should implement ConfigurationUnitInterface', function () {

            expect($this->unit)->toBeAnInstanceOf(ConfigurationUnitInterface::class);

        });

        describe('->map()', function () {

            it('should return the factory map', function () {

                $test = $this->unit->map();

                expect($test)->toBe($this->map->get());

            });

        });

        describe('->pass()', function () {

            it('should merge the processing passes', function () {

                $test = $this->unit->pass();

                expect($test)->toEqual(new MergedProcessingPass(...[
                    $this->pass1->get(),
                    $this->pass2->get(),
                    $this->pass3->get(),
                ]));

            });

        });

    });

});

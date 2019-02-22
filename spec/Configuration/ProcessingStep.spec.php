<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\FactoryMapInterface;
use Quanta\Container\ProcessedFactoryMap;
use Quanta\Container\Configuration\ProcessingStep;
use Quanta\Container\Configuration\ConfigurationStepInterface;
use Quanta\Container\Configuration\ConfigurationPassInterface;

describe('ProcessingStep', function () {

    beforeEach(function () {

        $this->delegate = mock(ConfigurationStepInterface::class);

    });

    context('when there is no configuration pass', function () {

        beforeEach(function () {

            $this->step = new ProcessingStep($this->delegate->get());

        });

        it('should implement ConfigurationStepInterface', function () {

            expect($this->step)->toBeAnInstanceOf(ConfigurationStepInterface::class);

        });

        describe('->map()', function () {

            it('should return an empty processed factory map from the factory map provided by the delegate', function () {

                $map1 = mock(FactoryMapInterface::class);
                $map2 = mock(FactoryMapInterface::class);

                $this->delegate->map->with($map1)->returns($map2);

                $test = $this->step->map($map1->get());

                expect($test)->toEqual(new ProcessedFactoryMap($map2->get()));

            });

        });

    });

    context('when there is no factory map', function () {

        beforeEach(function () {

            $this->pass1 = mock(ConfigurationPassInterface::class);
            $this->pass2 = mock(ConfigurationPassInterface::class);
            $this->pass3 = mock(ConfigurationPassInterface::class);

            $this->step = new ProcessingStep($this->delegate->get(), ...[
                $this->pass1->get(),
                $this->pass2->get(),
                $this->pass3->get(),
            ]);

        });

        it('should implement ConfigurationStepInterface', function () {

            expect($this->step)->toBeAnInstanceOf(ConfigurationStepInterface::class);

        });

        describe('->map()', function () {

            it('should return a processed factory map from the factory map provided by the delegate', function () {

                $map1 = mock(FactoryMapInterface::class);
                $map2 = mock(FactoryMapInterface::class);

                $this->delegate->map->with($map1)->returns($map2);

                $test = $this->step->map($map1->get());

                expect($test)->toEqual(new ProcessedFactoryMap($map2->get(), ...[
                    $this->pass1->get(),
                    $this->pass2->get(),
                    $this->pass3->get(),
                ]));

            });

        });

    });

});

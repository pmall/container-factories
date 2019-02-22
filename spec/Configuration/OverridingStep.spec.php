<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\MergedFactoryMap;
use Quanta\Container\FactoryMapInterface;
use Quanta\Container\Configuration\OverridingStep;
use Quanta\Container\Configuration\ConfigurationStepInterface;

describe('OverridingStep', function () {

    beforeEach(function () {

        $this->delegate = mock(ConfigurationStepInterface::class);

    });

    context('when there is no factory map', function () {

        beforeEach(function () {

            $this->step = new OverridingStep($this->delegate->get());

        });

        it('should implement ConfigurationStepInterface', function () {

            expect($this->step)->toBeAnInstanceOf(ConfigurationStepInterface::class);

        });

        describe('->map()', function () {

            it('should proxy the delegate with a merged factory map', function () {

                $map1 = mock(FactoryMapInterface::class);
                $map2 = mock(FactoryMapInterface::class);

                $this->delegate->map
                    ->with(new MergedFactoryMap($map1->get()))
                    ->returns($map2);

                $test = $this->step->map($map1->get());

                expect($test)->toBe($map2->get());

            });

        });

    });

    context('when there is no factory map', function () {

        beforeEach(function () {

            $this->map1 = mock(FactoryMapInterface::class);
            $this->map2 = mock(FactoryMapInterface::class);
            $this->map3 = mock(FactoryMapInterface::class);

            $this->step = new OverridingStep($this->delegate->get(), ...[
                $this->map1->get(),
                $this->map2->get(),
                $this->map3->get(),
            ]);

        });

        it('should implement ConfigurationStepInterface', function () {

            expect($this->step)->toBeAnInstanceOf(ConfigurationStepInterface::class);

        });

        describe('->map()', function () {

            it('should proxy the delegate with a merged factory map', function () {

                $map1 = mock(FactoryMapInterface::class);
                $map2 = mock(FactoryMapInterface::class);

                $this->delegate->map->with(new MergedFactoryMap(...[
                    $this->map1->get(),
                    $this->map2->get(),
                    $this->map3->get(),
                    $map1->get(),
                ]))->returns($map2);

                $test = $this->step->map($map1->get());

                expect($test)->toBe($map2->get());

            });

        });

    });

});

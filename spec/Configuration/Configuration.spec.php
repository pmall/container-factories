<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\FactoryMapInterface;
use Quanta\Container\Configuration\Configuration;
use Quanta\Container\Configuration\OverridingStep;
use Quanta\Container\Configuration\ProcessingStep;
use Quanta\Container\Configuration\ConfigurationInterface;
use Quanta\Container\Configuration\ConfigurationStepInterface;
use Quanta\Container\Configuration\ConfigurationPassInterface;

describe('Configuration', function () {

    beforeEach(function () {

        $this->map = mock(FactoryMapInterface::class);

    });

    context('when there is no configuration pass', function () {

        beforeEach(function () {

            $this->configuration = new Configuration($this->map->get());

        });

        it('should implement ConfigurationInterface', function () {

            expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

        });

        describe('->step()', function () {

            it('should return an overriding step with no configuration pass', function () {

                $step = mock(ConfigurationStepInterface::class);

                $test = $this->configuration->step($step->get());

                expect($test)->toEqual(new ProcessingStep(
                    new OverridingStep($step->get(), $this->map->get())
                ));

            });

        });

    });

    context('when there is configuration passes', function () {

        beforeEach(function () {

            $this->pass1 = mock(ConfigurationPassInterface::class);
            $this->pass2 = mock(ConfigurationPassInterface::class);
            $this->pass3 = mock(ConfigurationPassInterface::class);

            $this->configuration = new Configuration($this->map->get(), ...[
                $this->pass1->get(),
                $this->pass2->get(),
                $this->pass3->get(),
            ]);

        });

        it('should implement ConfigurationInterface', function () {

            expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

        });

        describe('->step()', function () {

            it('should return an overriding step with the configuration passes', function () {

                $step = mock(ConfigurationStepInterface::class);

                $test = $this->configuration->step($step->get());

                expect($test)->toEqual(new ProcessingStep(
                    new OverridingStep($step->get(), $this->map->get()),
                    $this->pass1->get(),
                    $this->pass2->get(),
                    $this->pass3->get()
                ));

            });

        });

    });

});

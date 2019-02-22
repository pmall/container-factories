<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\Configuration\MergedConfiguration;
use Quanta\Container\Configuration\ConfigurationInterface;
use Quanta\Container\Configuration\ConfigurationStepInterface;

describe('MergedConfiguration', function () {

    context('when there is no configuration', function () {

        beforeEach(function () {

            $this->configuration = new MergedConfiguration;

        });

        it('should implement ConfigurationInterface', function () {

            expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

        });

        describe('->step()', function () {

            it('should return the given configuration step', function () {

                $step = mock(ConfigurationStepInterface::class);

                $test = $this->configuration->step($step->get());

                expect($test)->toBe($step->get());

            });

        });

    });

    context('when there is at least one configuration', function () {

        beforeEach(function () {

            $this->configuration1 = mock(ConfigurationInterface::class);
            $this->configuration2 = mock(ConfigurationInterface::class);
            $this->configuration3 = mock(ConfigurationInterface::class);

            $this->configuration = new MergedConfiguration(...[
                $this->configuration1->get(),
                $this->configuration2->get(),
                $this->configuration3->get(),
            ]);

        });

        it('should implement ConfigurationInterface', function () {

            expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

        });

        describe('->step()', function () {

            it('should reduce the given configuration step with all the configurations', function () {

                $step1 = mock(ConfigurationStepInterface::class);
                $step2 = mock(ConfigurationStepInterface::class);
                $step3 = mock(ConfigurationStepInterface::class);
                $step4 = mock(ConfigurationStepInterface::class);

                $this->configuration1->step->with($step1)->returns($step2);
                $this->configuration2->step->with($step2)->returns($step3);
                $this->configuration3->step->with($step3)->returns($step4);

                $test = $this->configuration->step($step1->get());

                expect($test)->toBe($step4->get());

            });

        });

    });

});

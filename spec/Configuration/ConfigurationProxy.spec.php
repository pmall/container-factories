<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\Configuration\ConfigurationProxy;
use Quanta\Container\Configuration\ConfigurationInterface;
use Quanta\Container\Configuration\ConfigurationStepInterface;
use Quanta\Container\Configuration\ConfigurationSourceInterface;

describe('ConfigurationProxy', function () {

    beforeEach(function () {

        $this->source = mock(ConfigurationSourceInterface::class);

        $this->configuration = new ConfigurationProxy($this->source->get());

    });

    it('should implement ConfigurationInterface', function () {

        expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

    });

    describe('->step()', function () {

        it('should proxy the configuration provided by the source', function () {

            $step1 = mock(ConfigurationStepInterface::class);
            $step2 = mock(ConfigurationStepInterface::class);

            $configuration = mock(ConfigurationInterface::class);

            $this->source->configuration->returns($configuration);

            $configuration->step->with($step1)->returns($step2);

            $test = $this->configuration->step($step1->get());

            expect($test)->toBe($step2->get());

        });

    });

});

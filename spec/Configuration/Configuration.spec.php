<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\Configuration\Configuration;
use Quanta\Container\Configuration\ConfigurationInterface;
use Quanta\Container\Configuration\ConfigurationUnitInterface;

describe('Configuration', function () {

    beforeEach(function () {

        $this->unit = mock(ConfigurationUnitInterface::class);

        $this->configuration = new Configuration($this->unit->get());

    });

    it('should implement ConfigurationInterface', function () {

        expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

    });

    describe('->unit()', function () {

        it('should return the configuration unit', function () {

            $test = $this->configuration->unit();

            expect($test)->toEqual($this->unit->get());

        });

    });

});

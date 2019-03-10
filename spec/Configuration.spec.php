<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\Configuration;
use Quanta\Container\ConfiguredFactoryMap;
use Quanta\Container\ConfigurationInterface;
use Quanta\Container\Maps\FactoryMapInterface;
use Quanta\Container\Passes\ProcessingPassInterface;

describe('Configuration', function () {

    beforeEach(function () {

        $this->map = new ConfiguredFactoryMap(
            mock(FactoryMapInterface::class)->get(),
            mock(ProcessingPassInterface::class)->get()
        );

        $this->configuration = new Configuration($this->map);

    });

    it('should implement ConfigurationInterface', function () {

        expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

    });

    describe('->map()', function () {

        it('should return the configured factory map', function () {

            $test = $this->configuration->map();

            expect($test)->toBe($this->map);

        });

    });

});

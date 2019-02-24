<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\FactoryMapInterface;
use Quanta\Container\ProcessedFactoryMap;
use Quanta\Container\Configuration\Configuration;
use Quanta\Container\Configuration\ConfigurationInterface;

describe('Configuration', function () {

    beforeEach(function () {

        $this->map = new ProcessedFactoryMap(
            mock(FactoryMapInterface::class)->get()
        );

        $this->configuration = new Configuration($this->map);

    });

    it('should implement ConfigurationInterface', function () {

        expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

    });

    describe('->map()', function () {

        it('should return the processed factory map', function () {

            $test = $this->configuration->map();

            expect($test)->toBe($this->map);

        });

    });

});

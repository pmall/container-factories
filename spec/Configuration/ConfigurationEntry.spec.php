<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\FactoryMapInterface;
use Quanta\Container\Configuration\ConfigurationEntry;
use Quanta\Container\Configuration\Passes\ProcessingPassInterface;

describe('ConfigurationEntry', function () {

    beforeEach(function () {

        $this->map = mock(FactoryMapInterface::class);
        $this->pass = mock(ProcessingPassInterface::class);

        $this->configuration = new ConfigurationEntry(
            $this->map->get(),
            $this->pass->get()
        );

    });

    describe('->map()', function () {

        it('should return the factory map', function () {

            $test = $this->configuration->map();

            expect($test)->toBe($this->map->get());

        });

    });

    describe('->pass()', function () {

        it('should return the processing pass', function () {

            $test = $this->configuration->pass();

            expect($test)->toBe($this->pass->get());

        });

    });

});

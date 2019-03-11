<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\Configuration;
use Quanta\Container\ConfigurationEntry;
use Quanta\Container\ConfigurationEntryInterface;
use Quanta\Container\Maps\FactoryMapInterface;
use Quanta\Container\Passes\ProcessingPassInterface;

describe('ConfigurationEntry', function () {

    beforeEach(function () {

        $this->configuration = new Configuration(
            mock(FactoryMapInterface::class)->get(),
            mock(ProcessingPassInterface::class)->get()
        );

        $this->entry = new ConfigurationEntry($this->configuration);

    });

    it('should implement ConfigurationEntryInterface', function () {

        expect($this->entry)->toBeAnInstanceOf(ConfigurationEntryInterface::class);

    });

    describe('->configuration()', function () {

        it('should return the configuration', function () {

            $test = $this->entry->configuration();

            expect($test)->toBe($this->configuration);

        });

    });

});

<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\ConfigurationSource;
use Quanta\Container\ConfigurationEntryInterface;
use Quanta\Container\ConfigurationSourceInterface;

describe('ConfigurationSource', function () {

    beforeEach(function () {

        $this->entry = mock(ConfigurationEntryInterface::class);

        $this->source = new ConfigurationSource($this->entry->get());

    });

    it('should implement ConfigurationSourceInterface', function () {

        expect($this->source)->toBeAnInstanceOf(ConfigurationSourceInterface::class);

    });

    describe('->entry()', function () {

        it('should return the configuration entry', function () {

            $test = $this->source->entry();

            expect($test)->toBe($this->entry->get());

        });

    });

});

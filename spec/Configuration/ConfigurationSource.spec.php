<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\Configuration\ConfigurationSource;
use Quanta\Container\Configuration\ConfigurationInterface;
use Quanta\Container\Configuration\ConfigurationSourceInterface;

describe('ConfigurationSource', function () {

    beforeEach(function () {

        $this->counfiguration = mock(ConfigurationInterface::class);

        $this->source = new ConfigurationSource($this->counfiguration->get());

    });

    it('should implement ConfigurationSourceInterface', function () {

        expect($this->source)->toBeAnInstanceOf(ConfigurationSourceInterface::class);

    });

    describe('->configuration()', function () {

        it('should return the configuration', function () {

            $test = $this->source->configuration();

            expect($test)->toBe($this->counfiguration->get());

        });

    });

});

<?php

use function Eloquent\Phony\Kahlan\mock;

use Interop\Container\ServiceProviderInterface;

use Quanta\Container\Configuration;
use Quanta\Container\ConfigurationInterface;
use Quanta\Container\ExternalServiceProvider;

describe('Configuration', function () {

    beforeEach(function () {

        $this->provider1 = mock(ServiceProviderInterface::class);
        $this->provider2 = mock(ServiceProviderInterface::class);
        $this->provider3 = mock(ServiceProviderInterface::class);

        $this->configuration = new Configuration(...[
            $this->provider1->get(),
            $this->provider2->get(),
            $this->provider3->get(),
        ]);

    });

    it('should implement ConfigurationInterface', function () {

        expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

    });

    describe('->providers()', function () {

        it('should return an array of ExternalServiceProvider instances from the service providers', function () {

            $test = $this->configuration->providers();

            expect($test)->toBeAn('array');
            expect($test)->toHaveLength(3);
            expect($test[0])->toEqual(new ExternalServiceProvider($this->provider1->get()));
            expect($test[1])->toEqual(new ExternalServiceProvider($this->provider2->get()));
            expect($test[2])->toEqual(new ExternalServiceProvider($this->provider3->get()));

        });

    });

});

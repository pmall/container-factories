<?php

use function Eloquent\Phony\Kahlan\mock;

use Interop\Container\ServiceProviderInterface;

use Quanta\Container\Configuration\ConfigurationInterface;
use Quanta\Container\Configuration\ServiceProviderCollection;
use Quanta\Container\Configuration\ServiceProviderConfigurationEntry;

describe('ServiceProviderCollection', function () {

    beforeEach(function () {

        $this->provider1 = mock(ServiceProviderInterface::class);
        $this->provider2 = mock(ServiceProviderInterface::class);
        $this->provider3 = mock(ServiceProviderInterface::class);

        $this->configuration = new ServiceProviderCollection(...[
            $this->provider1->get(),
            $this->provider2->get(),
            $this->provider3->get(),
        ]);

    });

    it('should implement ConfigurationInterface', function () {

        expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

    });

    describe('->entries()', function () {

        it('should return an array of service provider configuration entries from the service providers', function () {

            $test = $this->configuration->entries();

            expect($test)->toBeAn('array');
            expect($test)->toHaveLength(3);
            expect($test[0])->toEqual(new ServiceProviderConfigurationEntry($this->provider1->get()));
            expect($test[1])->toEqual(new ServiceProviderConfigurationEntry($this->provider2->get()));
            expect($test[2])->toEqual(new ServiceProviderConfigurationEntry($this->provider3->get()));

        });

    });

});

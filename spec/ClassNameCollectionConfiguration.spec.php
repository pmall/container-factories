<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\ExternalServiceProvider;
use Quanta\Container\ConfigurationInterface;
use Quanta\Container\ClassNameCollectionConfiguration;

use Quanta\Utils\ClassNameCollectionInterface;

require_once __DIR__ . '/.test/namespace1.php';
require_once __DIR__ . '/.test/namespace2.php';

describe('ClassNameCollectionConfiguration', function () {

    beforeEach(function () {

        $this->collection = mock(ClassNameCollectionInterface::class);

        $this->configuration = new ClassNameCollectionConfiguration($this->collection->get());

    });

    it('should implement ConfigurationInterface', function () {

        expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

    });

    describe('->providers()', function () {

        it('should return an array of ExternalServiceProvider instances from the ServiceProviderInterface implementation class names returned by the collection ->classes() method', function () {

            $this->collection->classes->returns([
                Test1\TestClass1::class,
                Test1\TestClass2::class,
                Test1\ServiceProvider1::class,
                Test1\ServiceProvider2::class,
                Test1\ServiceProvider3::class,
                Test2\TestClass1::class,
                Test2\TestClass2::class,
                Test2\ServiceProvider1::class,
                Test2\ServiceProvider2::class,
                Test2\ServiceProvider3::class,
            ]);

            $test = $this->configuration->providers();

            expect($test)->toEqual([
                new ExternalServiceProvider(new Test1\ServiceProvider1),
                new ExternalServiceProvider(new Test1\ServiceProvider2),
                new ExternalServiceProvider(new Test1\ServiceProvider3),
                new ExternalServiceProvider(new Test2\ServiceProvider1),
                new ExternalServiceProvider(new Test2\ServiceProvider2),
                new ExternalServiceProvider(new Test2\ServiceProvider3),
            ]);

        });

        it('should not try to instantiate ServiceProviderInterface', function () {

            $this->collection->classes->returns([
                Test1\ServiceProvider1::class,
                Test1\ServiceProvider2::class,
                Test1\ServiceProvider3::class,
                Test2\ServiceProvider1::class,
                Test2\ServiceProvider2::class,
                Test2\ServiceProvider3::class,
                Interop\Container\ServiceProviderInterface::class,
            ]);

            expect([$this->configuration, 'providers'])->not->toThrow();

        });

    });

});

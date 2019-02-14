<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Utils\Psr4Namespace;
use Quanta\Utils\VendorDirectory;
use Quanta\Utils\ClassNameCollection;
use Quanta\Utils\ClassNameCollectionInterface;
use Quanta\Utils\WhitelistedClassNameCollection;
use Quanta\Utils\BlacklistedClassNameCollection;
use Quanta\Container\Configuration\ConfigurationInterface;
use Quanta\Container\Configuration\ServiceProviderAdapter;
use Quanta\Container\Configuration\ServiceProviderClassNameCollection;

require_once __DIR__ . '/../.test/namespace1.php';
require_once __DIR__ . '/../.test/namespace2.php';

describe('ServiceProviderClassNameCollection::fromClassNames()', function () {

    it('should return a service provider class name collection using the given class names', function () {

        $classes = [
            Test1\ServiceProvider1::class,
            Test1\ServiceProvider2::class,
            Test1\ServiceProvider3::class,
            Test2\ServiceProvider1::class,
            Test2\ServiceProvider2::class,
            Test2\ServiceProvider3::class,
        ];

        $test = ServiceProviderClassNameCollection::fromClassNames(...$classes);

        expect($test)->toEqual(new ServiceProviderClassNameCollection(
            new ClassNameCollection(...$classes)
        ));

    });

});

describe('ServiceProviderClassNameCollection::fromPsr4Namespace()', function () {

    it('should return a service provider class name collection using the given Psr-4 namespace', function () {

        $test = ServiceProviderClassNameCollection::fromPsr4Namespace('NS', 'path');

        expect($test)->toEqual(new ServiceProviderClassNameCollection(
            new Psr4Namespace('NS', 'path')
        ));

    });

});

describe('ServiceProviderClassNameCollection::fromVendorDirectory()', function () {

    it('should return a service provider class name collection using the given vendor directory', function () {

        $test = ServiceProviderClassNameCollection::fromVendorDirectory('path');

        expect($test)->toEqual(new ServiceProviderClassNameCollection(
            new VendorDirectory('path')
        ));

    });

});

describe('ServiceProviderClassNameCollection', function () {

    beforeEach(function () {

        $this->collection = mock(ClassNameCollectionInterface::class);

        $this->configuration = new ServiceProviderClassNameCollection($this->collection->get());

    });

    it('should implement ConfigurationInterface', function () {

        expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

    });

    describe('->withWhitelist()', function () {

        it('should return a new service provider class name collection using the given whitelist patterns', function () {

            $patterns = ['/pattern1/', '/pattern2/', '/pattern3/'];

            $test = $this->configuration->withWhitelist(...$patterns);

            expect($test)->toEqual(new ServiceProviderClassNameCollection(
                new WhitelistedClassNameCollection(
                    $this->collection->get(), ...$patterns
                )
            ));

        });

    });

    describe('->withBlacklist()', function () {

        it('should return a new service provider class name collection using the given blacklist patterns', function () {

            $patterns = ['/pattern1/', '/pattern2/', '/pattern3/'];

            $test = $this->configuration->withBlacklist(...$patterns);

            expect($test)->toEqual(new ServiceProviderClassNameCollection(
                new BlacklistedClassNameCollection(
                    $this->collection->get(), ...$patterns
                )
            ));

        });

    });

    describe('->entries()', function () {

        it('should return an array of service provider configuration entries from the names of classes implementing ServiceProviderInterface returned by the collection ->classes() method', function () {

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

            $test = $this->configuration->entries();

            expect($test)->toEqual([
                new ServiceProviderAdapter(new Test1\ServiceProvider1),
                new ServiceProviderAdapter(new Test1\ServiceProvider2),
                new ServiceProviderAdapter(new Test1\ServiceProvider3),
                new ServiceProviderAdapter(new Test2\ServiceProvider1),
                new ServiceProviderAdapter(new Test2\ServiceProvider2),
                new ServiceProviderAdapter(new Test2\ServiceProvider3),
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

            expect([$this->configuration, 'entries'])->not->toThrow();

        });

    });

});

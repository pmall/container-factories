<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\ServiceProviderCollection;
use Quanta\Container\ServiceProviderCollectionInterface;

use Quanta\Utils\ClassNameCollectionInterface;

require_once __DIR__ . '/test/namespace1.php';
require_once __DIR__ . '/test/namespace2.php';

describe('ServiceProviderCollection', function () {

    beforeEach(function () {

        $this->collection = mock(ClassNameCollectionInterface::class);

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
            Quanta\Container\ServiceProvider::class,
            Interop\Container\ServiceProviderInterface::class,
        ]);

    });

    it('should implement ServiceProviderCollectionInterface', function () {

        $test = new ServiceProviderCollection($this->collection->get());

        expect($test)->toBeAnInstanceOf(ServiceProviderCollectionInterface::class);

    });

    describe('->providers()', function () {

        context('when there is no pattern', function () {

            it('should return an array of all implementations of ServiceProviderInterface provided by the collection', function () {

                $configuration = new ServiceProviderCollection($this->collection->get());

                $test = $configuration->providers();

                expect($test)->toBeAn('array');
                expect($test)->toHaveLength(6);
                expect($test[0])->toEqual(new Test1\ServiceProvider1);
                expect($test[1])->toEqual(new Test1\ServiceProvider2);
                expect($test[2])->toEqual(new Test1\ServiceProvider3);
                expect($test[3])->toEqual(new Test2\ServiceProvider1);
                expect($test[4])->toEqual(new Test2\ServiceProvider2);
                expect($test[5])->toEqual(new Test2\ServiceProvider3);

            });

        });

        context('when there is a pattern', function () {

            context('when the blacklist is empty', function () {

                it('should return an array of all implementations of ServiceProviderInterface provided by the collection and matching the pattern', function () {

                    $configuration = new ServiceProviderCollection(...[
                        $this->collection->get(),
                        '/^Test1.+?[1-2]$/'
                    ]);

                    $test = $configuration->providers();

                    expect($test)->toBeAn('array');
                    expect($test)->toHaveLength(2);
                    expect($test[0])->toEqual(new Test1\ServiceProvider1);
                    expect($test[1])->toEqual(new Test1\ServiceProvider2);

                });

            });

            context('when the blacklist is not empty', function () {

                it('should return an array of all implementations of ServiceProviderInterface provided by the collection, matching the pattern and not matching any blackist pattern', function () {

                    $configuration = new ServiceProviderCollection(...[
                        $this->collection->get(),
                        '/^Test1/',
                        'Test1\\*1',
                        'Test1\\*3',
                    ]);

                    $test = $configuration->providers();

                    expect($test)->toBeAn('array');
                    expect($test)->toHaveLength(1);
                    expect($test[0])->toEqual(new Test1\ServiceProvider2);

                });

            });

        });

    });

});

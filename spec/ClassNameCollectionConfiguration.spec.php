<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\ConfigurationInterface;
use Quanta\Container\ClassNameCollectionConfiguration;

use Quanta\Utils\ClassNameCollectionInterface;

require_once __DIR__ . '/.test/namespace1.php';
require_once __DIR__ . '/.test/namespace2.php';

describe('ClassNameCollectionConfiguration', function () {

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

    context('when there is no pattern', function () {

        beforeEach(function () {

            $this->configuration = new ClassNameCollectionConfiguration($this->collection->get());

        });

        it('should implement ConfigurationInterface', function () {

            expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

        });

        describe('->providers()', function () {

            it('should return an array of ServiceProviderInterface implementations from the class names provided by the collection', function () {

                $test = $this->configuration->providers();

                expect($test)->toEqual([
                    new Test1\ServiceProvider1,
                    new Test1\ServiceProvider2,
                    new Test1\ServiceProvider3,
                    new Test2\ServiceProvider1,
                    new Test2\ServiceProvider2,
                    new Test2\ServiceProvider3,
                ]);

            });

        });

    });

    context('when there is a pattern', function () {

        context('when there is no blackist pattern', function () {

            beforeEach(function () {

                $this->configuration = new ClassNameCollectionConfiguration(...[
                    $this->collection->get(),
                    '/^Test1.+?(1|3)$/',
                ]);

            });

            it('should implement ConfigurationInterface', function () {

                expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

            });

            it('should return an array of ServiceProviderInterface implementations from the class names provided by the collection and matching the pattern', function () {

                $test = $this->configuration->providers();

                expect($test)->toEqual([
                    new Test1\ServiceProvider1,
                    new Test1\ServiceProvider3,
                ]);

            });

        });

        context('when there is blacklist patterns', function () {

            beforeEach(function () {

                $this->configuration = new ClassNameCollectionConfiguration(...[
                    $this->collection->get(),
                    '/^Test1/',
                    'Test1\\*1',
                    'Test1\\*3',
                ]);

            });

            it('should implement ConfigurationInterface', function () {

                expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

            });

            it('should return an array of ServiceProviderInterface implementations from the class names provided by the collection and matching the pattern but not any blacklist pattern', function () {

                $test = $this->configuration->providers();

                expect($test)->toEqual([
                    new Test1\ServiceProvider2,
                ]);

            });

        });

    });

});

<?php

use function Eloquent\Phony\Kahlan\mock;

use Test\TestFactory;

use Interop\Container\ServiceProviderInterface;

use Quanta\Container\FactoryMapInterface;
use Quanta\Container\ServiceProviderCollectionInterface;
use Quanta\Container\ServiceProviderCollectionFactoryMap;

use Quanta\Container\Factories\Extension;

require_once __DIR__ . '/test/classes.php';

describe('ServiceProviderCollectionFactoryMap', function () {

    it('should implement FactoryMapInterface', function () {

        expect(new ServiceProviderCollectionFactoryMap)->toBeAnInstanceOf(FactoryMapInterface::class);

    });

    describe('->factories()', function () {

        context('when there is no collection', function () {

            it('should return an empty array', function () {

                $map = new ServiceProviderCollectionFactoryMap;

                $test = $map->factories();

                expect($test)->toEqual([]);

            });

        });

        context('when there is at least one collection', function () {

            it('should return an array of factories from all the service providers provided by the collections', function () {

                $provider1 = mock(ServiceProviderInterface::class);
                $provider2 = mock(ServiceProviderInterface::class);
                $provider3 = mock(ServiceProviderInterface::class);
                $provider4 = mock(ServiceProviderInterface::class);

                $provider1->getFactories->returns([
                    'id1' => new TestFactory('f11'),
                    'id2' => new TestFactory('f12'),
                    'id3' => new TestFactory('f13'),
                    'id4' => new TestFactory('f14'),
                ]);

                $provider1->getExtensions->returns([
                    'id3' => new TestFactory('e13'),
                ]);

                $provider2->getFactories->returns([
                    'id2' => new TestFactory('f22'),
                    'id3' => new TestFactory('f23'),
                    'id4' => new TestFactory('f24'),
                ]);

                $provider2->getExtensions->returns([
                    'id4' => new TestFactory('e24'),
                ]);

                $provider3->getFactories->returns([
                    'id3' => new TestFactory('f33'),
                    'id4' => new TestFactory('f34'),
                ]);

                $provider3->getExtensions->returns([
                    'id1' => new TestFactory('e31'),
                ]);

                $provider4->getFactories->returns([
                    'id4' => new TestFactory('f44'),
                ]);

                $provider4->getExtensions->returns([
                    'id2' => new TestFactory('e42'),
                ]);

                $collection1 = mock(ServiceProviderCollectionInterface::class);
                $collection2 = mock(ServiceProviderCollectionInterface::class);
                $collection3 = mock(ServiceProviderCollectionInterface::class);

                $collection1->providers->returns([
                    $provider1->get(),
                    $provider2->get(),
                ]);

                $collection2->providers->returns([
                    $provider3->get(),
                ]);

                $collection3->providers->returns([
                    $provider4->get(),
                ]);

                $map = new ServiceProviderCollectionFactoryMap(...[
                    $collection1->get(),
                    $collection2->get(),
                    $collection3->get(),
                ]);

                $test = $map->factories();

                expect($test)->toBeAn('array');
                expect($test)->toHaveLength(4);
                expect($test['id1'])->toEqual(new Extension(new TestFactory('f11'), new TestFactory('e31')));
                expect($test['id2'])->toEqual(new Extension(new TestFactory('f22'), new TestFactory('e42')));
                expect($test['id3'])->toEqual(new Extension(new TestFactory('f33'), new TestFactory('e13')));
                expect($test['id4'])->toEqual(new Extension(new TestFactory('f44'), new TestFactory('e24')));

            });

        });

    });

});

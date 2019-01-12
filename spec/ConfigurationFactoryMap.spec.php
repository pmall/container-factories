<?php

use function Eloquent\Phony\Kahlan\mock;

use Test\TestFactory;

use Quanta\Container\FactoryMap;
use Quanta\Container\FactoryMapInterface;
use Quanta\Container\ConfigurationInterface;
use Quanta\Container\ConfigurationFactoryMap;
use Quanta\Container\TaggedServiceProviderInterface;

use Quanta\Container\Factories\Extension;

require_once __DIR__ . '/.test/classes.php';

describe('ConfigurationFactoryMap', function () {

    context('when there is no configuration pass', function () {

        beforeEach(function () {

            $this->configuration = mock(ConfigurationInterface::class);

            $this->map = new ConfigurationFactoryMap($this->configuration->get());

        });

        it('should implement FactoryMapInterface', function () {

            expect($this->map)->toBeAnInstanceOf(FactoryMapInterface::class);

        });

        describe('->factories()', function () {

            context('when no tagged service provider is provided by the configuration', function () {

                it('should return an empty array', function () {

                    $this->configuration->providers->returns([]);

                    $test = $this->map->factories();

                    expect($test)->toEqual([]);

                });

            });

            context('when at least one tagged service provider is provided by the configuration', function () {

                beforeEach(function () {

                    $this->provider1 = mock(TaggedServiceProviderInterface::class);
                    $this->provider2 = mock(TaggedServiceProviderInterface::class);
                    $this->provider3 = mock(TaggedServiceProviderInterface::class);

                    $this->configuration->providers->returns([
                        $this->provider1->get(),
                        $this->provider2->get(),
                        $this->provider3->get(),
                    ]);

                    $this->provider1->factories->returns(new FactoryMap([
                        'id1' => new TestFactory('f11'),
                        'id2' => new TestFactory('f12'),
                        'id3' => new TestFactory('f13'),
                    ]));

                    $this->provider2->factories->returns(new FactoryMap([
                        'id2' => new TestFactory('f22'),
                        'id3' => new TestFactory('f23'),
                        'id4' => new TestFactory('f24'),
                    ]));

                    $this->provider3->factories->returns(new FactoryMap([
                        'id3' => new TestFactory('f33'),
                        'id4' => new TestFactory('f34'),
                        'id5' => new TestFactory('f35'),
                    ]));

                });

                it('should merge the factories provided by the service providers', function () {

                    $test = $this->map->factories();

                    expect($test)->toBeAn('array');
                    expect($test)->toHaveLength(5);
                    expect($test['id1'])->toEqual(new TestFactory('f11'));
                    expect($test['id2'])->toEqual(new TestFactory('f22'));
                    expect($test['id3'])->toEqual(new TestFactory('f33'));
                    expect($test['id4'])->toEqual(new TestFactory('f34'));
                    expect($test['id5'])->toEqual(new TestFactory('f35'));

                });

                it('should extend the factories with the extensions provided by the service providers', function () {

                    $this->provider1->extensions->returns(new FactoryMap([
                        'id2' => new TestFactory('e12'),
                        'id3' => new TestFactory('e13'),
                    ]));

                    $this->provider2->extensions->returns(new FactoryMap([
                        'id1' => new TestFactory('e21'),
                        'id3' => new TestFactory('e23'),
                    ]));

                    $this->provider3->extensions->returns(new FactoryMap([
                        'id1' => new TestFactory('e31'),
                        'id2' => new TestFactory('e32'),
                    ]));

                    $test = $this->map->factories();

                    expect($test)->toBeAn('array');
                    expect($test)->toHaveLength(5);
                    expect($test['id1'])->toEqual(new Extension(
                        new Extension(new TestFactory('f11'), new TestFactory('e21')), new TestFactory('e31')
                    ));
                    expect($test['id2'])->toEqual(new Extension(
                        new Extension(new TestFactory('f22'), new TestFactory('e12')), new TestFactory('e32')
                    ));
                    expect($test['id3'])->toEqual(new Extension(
                        new Extension(new TestFactory('f33'), new TestFactory('e13')), new TestFactory('e23')
                    ));

                });

                it('should return extensions with no corresponding factory', function () {

                    $this->provider1->extensions->returns(new FactoryMap([
                        'id6' => new TestFactory('e16'),
                    ]));

                    $this->provider2->extensions->returns(new FactoryMap([
                        'id6' => new TestFactory('e26'),
                    ]));

                    $this->provider3->extensions->returns(new FactoryMap([
                        'id6' => new TestFactory('e36'),
                    ]));

                    $test = $this->map->factories();

                    expect($test)->toBeAn('array');
                    expect($test)->toHaveLength(6);
                    expect($test['id6'])->toEqual(new Extension(
                        new Extension(new TestFactory('e16'), new TestFactory('e26')), new TestFactory('e36')
                    ));

                });

            });

        });

    });

});

<?php

use function Eloquent\Phony\Kahlan\mock;

use Test\TestFactory;

use Quanta\Container\Metadata;
use Quanta\Container\FactoryMap;
use Quanta\Container\FactoryMapInterface;
use Quanta\Container\ConfigurationInterface;
use Quanta\Container\ConfigurationFactoryMap;
use Quanta\Container\ConfigurationEntryInterface;
use Quanta\Container\Passes\ConfigurationPassInterface;

use Quanta\Container\Factories\Extension;

require_once __DIR__ . '/.test/classes.php';

describe('ConfigurationFactoryMap', function () {

    context('when there is no configuration', function () {

        beforeEach(function () {

            $this->map = new ConfigurationFactoryMap;

        });

        it('should implement FactoryMapInterface', function () {

            expect($this->map)->toBeAnInstanceOf(FactoryMapInterface::class);

        });

        describe('->factories()', function () {

            it('should return an empty array', function () {

                $test = $this->map->factories();

                expect($test)->toEqual([]);

            });

        });

    });

    context('when there is at least one configuration', function () {

        beforeEach(function () {

            $this->configuration1 = mock(ConfigurationInterface::class);
            $this->configuration2 = mock(ConfigurationInterface::class);

            $this->map = new ConfigurationFactoryMap(...[
                $this->configuration1->get(),
                $this->configuration2->get(),
            ]);

        });

        it('should implement FactoryMapInterface', function () {

            expect($this->map)->toBeAnInstanceOf(FactoryMapInterface::class);

        });

        describe('->factories()', function () {

            context('when no configuration entry is provided by the configurations', function () {

                it('should return an empty array', function () {

                    $this->configuration1->entries->returns([]);
                    $this->configuration2->entries->returns([]);

                    $test = $this->map->factories();

                    expect($test)->toEqual([]);

                });

            });

            context('when at least one configuration entry is provided by the configurations', function () {

                beforeEach(function () {

                    // setup the configuration entries.
                    $this->entry1 = mock(ConfigurationEntryInterface::class);
                    $this->entry2 = mock(ConfigurationEntryInterface::class);
                    $this->entry3 = mock(ConfigurationEntryInterface::class);

                    $this->configuration1->entries->returns([
                        $this->entry1->get(),
                    ]);

                    $this->configuration2->entries->returns([
                        'test' => $this->entry2->get(),
                        $this->entry3->get(),
                    ]);

                    // setup the factories.
                    $this->entry1->factories->returns(new FactoryMap([
                        'id1' => new TestFactory('f11'),
                        'id2' => new TestFactory('f12'),
                        'id3' => new TestFactory('f13'),
                    ]));

                    $this->entry2->factories->returns(new FactoryMap([
                        'id2' => new TestFactory('f22'),
                        'id3' => new TestFactory('f23'),
                        'id4' => new TestFactory('f24'),
                    ]));

                    $this->entry3->factories->returns(new FactoryMap([
                        'id3' => new TestFactory('f33'),
                        'id4' => new TestFactory('f34'),
                        'id5' => new TestFactory('f35'),
                    ]));

                    $this->entry1->extensions->returns(new FactoryMap([
                        'id2' => new TestFactory('e12'),
                        'id3' => new TestFactory('e13'),
                        'id6' => new TestFactory('e16'),
                    ]));

                    $this->entry2->extensions->returns(new FactoryMap([
                        'id1' => new TestFactory('e21'),
                        'id3' => new TestFactory('e23'),
                        'id6' => new TestFactory('e26'),
                    ]));

                    $this->entry3->extensions->returns(new FactoryMap([
                        'id1' => new TestFactory('e31'),
                        'id2' => new TestFactory('e32'),
                        'id6' => new TestFactory('e36'),
                    ]));

                    // the processed factories.
                    $this->factories = [
                        'id1' => new Extension(
                            new Extension(new TestFactory('f11'), new TestFactory('e21')),
                            new TestFactory('e31')
                        ),
                        'id2' => new Extension(
                            new Extension(new TestFactory('f22'), new TestFactory('e12')),
                            new TestFactory('e32')
                        ),
                        'id3' => new Extension(
                            new Extension(new TestFactory('f33'), new TestFactory('e13')),
                            new TestFactory('e23')
                        ),
                        'id4' => new TestFactory('f34'),
                        'id5' => new TestFactory('f35'),
                        'id6' => new Extension(
                            new Extension(new TestFactory('e16'), new TestFactory('e26')),
                            new TestFactory('e36')
                        ),
                    ];

                });

                context('when no compilation pass is provided by the configurations', function () {

                    it('should return the processed factories', function () {

                        $test = $this->map->factories();

                        expect($test)->toEqual($this->factories);

                    });

                });

                context('when at least one configuration pass is provided by the configurations', function () {

                    it('should return the processed factories merged with the ones provided by the configuration passes', function () {

                        // setup the metadata.
                        $this->entry1->metadata->returns(['id1' => ['k1' => 'm1']]);
                        $this->entry2->metadata->returns(['id2' => ['k2' => 'm2']]);
                        $this->entry3->metadata->returns(['id3' => ['k3' => 'm3']]);

                        $metadata = new Metadata(...[
                            ['id1' => ['k1' => 'm1']],
                            ['id2' => ['k2' => 'm2']],
                            ['id3' => ['k3' => 'm3']],
                        ]);

                        // setup the passes.
                        $pass1 = mock(ConfigurationPassInterface::class);
                        $pass2 = mock(ConfigurationPassInterface::class);
                        $pass3 = mock(ConfigurationPassInterface::class);
                        $pass4 = mock(ConfigurationPassInterface::class);

                        $this->entry1->passes->returns([
                            $pass1->get(),
                        ]);

                        $this->entry2->passes->returns([
                            $pass2->get(),
                        ]);

                        $this->entry3->passes->returns([
                            $pass3->get(),
                            $pass4->get(),
                        ]);

                        $pass1->factories->with($this->factories, $metadata)->returns([
                            'id1' => new TestFactory('pf11'),
                            'id2' => new TestFactory('pf12'),
                        ]);

                        $pass2->factories->with($this->factories, $metadata)->returns([
                            'id2' => new TestFactory('pf22'),
                            'id3' => new TestFactory('pf23'),
                        ]);

                        $pass3->factories->with($this->factories, $metadata)->returns([
                            'id7' => new TestFactory('pf37'),
                        ]);

                        $pass4->factories->with($this->factories, $metadata)->returns([
                            'id8' => new TestFactory('pf48'),
                            'id9' => new TestFactory('pf49'),
                        ]);

                        $test = $this->map->factories();

                        expect($test)->toEqual(array_merge($this->factories, [
                            'id1' => new TestFactory('pf11'),
                            'id2' => new TestFactory('pf22'),
                            'id3' => new TestFactory('pf23'),
                            'id7' => new TestFactory('pf37'),
                            'id8' => new TestFactory('pf48'),
                            'id9' => new TestFactory('pf49'),
                        ]));

                    });

                });

            });

        });

    });

});

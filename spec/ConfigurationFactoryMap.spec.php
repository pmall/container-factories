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

    beforeEach(function () {

        $this->configuration = mock(ConfigurationInterface::class);

    });

    context('when there is no configuration pass', function () {

        beforeEach(function () {

            $this->map = new ConfigurationFactoryMap($this->configuration->get());

        });

        it('should implement FactoryMapInterface', function () {

            expect($this->map)->toBeAnInstanceOf(FactoryMapInterface::class);

        });

        describe('->factories()', function () {

            context('when no configuration entry is provided by the configuration', function () {

                it('should return an empty array', function () {

                    $this->configuration->entries->returns([]);

                    $test = $this->map->factories();

                    expect($test)->toEqual([]);

                });

            });

            context('when at least one configuration entry is provided by the configuration', function () {

                beforeEach(function () {

                    $this->entry1 = mock(ConfigurationEntryInterface::class);
                    $this->entry2 = mock(ConfigurationEntryInterface::class);
                    $this->entry3 = mock(ConfigurationEntryInterface::class);

                    $this->configuration->entries->returns([
                        $this->entry1->get(),
                        $this->entry2->get(),
                        $this->entry3->get(),
                    ]);

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

                });

                it('should merge the factories provided by the configuration entries', function () {

                    $test = $this->map->factories();

                    expect($test)->toBeAn('array');
                    expect($test)->toHaveLength(5);
                    expect($test['id1'])->toEqual(new TestFactory('f11'));
                    expect($test['id2'])->toEqual(new TestFactory('f22'));
                    expect($test['id3'])->toEqual(new TestFactory('f33'));
                    expect($test['id4'])->toEqual(new TestFactory('f34'));
                    expect($test['id5'])->toEqual(new TestFactory('f35'));

                });

                it('should extend the factories with the extensions provided by the configuration entries', function () {

                    $this->entry1->extensions->returns(new FactoryMap([
                        'id2' => new TestFactory('e12'),
                        'id3' => new TestFactory('e13'),
                    ]));

                    $this->entry2->extensions->returns(new FactoryMap([
                        'id1' => new TestFactory('e21'),
                        'id3' => new TestFactory('e23'),
                    ]));

                    $this->entry3->extensions->returns(new FactoryMap([
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

                    $this->entry1->extensions->returns(new FactoryMap([
                        'id6' => new TestFactory('e16'),
                    ]));

                    $this->entry2->extensions->returns(new FactoryMap([
                        'id6' => new TestFactory('e26'),
                    ]));

                    $this->entry3->extensions->returns(new FactoryMap([
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

    context('when there is at least one configuration pass', function () {

        beforeEach(function () {

            $this->pass1 = mock(ConfigurationPassInterface::class);
            $this->pass2 = mock(ConfigurationPassInterface::class);
            $this->pass3 = mock(ConfigurationPassInterface::class);

            $this->map = new ConfigurationFactoryMap($this->configuration->get(), ...[
                $this->pass1->get(),
                $this->pass2->get(),
                $this->pass3->get(),
            ]);

        });

        it('should implement FactoryMapInterface', function () {

            expect($this->map)->toBeAnInstanceOf(FactoryMapInterface::class);

        });

        describe('->factories()', function () {

            it('should merge the factory maps returned by all the configuration passes ->factories() methods', function () {

                $entry1 = mock(ConfigurationEntryInterface::class);
                $entry2 = mock(ConfigurationEntryInterface::class);

                $this->configuration->entries->returns([
                    $entry1->get(),
                    $entry2->get(),
                ]);

                $entry1->factories->returns(new FactoryMap([
                    'id1' => new TestFactory('f11'),
                    'id2' => new TestFactory('f12'),
                ]));

                $entry2->factories->returns(new FactoryMap([
                    'id2' => new TestFactory('f22'),
                ]));

                $entry1->extensions->returns(new FactoryMap([
                    'id2' => new TestFactory('e12'),
                ]));

                $entry2->extensions->returns(new FactoryMap([
                    'id1' => new TestFactory('e21'),
                ]));

                $entry1->metadata->returns([
                    'id1' => ['k11' => 'm11', 'k12' => 'm12'],
                ]);

                $entry2->metadata->returns([
                    'id2' => ['k21' => 'm21', 'k22' => 'm22'],
                ]);

                $factories = [
                    'id1' => new Extension(new TestFactory('f11'), new TestFactory('e21')),
                    'id2' => new Extension(new TestFactory('f22'), new TestFactory('e12')),
                ];

                $metadata = new Metadata(...[
                    ['id1' => ['k11' => 'm11', 'k12' => 'm12']],
                    ['id2' => ['k21' => 'm21', 'k22' => 'm22']],
                ]);

                $this->pass1->factories->with($factories, $metadata)->returns([
                    'id3' => new TestFactory('f33'),
                    'id4' => new TestFactory('f34'),
                ]);

                $this->pass2->factories->with($factories, $metadata)->returns([
                    'id2' => new TestFactory('f42'),
                    'id5' => new TestFactory('f45'),
                ]);

                $this->pass3->factories->with($factories, $metadata)->returns([
                    'id5' => new TestFactory('f55'),
                    'id6' => new TestFactory('f56'),
                ]);

                $test = $this->map->factories();

                expect($test)->toEqual([
                    'id1' => new Extension(new TestFactory('f11'), new TestFactory('e21')),
                    'id2' => new TestFactory('f42'),
                    'id3' => new TestFactory('f33'),
                    'id4' => new TestFactory('f34'),
                    'id5' => new TestFactory('f55'),
                    'id6' => new TestFactory('f56'),
                ]);

            });

        });

    });

});

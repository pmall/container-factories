<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\FactoryMapInterface;
use Quanta\Container\ConfiguredFactoryMap;
use Quanta\Container\ConfigurationInterface;
use Quanta\Container\ConfigurationPassInterface;

require_once __DIR__ . '/.test/classes.php';

describe('ConfiguredFactoryMap', function () {

    beforeEach(function () {

        $this->configuration = mock(Configurationinterface::class);

        $this->map = new ConfiguredFactoryMap($this->configuration->get());

    });

    it('should implement FactoryMapInterface', function () {

        expect($this->map)->toBeAnInstanceOf(FactoryMapInterface::class);

    });

    describe('->factories()', function () {

        beforeEach(function () {

            $this->delegate = mock(FactoryMapInterface::class);

            $this->configuration->map->returns($this->delegate->get());

        });

        context('when the configuration does not provide configuration passes', function () {

            it('should return the associative array of factory provided by the factory map provided by the configuration', function () {

                $this->delegate->factories->returns([
                    'id1' => $factory1 = function () {},
                    'id2' => $factory2 = function () {},
                    'id3' => $factory3 = function () {},
                ]);

                $test = $this->map->factories();

                expect($test)->toBeAn('array');
                expect($test)->toHaveLength(3);
                expect($test['id1'])->toBe($factory1);
                expect($test['id2'])->toBe($factory2);
                expect($test['id3'])->toBe($factory3);

            });

        });

        context('when the configuration provides at least one configuation pass', function () {

            it('should process the associative array of factories with the configuration passes provided by the configuration', function () {

                $pass1 = mock(ConfigurationPassInterface::class);
                $pass2 = mock(ConfigurationPassInterface::class);
                $pass3 = mock(ConfigurationPassInterface::class);

                $this->configuration->passes->returns([
                    $pass1->get(),
                    $pass2->get(),
                    $pass3->get(),
                ]);

                $this->delegate->factories->returns([
                    'id1' => $f11 = new Test\TestFactory('f11'),
                    'id2' => $f12 = new Test\TestFactory('f12'),
                    'id3' => $f13 = new Test\TestFactory('f13'),
                ]);

                $pass1->processed->with(['id1' => $f11, 'id2' => $f12, 'id3' => $f13])
                    ->returns([
                        'id1' => $f21 = new Test\TestFactory('f21'),
                        'id2' => $f22 = new Test\TestFactory('f22'),
                        'id3' => $f23 = new Test\TestFactory('f23'),
                    ]);

                $pass2->processed->with(['id1' => $f21, 'id2' => $f22, 'id3' => $f23])
                    ->returns([
                        'id1' => $f31 = new Test\TestFactory('f31'),
                        'id2' => $f32 = new Test\TestFactory('f32'),
                        'id3' => $f33 = new Test\TestFactory('f33'),
                    ]);

                $pass3->processed->with(['id1' => $f31, 'id2' => $f32, 'id3' => $f33])
                    ->returns([
                        'id1' => $f41 = function () {},
                        'id2' => $f42 = function () {},
                        'id3' => $f43 = function () {},
                    ]);

                $test = $this->map->factories();

                expect($test)->toBeAn('array');
                expect($test)->toHaveLength(3);
                expect($test['id1'])->toBe($f41);
                expect($test['id2'])->toBe($f42);
                expect($test['id3'])->toBe($f43);

            });

        });

    });

});

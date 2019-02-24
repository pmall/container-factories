<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\FactoryMapInterface;
use Quanta\Container\ProcessedFactoryMap;
use Quanta\Container\ProcessingPassInterface;

require_once __DIR__ . '/.test/classes.php';

describe('ProcessedFactoryMap', function () {

    beforeEach(function () {

        $this->delegate = mock(FactoryMapInterface::class);

    });

    context('when there is no configuration passes', function () {

        beforeEach(function () {

            $this->map = new ProcessedFactoryMap($this->delegate->get());

        });

        it('should implement FactoryMapInterface', function () {

            expect($this->map)->toBeAnInstanceOf(FactoryMapInterface::class);

        });

        describe('->map()', function () {

            it('should return the factory map', function () {

                $test = $this->map->map();

                expect($test)->toBe($this->delegate->get());

            });

        });

        describe('->passes()', function () {

            it('should return an empty array', function () {

                $test = $this->map->passes();

                expect($test)->toEqual([]);

            });

        });

        describe('->factories()', function () {

            it('should return the associative array of factories provided by the delegate', function () {

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

    });

    context('when there is at least one configuration passes', function () {

        beforeEach(function () {

            $this->pass1 = mock(ProcessingPassInterface::class);
            $this->pass2 = mock(ProcessingPassInterface::class);
            $this->pass3 = mock(ProcessingPassInterface::class);

            $this->map = new ProcessedFactoryMap($this->delegate->get(), ...[
                $this->pass1->get(),
                $this->pass2->get(),
                $this->pass3->get(),
            ]);

        });

        it('should implement FactoryMapInterface', function () {

            expect($this->map)->toBeAnInstanceOf(FactoryMapInterface::class);

        });

        describe('->map()', function () {

            it('should return the factory map', function () {

                $test = $this->map->map();

                expect($test)->toBe($this->delegate->get());

            });

        });

        describe('->passes()', function () {

            it('should return the processing passes', function () {

                $test = $this->map->passes();

                expect($test)->toBeAn('array');
                expect($test)->toHaveLength(3);
                expect($test[0])->toBe($this->pass1->get());
                expect($test[1])->toBe($this->pass2->get());
                expect($test[2])->toBe($this->pass3->get());

            });

        });

        describe('->factories()', function () {

            it('should process the associative array of factories provided by the delegate', function () {

                $this->delegate->factories->returns([
                    'id1' => $f11 = new Test\testFactory('f11'),
                    'id2' => $f12 = new Test\testFactory('f12'),
                    'id3' => $f13 = new Test\testFactory('f13'),
                ]);

                $this->pass1->processed->with(['id1' => $f11, 'id2' => $f12, 'id3' => $f13])->returns([
                    'id1' => $f21 = new Test\testFactory('f21'),
                    'id2' => $f22 = new Test\testFactory('f22'),
                    'id3' => $f23 = new Test\testFactory('f23'),
                ]);

                $this->pass2->processed->with(['id1' => $f21, 'id2' => $f22, 'id3' => $f23])->returns([
                    'id1' => $f31 = new Test\testFactory('f31'),
                    'id2' => $f32 = new Test\testFactory('f32'),
                    'id3' => $f33 = new Test\testFactory('f33'),
                ]);

                $this->pass3->processed->with(['id1' => $f31, 'id2' => $f32, 'id3' => $f33])->returns([
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

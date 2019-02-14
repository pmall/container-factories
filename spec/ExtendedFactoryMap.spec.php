<?php

use function Eloquent\Phony\Kahlan\mock;

use Test\TestFactory;

use Quanta\Container\ExtendedFactoryMap;
use Quanta\Container\FactoryMapInterface;
use Quanta\Container\Factories\Extension;

require_once __DIR__ . '/.test/classes.php';

describe('ExtendedFactoryMap', function () {

    context('when there is no factory map', function () {

        beforeEach(function () {

            $this->map = new ExtendedFactoryMap;

        });

        it('should implement FactoryMapInterface', function () {

            expect($this->map)->toBeAnInstanceOf(FactoryMapInterface::class);

        });

        describe('->factories()', function () {

            it('should return an empty array', function () {

                expect($this->map->factories())->toEqual([]);

            });

        });

    });

    context('when there is at least one factory map', function () {

        beforeEach(function () {

            $this->map1 = mock(FactoryMapInterface::class);
            $this->map2 = mock(FactoryMapInterface::class);
            $this->map3 = mock(FactoryMapInterface::class);

            $this->map = new ExtendedFactoryMap(...[
                $this->map1->get(),
                $this->map2->get(),
                $this->map3->get(),
            ]);

        });

        it('should implement FactoryMapInterface', function () {

            expect($this->map)->toBeAnInstanceOf(FactoryMapInterface::class);

        });

        describe('->factories()', function () {

            it('should merge the factories by creating extensions for those sharing the same identifiers', function () {

                $this->map1->factories->returns([
                    'id1' => new TestFactory('f01'),
                    'id4' => new TestFactory('f02'),
                    'id5' => new TestFactory('f03'),
                    'id6' => new TestFactory('f04'),
                ]);

                $this->map2->factories->returns([
                    'id2' => new TestFactory('f05'),
                    'id4' => new TestFactory('f06'),
                    'id6' => new TestFactory('f07'),
                ]);

                $this->map3->factories->returns([
                    'id3' => new TestFactory('f08'),
                    'id5' => new TestFactory('f09'),
                    'id6' => new TestFactory('f10'),
                ]);

                $test = $this->map->factories();

                expect($test)->toEqual([
                    'id1' => new TestFactory('f01'),
                    'id2' => new TestFactory('f05'),
                    'id3' => new TestFactory('f08'),
                    'id4' => new Extension(new TestFactory('f02'), new TestFactory('f06')),
                    'id5' => new Extension(new TestFactory('f03'), new TestFactory('f09')),
                    'id6' => new Extension(
                        new Extension(new TestFactory('f04'), new TestFactory('f07')), new TestFactory('f10')
                    ),
                ]);

            });

        });

    });

});

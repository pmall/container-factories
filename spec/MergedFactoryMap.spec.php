<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\MergedFactoryMap;
use Quanta\Container\FactoryMapInterface;

describe('MergedFactoryMap', function () {

    context('when there is no factory map', function () {

        beforeEach(function () {

            $this->map = new MergedFactoryMap;

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

            $this->map = new MergedFactoryMap(...[
                $this->map1->get(),
                $this->map2->get(),
                $this->map3->get(),
            ]);

        });

        it('should implement FactoryMapInterface', function () {

            expect($this->map)->toBeAnInstanceOf(FactoryMapInterface::class);

        });

        describe('->factories()', function () {

            it('should merge the factories by overwritting those sharing the same identifiers', function () {

                $this->map1->factories->returns([
                    'id1' => $this->factory1 = function () {},
                    'id2' => $this->factory2 = function () {},
                ]);

                $this->map2->factories->returns([
                    'id3' => $this->factory3 = function () {},
                    'id4' => $this->factory4 = function () {},
                ]);

                $this->map3->factories->returns([
                    'id1' => $this->factory5 = function () {},
                    'id4' => $this->factory6 = function () {},
                ]);

                $test = $this->map->factories();

                expect($test)->toBeAn('array');
                expect($test)->toHaveLength(4);
                expect($test['id1'])->toBe($this->factory5);
                expect($test['id2'])->toBe($this->factory2);
                expect($test['id3'])->toBe($this->factory3);
                expect($test['id4'])->toBe($this->factory6);

            });

        });

    });

});

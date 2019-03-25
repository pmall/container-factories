<?php

use Quanta\Container\FactoryMap;
use Quanta\Container\FactoryMapInterface;

describe('FactoryMap', function () {

    context('when all the values of the associative array of factories are callable', function () {

        beforeEach(function () {

            $this->map = new FactoryMap([
                'id1' => $this->factory1 = function () {},
                'id2' => $this->factory2 = function () {},
                'id3' => $this->factory3 = function () {},
            ]);

        });

        it('should implement FactoryMapInterface', function () {

            expect($this->map)->toBeAnInstanceOf(FactoryMapInterface::class);

        });

        describe('->factories()', function () {

            it('should merge all the arrays of factories', function () {

                $test = $this->map->factories();

                expect($test)->toBeAn('array');
                expect($test)->toHaveLength(3);
                expect($test['id1'])->toBe($this->factory1);
                expect($test['id2'])->toBe($this->factory2);
                expect($test['id3'])->toBe($this->factory3);

            });

        });

    });

    context('when a value of the associative array of factories is not a callable', function () {

        it('should throw an InvalidArgumentException', function () {

            $test = function () {
                new FactoryMap([
                    'id1' => function () {},
                    'id2' => 2,
                    'id3' => function () {},
                ]);
            };

            expect($test)->toThrow(new InvalidArgumentException);

        });

    });

});

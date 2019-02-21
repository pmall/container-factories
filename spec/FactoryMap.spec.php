<?php

use Quanta\Container\FactoryMap;
use Quanta\Container\FactoryMapInterface;

use Quanta\Exceptions\ArrayArgumentTypeErrorMessage;

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

        it('should merge all the arrays of factories', function () {

            $test = $this->map->factories();

            expect($test)->toBeAn('array');
            expect($test)->toHaveLength(3);
            expect($test['id1'])->toBe($this->factory1);
            expect($test['id2'])->toBe($this->factory2);
            expect($test['id3'])->toBe($this->factory3);

        });

    });

    context('when a value of the associative array of factories is not a callable', function () {

        it('should throw an InvalidArgumentException', function () {

            ArrayArgumentTypeErrorMessage::testing();

            $factories = [
                'id1' => function () {},
                'id2' => function () {},
                'id3' => 1,
                'id4' => function () {},
            ];

            $test = function () use ($factories) {
                new FactoryMap($factories);
            };

            expect($test)->toThrow(new InvalidArgumentException(
                (string) new ArrayArgumentTypeErrorMessage(1, 'callable', $factories)
            ));

        });

    });

});

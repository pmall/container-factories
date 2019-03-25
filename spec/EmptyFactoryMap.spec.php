<?php

use Quanta\Container\EmptyFactoryMap;
use Quanta\Container\FactoryMapInterface;

describe('EmptyFactoryMap', function () {

    beforeEach(function () {

        $this->map = new EmptyFactoryMap;

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

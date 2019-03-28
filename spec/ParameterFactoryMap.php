<?php

use Quanta\Container\FactoryMapInterface;
use Quanta\Container\ParameterFactoryMap;
use Quanta\Container\Values\Value;
use Quanta\Container\Values\ValueFactory;
use Quanta\Container\Values\DummyValueParser;
use Quanta\Container\Factories\Factory;

describe('ParameterFactoryMap', function () {

    beforeEach(function () {

        $this->factory = new ValueFactory(
            new DummyValueParser([
                'parameter1' => 'parsed1',
                'parameter2' => 'parsed2',
                'parameter3' => 'parsed3',
            ])
        );

    });

    context('when the array of parameters is empty', function () {

        beforeEach(function () {

            $this->map = new ParameterFactoryMap($this->factory, []);

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

    context('when there is at least one parameter', function () {

        beforeEach(function () {

            $this->map = new ParameterFactoryMap($this->factory, [
                'id1' => 'parameter1',
                'id2' => 'parameter2',
                'id3' => 'parameter3',
            ]);

        });

        it('should implement FactoryMapInterface', function () {

            expect($this->map)->toBeAnInstanceOf(FactoryMapInterface::class);

        });

        describe('->factories()', function () {

            it('should return an array of factory with the parsed parameters', function () {

                $test = $this->map->factories();

                expect($test)->toEqual([
                    'id1' => new Factory(new Value('parsed1')),
                    'id2' => new Factory(new Value('parsed2')),
                    'id3' => new Factory(new Value('parsed3')),
                ]);

            });

        });

    });

});

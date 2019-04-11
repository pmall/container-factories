<?php

use Quanta\Container\ValueParser;
use Quanta\Container\FactoryInterface;
use Quanta\Container\ParsedFactoryMap;
use Quanta\Container\FactoryMapInterface;

describe('ParsedFactoryMap', function () {

    beforeEach(function () {

        $this->parser = new ValueParser;

    });

    context('when the array of parameters is empty', function () {

        beforeEach(function () {

            $this->map = new ParsedFactoryMap($this->parser, []);

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

            $this->map = new ParsedFactoryMap($this->parser, [
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

                expect($test)->toBeAn('array');
                expect($test)->toHaveLength(3);
                expect($test['id1'])->toEqual(($this->parser)('parameter1'));
                expect($test['id2'])->toEqual(($this->parser)('parameter2'));
                expect($test['id3'])->toEqual(($this->parser)('parameter3'));

            });

        });

    });

});

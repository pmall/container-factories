<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\FactoryInterface;
use Quanta\Container\FactoryMapInterface;
use Quanta\Container\ParameterFactoryMap;
use Quanta\Container\Parsing\ParserInterface;
use Quanta\Container\Parsing\ParsedFactoryInterface;

describe('ParameterFactoryMap', function () {

    beforeEach(function () {

        $this->parser = mock(ParserInterface::class);

    });

    context('when the array of parameters is empty', function () {

        beforeEach(function () {

            $this->map = new ParameterFactoryMap($this->parser->get(), []);

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

            $this->map = new ParameterFactoryMap($this->parser->get(), [
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

                $factory1 = mock(FactoryInterface::class);
                $factory2 = mock(FactoryInterface::class);
                $factory3 = mock(FactoryInterface::class);

                $parsed1 = mock(ParsedFactoryInterface::class);
                $parsed2 = mock(ParsedFactoryInterface::class);
                $parsed3 = mock(ParsedFactoryInterface::class);

                $parsed1->factory->returns($factory1);
                $parsed2->factory->returns($factory2);
                $parsed3->factory->returns($factory3);

                $this->parser->__invoke->with('parameter1')->returns($parsed1);
                $this->parser->__invoke->with('parameter2')->returns($parsed2);
                $this->parser->__invoke->with('parameter3')->returns($parsed3);

                $test = $this->map->factories();

                expect($test)->toBeAn('array');
                expect($test)->toHaveLength(3);
                expect($test['id1'])->toBe($factory1->get());
                expect($test['id2'])->toBe($factory2->get());
                expect($test['id3'])->toBe($factory3->get());

            });

        });

    });

});

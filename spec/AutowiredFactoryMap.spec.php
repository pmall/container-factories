<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\DefinitionProxy;
use Quanta\Container\FactoryMapInterface;
use Quanta\Container\AutowiredFactoryMap;
use Quanta\Container\Autowiring\AutowiredInstance;
use Quanta\Container\Autowiring\ArgumentParserInterface;

describe('AutowiredFactoryMap', function () {

    beforeEach(function () {

        $this->parser = mock(ArgumentParserInterface::class);

    });

    context('when the map is empty', function () {

        beforeEach(function () {

            $this->map = new AutowiredFactoryMap($this->parser->get(), []);

        });

        it('should implement FactoryMapInterface', function () {

            expect($this->map)->toBeAnInstanceOf(FactoryMapInterface::class);

        });

        describe('->map()', function () {

            it('should return an empty array', function () {

                $test = $this->map->factories();

                expect($test)->toEqual([]);

            });

        });

    });

    context('when the map is not empty', function () {

        context('when all the values of the map are arrays', function () {

            beforeEach(function () {

                $this->map = new AutowiredFactoryMap($this->parser->get(), [
                    Test\SomeClass1::class => ['key1' => 'value1'],
                    Test\SomeClass2::class => ['key2' => 'value2'],
                    Test\SomeClass3::class => ['key3' => 'value3'],
                ]);

            });

            it('should implement FactoryMapInterface', function () {

                expect($this->map)->toBeAnInstanceOf(FactoryMapInterface::class);

            });

            describe('->map()', function () {

                it('should return an autowired factory for each entry of the map', function () {

                    $test = $this->map->factories();

                    expect($test)->toBeAn('array');
                    expect($test)->toHaveLength(3);
                    expect($test[Test\SomeClass1::class])->toEqual(new DefinitionProxy(
                        new AutowiredInstance($this->parser->get(), Test\SomeClass1::class, [
                            'key1' => 'value1'
                        ])
                    ));
                    expect($test[Test\SomeClass2::class])->toEqual(new DefinitionProxy(
                        new AutowiredInstance($this->parser->get(), Test\SomeClass2::class, [
                            'key2' => 'value2'
                        ])
                    ));
                    expect($test[Test\SomeClass3::class])->toEqual(new DefinitionProxy(
                        new AutowiredInstance($this->parser->get(), Test\SomeClass3::class, [
                            'key3' => 'value3'
                        ])
                    ));

                });

            });

        });

        context('when a value of the map is not an array', function () {

            it('should throw an InvalidArgumentException', function () {

                $test = function () {
                    new AutowiredFactoryMap($this->parser->get(), [
                        Test\SomeClass1::class => ['key1' => 'value1'],
                        Test\SomeClass2::class => 1,
                        Test\SomeClass3::class => ['key3' => 'value3'],
                    ]);
                };

                expect($test)->toThrow(new InvalidArgumentException);

            });

        });

    });

});

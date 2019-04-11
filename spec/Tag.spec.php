<?php

use function Eloquent\Phony\Kahlan\mock;

use Psr\Container\ContainerInterface;

use Quanta\Container\Tag;
use Quanta\Container\FactoryInterface;

describe('Tag', function () {

    context('when there is no id', function () {

        beforeEach(function () {

            $this->factory = new Tag;

        });

        it('should implement FactoryInterface', function () {

            expect($this->factory)->toBeAnInstanceOf(FactoryInterface::class);

        });

        describe('->__invoke()', function () {

            it('should return an empty array', function () {

                $container = mock(ContainerInterface::class);

                $test = ($this->factory)($container->get());

                expect($test)->toEqual([]);

            });

        });

        describe('->compiled()', function () {

            it('should return a compiled version of the empty tag', function () {

                $test = $this->factory->compiled('container', function () {});

                expect($test)->toEqual('[]');

            });

        });

    });

    context('when there is at leas one id', function () {

        beforeEach(function () {

            $this->factory = new Tag('id1', 'id2', 'id3');

        });

        it('should implement FactoryInterface', function () {

            expect($this->factory)->toBeAnInstanceOf(FactoryInterface::class);

        });

        describe('->__invoke()', function () {

            it('should return an array containing the container entries', function () {

                $container = mock(ContainerInterface::class);

                $container->get->with('id1')->returns('value1');
                $container->get->with('id2')->returns('value2');
                $container->get->with('id3')->returns('value3');

                $test = ($this->factory)($container->get());

                expect($test)->toEqual(['value1', 'value2', 'value3']);

            });

        });

        describe('->compiled()', function () {

            it('should return a compiled version of the tag', function () {

                $test = $this->factory->compiled('container', function () {});

                expect($test)->toEqual(implode(PHP_EOL, [
                    '[',
                    '    $container->get(\'id1\'),',
                    '    $container->get(\'id2\'),',
                    '    $container->get(\'id3\'),',
                    ']',
                ]));

            });

        });

    });

});

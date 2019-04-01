<?php

use function Eloquent\Phony\Kahlan\mock;

use Psr\Container\ContainerInterface;

use Quanta\Container\FactoryInterface;
use Quanta\Container\InterpolatedString;
use Quanta\Container\Compilation\Compiler;

describe('InterpolatedString::instance()', function () {

    context('when no identifier is given', function () {

        it('should return a new InterpolatedString with the given format and no identifier', function () {

            $test = InterpolatedString::instance('a:b:c');

            expect($test)->toEqual(new InterpolatedString('a:b:c'));

        });

    });

    context('when at least one identifier is given', function () {

        it('should return a new InterpolatedString with the given format and identifiers', function () {

            $test = InterpolatedString::instance('a:%s:b:%s:c:%s', 'id1', 'id2', 'id3');

            expect($test)->toEqual(new InterpolatedString('a:%s:b:%s:c:%s', 'id1', 'id2', 'id3'));

        });

    });

});

describe('InterpolatedString', function () {

    context('when there is no identifier', function () {

        beforeEach(function () {

            $this->factory = new InterpolatedString('a:b:c');

        });

        it('should implement FactoryInterface', function () {

            expect($this->factory)->toBeAnInstanceOf(FactoryInterface::class);

        });

        describe('->__invoke()', function () {

            it('should return the sprintf format', function () {

                $container = mock(ContainerInterface::class);

                $test = ($this->factory)($container->get());

                expect($test)->toEqual('a:b:c');

            });

        });

        describe('->compilable()', function () {

            it('should return a compilable version of the interpolated string', function () {

                $compiler = Compiler::testing();

                $test = $this->factory->compilable('container');

                expect($compiler($test))->toEqual('\'a:b:c\'');

            });

        });

    });

    context('when there is at least one identifiers', function () {

        beforeEach(function () {

            $this->factory = new InterpolatedString('a:%s:c:%s:e:%s', 'id1', 'id2', 'id3');

        });

        it('should implement FactoryInterface', function () {

            expect($this->factory)->toBeAnInstanceOf(FactoryInterface::class);

        });

        describe('->__invoke()', function () {

            it('should return the sprintf format interpolated with the container entries', function () {

                $container = mock(ContainerInterface::class);

                $container->get->with('id1')->returns('b');
                $container->get->with('id2')->returns('d');
                $container->get->with('id3')->returns('f');

                $test = ($this->factory)($container->get());

                expect($test)->toEqual('a:b:c:d:e:f');

            });

        });

        describe('->compilable()', function () {

            it('should return a compilable version of the interpolated string', function () {

                $compiler = Compiler::testing();

                $test = $this->factory->compilable('container');

                expect($compiler($test))->toEqual(implode(PHP_EOL, [
                    'vsprintf(\'a:%s:c:%s:e:%s\', [',
                    '    $container->get(\'id1\'),',
                    '    $container->get(\'id2\'),',
                    '    $container->get(\'id3\'),',
                    '])',
                ]));

            });

        });

    });

});

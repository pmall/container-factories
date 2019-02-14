<?php

use function Eloquent\Phony\Kahlan\mock;

use Psr\Container\ContainerInterface;

use Quanta\Container\Values\ValueInterface;
use Quanta\Container\Values\InterpolatedString;

describe('InterpolatedString', function () {

    context('when there is no identifier', function () {

        it('should throw an ArgumentCountError', function () {

            $test = function () { new InterpolatedString('value'); };

            expect($test)->toThrow(new ArgumentCountError);

        });

    });

    context('when there is identifiers', function () {

        beforeEach(function () {

            $this->value = new InterpolatedString('a:%s:c:%s:e:%s', ...[
                'id.b',
                'id.d',
                'id.f',
            ]);

        });

        it('should implement ValueInterface', function () {

            expect($this->value)->toBeAnInstanceOf(ValueInterface::class);

        });

        describe('->value()', function () {

            it('should return the sprintf format interpolated with the container entries', function () {

                $container = mock(ContainerInterface::class);

                $container->get->with('id.b')->returns('b');
                $container->get->with('id.d')->returns('d');
                $container->get->with('id.f')->returns('f');

                $test = $this->value->value($container->get());

                expect($test)->toEqual('a:b:c:d:e:f');

            });

        });

        describe('->str()', function () {

            it('should return the string representation sprintf format interpolation', function () {

                $test = $this->value->str('container');

                expect($test)->toEqual(<<<'EOT'
(function (\Psr\Container\ContainerInterface $container) {
    return vsprintf('a:%s:c:%s:e:%s', array_map([$container, 'get'], [
        'id.b',
        'id.d',
        'id.f',
    ]));
})($container)
EOT
                );

            });

        });

    });

});

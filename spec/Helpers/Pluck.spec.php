<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\Helpers\Pluck;

describe('Pluck', function () {

    describe('->__invoke()', function () {

        it('should call the method on the given object with the arguments', function () {

            $helper = new Pluck('method', 'value1', 'value2', 'value3');

            $object = mock(['method' => function () {}]);

            $object->method->with('value1', 'value2', 'value3')->returns('value');

            $test = $helper($object->get());

            expect($test)->toEqual('value');

        });

    });

});

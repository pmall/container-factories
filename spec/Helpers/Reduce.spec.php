<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\Helpers\Reduce;

describe('Reduce', function () {

    describe('->__invoke()', function () {

        it('should call the method on the given object with the given carried value', function () {

            $helper = new Reduce('method');

            $object = mock(['method' => function () {}]);

            $object->method->with('carried')->returns('value');

            $test = $helper('carried', $object->get());

            expect($test)->toEqual('value');

        });

    });

});

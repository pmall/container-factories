<?php

use Quanta\Container\Helpers\Quote;

describe('Quote', function () {

    describe('->__invoke()', function () {

        it('should return the given string escaped with quotes', function () {

            $helper = new Quote;

            $test = $helper('value\'value');

            expect($test)->toEqual('\'value\\\'value\'');

        });

    });

});

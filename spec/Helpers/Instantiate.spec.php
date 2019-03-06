<?php

use Quanta\Container\Helpers\Instantiate;

require_once __DIR__ . '/../.test/classes.php';

describe('Instantiate', function () {

    describe('->__invoke()', function () {

        it('should return an instance of the class using the given arguments', function () {

            $helper = new Instantiate(Test\TestClass::class);

            $test = $helper('value1', 'value2', 'value3');

            expect($test)->toEqual(new Test\TestClass('value1', 'value2', 'value3'));

        });

    });

});

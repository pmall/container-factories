<?php

use Quanta\Container\Factories\Tag;

use Quanta\Container\Passes\ReverseTagging;
use Quanta\Container\Passes\ConfigurationPassInterface;
use Quanta\Container\Configuration\Metadata;

require_once __DIR__ . '/../.test/classes.php';

describe('ReverseTagging', function () {

    beforeEach(function () {

        $this->pass = new ReverseTagging('id', ...[
            Test\SomeInterface1::class,
            Test\SomeInterface2::class,
        ]);

    });

    it('should implement ConfigurationPassInterface', function () {

        expect($this->pass)->toBeAnInstanceOf(ConfigurationPassInterface::class);

    });

    describe('->factories()', function () {

        it('should return a factory map associating the id to a tag returning the entries matching all the interfaces', function () {

            $factories = [
                Test\SomeClass1::class => function () {},
                Test\SomeClass2::class => function () {},
                Test\SomeClass3::class => function () {},
                Test\SomeClass4::class => function () {},
                Test\SomeClass5::class => function () {},
            ];

            $test = $this->pass->factories($factories, new Metadata);

            expect($test)->toEqual([
                'id' => new Tag([
                    Test\SomeClass3::class => Test\SomeClass3::class,
                    Test\SomeClass4::class => Test\SomeClass4::class,
                ]),
            ]);

        });

    });

});

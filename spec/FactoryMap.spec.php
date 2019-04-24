<?php

use function Eloquent\Phony\Kahlan\stub;
use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\Tag;
use Quanta\Container\Alias;
use Quanta\Container\Extension;
use Quanta\Container\FactoryMap;
use Quanta\Container\FactoryMapInterface;
use Quanta\Container\Configuration\ConfigurationInterface;
use Quanta\Container\Configuration\ConfigurationSourceInterface;

require_once __DIR__ . '/.test/classes.php';

describe('FactoryMap', function () {

    beforeEach(function () {

        $this->source = mock(ConfigurationSourceInterface::class);

        $this->map = new FactoryMap($this->source->get());

    });

    it('should implement FactoryMapInterface', function () {

        expect($this->map)->toBeAnInstanceOf(FactoryMapInterface::class);

    });

    describe('->factories()', function () {

        it('should get the configuration provided by the configuration source only once', function () {

            $test = $this->map->factories();

            expect($test)->toBeAn('array');

            $this->source->configuration->once()->called();

        });

        it('should return an associative array of factories from the configuration source', function () {

            $configuration = mock(ConfigurationInterface::class);

            $this->source->configuration->returns($configuration);

            $configuration->factories->returns([
                'id1' => $factory1 = function () {},
                'id2' => $factory2 = function () {},
                'id3' => $factory3 = function () {},
            ]);

            $configuration->mappers->returns([
                'tag1' => $predicate1 = stub(),
                'tag2' => $predicate2 = stub(),
                'tag3' => $predicate3 = stub(),

                // here to show tags can't overwrite factory.
                'id1' => function () { return true; },
                'id2' => function () { return true; },
                'id3' => function () { return true; },
            ]);

            $configuration->extensions->returns([
                'id1' => [$extension11 = function () {}],
                'id3' => [$extension31 = function () {}, $extension32 = function () {}],
                'tag1' => [$extension41 = function () {}],
                'tag3' => [$extension61 = function () {}, $extension62 = function () {}],

                // here to show extensions without factories are left out.
                'id4' => [$extension71 = function () {}, $extension72 = function () {}],
                'tag4' => [$extension81 = function () {}, $extension82 = function () {}],
            ]);

            $predicate1->with('id1')->returns(true);
            $predicate1->with('id2')->returns(false);
            $predicate1->with('id3')->returns(true);
            $predicate2->with('id1')->returns(false);
            $predicate2->with('id2')->returns(false);
            $predicate2->with('id3')->returns(false);
            $predicate3->with('id1')->returns(false);
            $predicate3->with('id2')->returns(true);
            $predicate3->with('id3')->returns(false);

            $test = $this->map->factories();

            expect($test)->toEqual([
                'id1' => new Extension($factory1, $extension11),
                'id2' => $factory2,
                'id3' => new Extension(new Extension($factory3, $extension31), $extension32),
                'tag1' => new Extension(new Tag('id1', 'id3'), $extension41),
                'tag2' => new Tag,
                'tag3' => new Extension(new Extension(new Tag('id2'), $extension61), $extension62),
            ]);

        });

    });

});

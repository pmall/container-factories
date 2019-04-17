<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\Tag;
use Quanta\Container\Alias;
use Quanta\Container\FactoryMap;
use Quanta\Container\FactoryMapInterface;
use Quanta\Container\ProcessingPassInterface;
use Quanta\Container\Configuration\ConfigurationInterface;
use Quanta\Container\Configuration\ConfigurationUnitInterface;

require_once __DIR__ . '/.test/classes.php';

describe('FactoryMap', function () {

    beforeEach(function () {

        $this->configuration = mock(ConfigurationInterface::class);

        $this->map = new FactoryMap($this->configuration->get());

    });

    it('should implement FactoryMapInterface', function () {

        expect($this->map)->toBeAnInstanceOf(FactoryMapInterface::class);

    });

    describe('->factories()', function () {

        it('should get a configuration unit from the configuration only once', function () {

            $test = $this->map->factories();

            expect($test)->toBeAn('array');

            $this->configuration->unit->once()->called();

        });

        it('should return an associative array of factories from the configuration', function () {

            $unit = mock(ConfigurationUnitInterface::class);
            $pass = mock(ProcessingPassInterface::class);

            $this->configuration->unit->returns($unit);

            $unit->factories->returns([
                'id1' => $factory1 = function () {},
                'id2' => $factory2 = function () {},
                'id3' => $factory3 = function () {},
            ]);

            $unit->pass->returns($pass);

            $pass->aliases->with('id1')->returns(['alias1', 'alias3']);
            $pass->aliases->with('id2')->returns([]);
            $pass->aliases->with('id3')->returns(['alias2']);

            $pass->tags->with('id1', 'id2', 'id3')->returns([
                'tag1' => ['id1', 'id2'],
                'tag2' => [],
                'tag3' => ['id3'],
            ]);

            $pass->processed->with('id1', Kahlan\Arg::toBe($factory1))->returns($processed1 = function () {});
            $pass->processed->with('id2', Kahlan\Arg::toBe($factory2))->returns($processed2 = function () {});
            $pass->processed->with('id3', Kahlan\Arg::toBe($factory3))->returns($processed3 = function () {});
            $pass->processed->with('alias1', new Alias('id1'))->returns($processed4 = function () {});
            $pass->processed->with('alias2', new Alias('id3'))->returns($processed5 = function () {});
            $pass->processed->with('alias3', new Alias('id1'))->returns($processed6 = function () {});
            $pass->processed->with('tag1', new Tag('id1', 'id2'))->returns($processed7 = function () {});
            $pass->processed->with('tag2', new Tag)->returns($processed8 = function () {});
            $pass->processed->with('tag3', new Tag('id3'))->returns($processed9 = function () {});

            $test = $this->map->factories();

            expect($test)->toBeAn('array');
            expect($test)->toHaveLength(9);
            expect($test['id1'])->toBe($processed1);
            expect($test['id2'])->toBe($processed2);
            expect($test['id3'])->toBe($processed3);
            expect($test['alias1'])->toBe($processed4);
            expect($test['alias2'])->toBe($processed5);
            expect($test['alias3'])->toBe($processed6);
            expect($test['tag1'])->toBe($processed7);
            expect($test['tag2'])->toBe($processed8);
            expect($test['tag3'])->toBe($processed9);

        });

    });

});

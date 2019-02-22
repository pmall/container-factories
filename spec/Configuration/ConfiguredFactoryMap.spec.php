<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\FactoryMap;
use Quanta\Container\FactoryMapInterface;
use Quanta\Container\Configuration\IdentityStep;
use Quanta\Container\Configuration\ConfiguredFactoryMap;
use Quanta\Container\Configuration\ConfigurationInterface;
use Quanta\Container\Configuration\ConfigurationStepInterface;

describe('Configuration', function () {

    beforeEach(function () {

        $this->configuration = mock(ConfigurationInterface::class);

        $this->map = new ConfiguredFactoryMap($this->configuration->get());

    });

    it('should implement FactoryMapInterface', function () {

        expect($this->map)->toBeAnInstanceOf(FactoryMapInterface::class);

    });

    describe('->factories()', function () {

        it('should return a factory map from the configuration', function () {

            $map = mock(FactoryMapInterface::class);
            $step = mock(ConfigurationStepInterface::class);

            $this->configuration->step->with(new IdentityStep)->returns($step);

            $step->map->with(new FactoryMap([]))->returns($map);

            $map->factories->returns([
                'id1' => $factory1 = function () {},
                'id2' => $factory2 = function () {},
                'id3' => $factory3 = function () {},
            ]);

            $test = $this->map->factories();

            expect($test)->toBeAn('array');
            expect($test)->toHaveLength(3);
            expect($test['id1'])->toBe($factory1);
            expect($test['id2'])->toBe($factory2);
            expect($test['id3'])->toBe($factory3);

        });

    });

});

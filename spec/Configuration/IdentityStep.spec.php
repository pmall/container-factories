<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\FactoryMapInterface;
use Quanta\Container\Configuration\IdentityStep;
use Quanta\Container\Configuration\ConfigurationStepInterface;

describe('IdentityStep', function () {

    beforeEach(function () {

        $this->step = new IdentityStep;

    });

    it('should implement ConfigurationStepInterface', function () {

        expect($this->step)->toBeAnInstanceOf(ConfigurationStepInterface::class);

    });

    describe('->map()', function () {

        it('should return the given factory map', function () {

            $map = mock(FactoryMapInterface::class);

            $test = $this->step->map($map->get());

            expect($test)->toBe($map->get());

        });

    });

});

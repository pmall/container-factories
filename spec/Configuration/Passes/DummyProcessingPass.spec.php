<?php

use Quanta\Container\Configuration\Passes\DummyProcessingPass;
use Quanta\Container\Configuration\Passes\ProcessingPassInterface;

describe('DummyProcessingPass', function () {

    beforeEach(function () {

        $this->pass = new DummyProcessingPass;

    });

    it('should implement ProcessingPassInterface', function () {

        expect($this->pass)->toBeAnInstanceOf(ProcessingPassInterface::class);

    });

    context('->aliases()', function () {

        it('should return an empty array', function () {

            $test = $this->pass->aliases('id');

            expect($test)->toEqual([]);

        });

    });

    context('->tags()', function () {

        it('should return an empty array', function () {

            $test = $this->pass->tags('id1', 'id2', 'id3');

            expect($test)->toEqual([]);

        });

    });

    context('->processed()', function () {

        it('should return the given factory', function () {

            $factory = function () {};

            $test = $this->pass->processed('id', $factory);

            expect($test)->toBe($factory);

        });

    });

});

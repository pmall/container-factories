<?php

use function Eloquent\Phony\Kahlan\stub;

use Quanta\Container\TaggingPass;
use Quanta\Container\ProcessingPassInterface;

describe('TaggingPass', function () {

    beforeEach(function () {

        $this->predicate = stub();

        $this->pass = new TaggingPass('id', $this->predicate);

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

        it('should return an array associating the id to the ones evaluated to true by the predicate', function () {

            $this->predicate->with('id1')->returns(true);
            $this->predicate->with('id2')->returns(false);
            $this->predicate->with('id3')->returns(true);

            $test = $this->pass->tags('id1', 'id2', 'id3');

            expect($test)->toEqual(['id' => ['id1', 'id3']]);

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

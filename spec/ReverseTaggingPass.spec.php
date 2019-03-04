<?php

use function Eloquent\Phony\Kahlan\stub;

use Quanta\Container\ReverseTaggingPass;
use Quanta\Container\ProcessingPassInterface;
use Quanta\Container\Factories\Tag;
use Quanta\Container\Factories\Extension;
use Quanta\Container\Factories\EmptyArrayFactory;

describe('ReverseTaggingPass', function () {

    beforeEach(function () {

    $this->pass = new ReverseTaggingPass('id', $this->predicate = stub());

    });

    it('should implement ProcessingPassInterface', function () {

        expect($this->pass)->toBeAnInstanceOf(ProcessingPassInterface::class);

    });

    context('->processed()', function () {

        context('when no id of the associative array of factories is matched by the predicate', function () {

            it('should add a tag returning an empty array', function () {

                $this->predicate->with('id1')->returns(false);
                $this->predicate->with('id2')->returns(false);
                $this->predicate->with('id3')->returns(false);

                $test = $this->pass->processed([
                    'id1' => $factory1 = function () {},
                    'id2' => $factory2 = function () {},
                    'id3' => $factory3 = function () {},
                ]);

                expect($test)->toBeAn('array');
                expect($test)->toHaveLength(4);
                expect($test['id1'])->toBe($factory1);
                expect($test['id2'])->toBe($factory2);
                expect($test['id3'])->toBe($factory3);
                expect($test['id'])->toEqual(new EmptyArrayFactory);

            });

        });

        context('when at least one id of the associative array of factories is matched by the predicate', function () {

            it('should add a tag returning the matched ids', function () {

                $this->predicate->with('id1')->returns(true);
                $this->predicate->with('id2')->returns(false);
                $this->predicate->with('id3')->returns(true);

                $test = $this->pass->processed([
                    'id1' => $factory1 = function () {},
                    'id2' => $factory2 = function () {},
                    'id3' => $factory3 = function () {},
                ]);

                expect($test)->toBeAn('array');
                expect($test)->toHaveLength(4);
                expect($test['id1'])->toBe($factory1);
                expect($test['id2'])->toBe($factory2);
                expect($test['id3'])->toBe($factory3);
                expect($test['id'])->toEqual(
                    new Extension(
                        new Extension(
                            new EmptyArrayFactory,
                            new Tag('id1')
                        ),
                        new Tag('id3')
                    )
                );

            });

        });

    });

});

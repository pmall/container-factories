<?php

use function Eloquent\Phony\Kahlan\stub;

use Quanta\Container\TaggingPass;
use Quanta\Container\ProcessingPassInterface;
use Quanta\Container\Factories\Tag;
use Quanta\Container\Factories\Extension;
use Quanta\Container\Factories\EmptyArrayFactory;

describe('TaggingPass::instance()', function () {

    it('should return a new TaggingPass with the given id and predicate', function () {

        $predicate = function () {};

        $test = TaggingPass::instance('id', $predicate);

        expect($test)->toEqual(new TaggingPass('id', $predicate));

    });

});

describe('TaggingPass', function () {

    beforeEach(function () {

    $this->pass = new TaggingPass('tag', $this->predicate = stub());

    });

    it('should implement ProcessingPassInterface', function () {

        expect($this->pass)->toBeAnInstanceOf(ProcessingPassInterface::class);

    });

    context('->processed()', function () {

        context('when the given associative array of factories does not have the tag id as a key', function () {

            context('when no key of the given associative array of factories is matched by the predicate', function () {

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
                    expect($test['tag'])->toEqual(new EmptyArrayFactory);

                });

            });

            context('when at least one key of the given associative array of factories is matched by the predicate', function () {

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
                    expect($test['tag'])->toEqual(new Extension(
                        new Extension(new EmptyArrayFactory, new Tag('id1')),
                        new Tag('id3')
                    ));

                });

            });

        });

        context('when the given associative array of factories has the tag id as a key', function () {

            context('when no key of the given associative array of factories is matched by the predicate', function () {

                it('should return the given associative array of factory', function () {

                    $this->predicate->with('id1')->returns(false);
                    $this->predicate->with('id2')->returns(false);
                    $this->predicate->with('id3')->returns(false);

                    $test = $this->pass->processed([
                        'id1' => $factory1 = function () {},
                        'id2' => $factory2 = function () {},
                        'id3' => $factory3 = function () {},
                        'tag' => $tag = function () {},
                    ]);

                    expect($test)->toBeAn('array');
                    expect($test)->toHaveLength(4);
                    expect($test['id1'])->toBe($factory1);
                    expect($test['id2'])->toBe($factory2);
                    expect($test['id3'])->toBe($factory3);
                    expect($test['tag'])->toBe($tag);

                    // it should not try to tag itself...
                    $this->predicate->never()->calledWith('tag');

                });

            });

            context('when at least one key of the given associative array of factories is matched by the predicate', function () {

                it('should extend the factory with a tag returning the matched ids', function () {

                    $this->predicate->with('id1')->returns(true);
                    $this->predicate->with('id2')->returns(false);
                    $this->predicate->with('id3')->returns(true);

                    $test = $this->pass->processed([
                        'id1' => $factory1 = function () {},
                        'id2' => $factory2 = function () {},
                        'id3' => $factory3 = function () {},
                        'tag' => $tag = function () {},
                    ]);

                    expect($test)->toBeAn('array');
                    expect($test)->toHaveLength(4);
                    expect($test['id1'])->toBe($factory1);
                    expect($test['id2'])->toBe($factory2);
                    expect($test['id3'])->toBe($factory3);
                    expect($test['tag'])->toEqual(new Extension(
                        new Extension($tag, new Tag('id1')),
                        new Tag('id3')
                    ));

                    // it should not try to tag itself...
                    $this->predicate->never()->calledWith('tag');

                });

            });

        });

    });

});

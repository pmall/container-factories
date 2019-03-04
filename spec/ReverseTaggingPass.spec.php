<?php

use function Eloquent\Phony\Kahlan\stub;

use Quanta\Container\ReverseTaggingPass;
use Quanta\Container\ProcessingPassInterface;
use Quanta\Container\Factories\Tag;
use Quanta\Container\Factories\Extension;
use Quanta\Container\Factories\EmptyArrayFactory;

describe('ReverseTaggingPass', function () {

    context('when all the values of the associative array of predicates are callable', function () {

        beforeEach(function () {

            $this->pass = new ReverseTaggingPass([
                'id2' => $this->predicate2 = stub(),
                'id4' => $this->predicate4 = stub(),
                'id6' => $this->predicate6 = stub(),
                'id8' => $this->predicate8 = stub(),
            ]);

        });

        it('should implement ProcessingPassInterface', function () {

            expect($this->pass)->toBeAnInstanceOf(ProcessingPassInterface::class);

        });

        context('->processed()', function () {

            it('should tag the given associative array of factories', function () {

                $this->predicate2->with('id1')->returns(true);
                $this->predicate2->with('id3')->returns(true);
                $this->predicate2->with('id4')->returns(false);
                $this->predicate2->with('id5')->returns(true);
                $this->predicate2->with('id7')->returns(false);

                $this->predicate4->with('id1')->returns(false);
                $this->predicate4->with('id3')->returns(true);
                $this->predicate4->with('id4')->returns(false);
                $this->predicate4->with('id5')->returns(true);
                $this->predicate4->with('id7')->returns(false);

                $this->predicate6->with('id1')->returns(false);
                $this->predicate6->with('id3')->returns(true);
                $this->predicate6->with('id4')->returns(false);
                $this->predicate6->with('id5')->returns(true);
                $this->predicate6->with('id7')->returns(true);

                $this->predicate8->with('id1')->returns(false);
                $this->predicate8->with('id3')->returns(false);
                $this->predicate8->with('id4')->returns(false);
                $this->predicate8->with('id5')->returns(false);
                $this->predicate8->with('id7')->returns(false);

                $test = $this->pass->processed([
                    'id1' => $factory1 = function () {},
                    'id3' => $factory3 = function () {},
                    'id4' => $factory4 = function () {},
                    'id5' => $factory5 = function () {},
                    'id7' => $factory7 = function () {},
                ]);

                expect($test['id1'])->toBe($factory1);

                expect($test['id2'])->toEqual(new Extension(
                    new Extension(
                        new Extension(
                            new EmptyArrayFactory,
                            new Tag('id1')
                        ),
                        new Tag('id3')
                    ),
                    new Tag('id5')
                ));

                expect($test['id3'])->toBe($factory3);

                expect($test['id4'])->toEqual(new Extension(
                    new Extension(
                        new EmptyArrayFactory,
                        new Tag('id3')
                    ),
                    new Tag('id5')
                ));

                expect($test['id5'])->toBe($factory5);

                expect($test['id6'])->toEqual(new Extension(
                    new Extension(
                        new Extension(
                            new EmptyArrayFactory,
                            new Tag('id3')
                        ),
                        new Tag('id5')
                    ),
                    new Tag('id7')
                ));

                expect($test['id7'])->toBe($factory7);

                expect($test['id8'])->toEqual(new EmptyArrayFactory);

            });

        });

    });

    context('when a value of the associative array of predicates is not a callable', function () {

        it('should throw an InvalidArgumentException', function () {

            $test = function () {
                new ReverseTaggingPass([
                    'id1' => function () {},
                    'id2' => 2,
                    'id3' => function () {},
                ]);
            };

            expect($test)->toThrow(new InvalidArgumentException);

        });

    });

});

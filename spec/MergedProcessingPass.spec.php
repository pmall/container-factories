<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\MergedProcessingPass;
use Quanta\Container\ProcessingPassInterface;

describe('MergedProcessingPass', function () {

    context('when there is no processing pass', function () {

        beforeEach(function () {

            $this->pass = new MergedProcessingPass;

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

    context('when there is at leat one processing pass', function () {

        beforeEach(function () {

            $this->pass1 = mock(ProcessingPassInterface::class);
            $this->pass2 = mock(ProcessingPassInterface::class);
            $this->pass3 = mock(ProcessingPassInterface::class);

            $this->pass = new MergedProcessingPass(
                $this->pass1->get(),
                $this->pass2->get(),
                $this->pass3->get()
            );

        });

        it('should implement ProcessingPassInterface', function () {

            expect($this->pass)->toBeAnInstanceOf(ProcessingPassInterface::class);

        });

        context('->aliases()', function () {

            it('should merge the arrays returned by the passes for the given id', function () {

                $this->pass1->aliases->with('id')->returns(['alias1', 'alias3']);
                $this->pass2->aliases->with('id')->returns([]);
                $this->pass3->aliases->with('id')->returns(['alias2', 'alias3']);

                $test = $this->pass->aliases('id');

                expect($test)->toBeAn('array');
                expect($test)->toHaveLength(3);
                expect($test)->toContain('alias1');
                expect($test)->toContain('alias2');
                expect($test)->toContain('alias3');

            });

        });

        context('->tags()', function () {

            it('should merge the arrays returned by the passes for the given ids', function () {

                $this->pass1->tags->with('id1', 'id2', 'id3')->returns([
                    'tag1' => ['id1', 'id2'],
                    'tag3' => ['id1', 'id2'],
                ]);

                $this->pass2->tags->with('id1', 'id2', 'id3')->returns([]);

                $this->pass3->tags->with('id1', 'id2', 'id3')->returns([
                    'tag2' => ['id1', 'id3'],
                    'tag3' => ['id2', 'id3'],
                ]);

                $test = $this->pass->tags('id1', 'id2', 'id3');

                expect($test)->toBeAn('array');
                expect($test)->toHaveLength(3);
                expect($test['tag1'])->toBeAn('array');
                expect($test['tag1'])->toHaveLength(2);
                expect($test['tag1'])->toContain('id1');
                expect($test['tag1'])->toContain('id2');
                expect($test['tag2'])->toBeAn('array');
                expect($test['tag2'])->toHaveLength(2);
                expect($test['tag2'])->toContain('id1');
                expect($test['tag2'])->toContain('id3');
                expect($test['tag3'])->toBeAn('array');
                expect($test['tag3'])->toHaveLength(3);
                expect($test['tag3'])->toContain('id1');
                expect($test['tag3'])->toContain('id2');
                expect($test['tag3'])->toContain('id3');

            });

        });

        context('->processed()', function () {

            it('should sequentially process the given factory with the passes', function () {

                $factory1 = function () {};
                $factory2 = function () {};
                $factory3 = function () {};

                $this->pass1->processed->with('id', $factory1)->returns($factory2);
                $this->pass2->processed->with('id', $factory2)->returns($factory2);
                $this->pass3->processed->with('id', $factory2)->returns($factory3);

                $test = $this->pass->processed('id', $factory1);

                expect($test)->toBe($factory3);

            });

        });

    });

});

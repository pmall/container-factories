<?php

use Quanta\Container\Metadata;

describe('Metadata', function () {

    context('when there is no metadata array', function () {

        beforeEach(function () {

            $this->metadata = new Metadata;

        });

        describe('->for()', function () {

            it('should return an empty array', function () {

                $test = $this->metadata->for('id');

                expect($test)->toEqual([]);

            });

        });

    });

    context('when there is at least one metadata array', function () {

        beforeEach(function () {

            $this->metadata = new Metadata(...[
                [
                    'id1' => ['k111' => 'm111'],
                    'id2' => ['k121' => 'm121', 'k122' => 'm122', 'k123' => 'm123'],
                    'id3' => ['k131' => 'm131'],
                ],
                [
                    'id1' => ['k211' => 'm211'],
                    'id3' => ['k231' => 'm231'],
                ],
                [
                    'id1' => ['k311' => 'm311'],
                    'id2' => ['k321' => 'm321', 'k122' => 'm322', 'k323' => 'm323'],
                    'id3' => ['k331' => 'm331'],
                ],
            ]);

        });

        describe('->for()', function () {

            it('should merge the metadata associated to the given id', function () {

                $test = $this->metadata->for('id2');

                expect($test)->toEqual([
                    'k121' => 'm121',
                    'k122' => 'm322',
                    'k123' => 'm123',
                    'k321' => 'm321',
                    'k323' => 'm323',
                ]);

            });

        });

    });

});

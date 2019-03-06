<?php

use Quanta\Container\Tagging\Entries;

describe('Entries', function () {

    beforeEach(function () {

        $this->predicate = new Entries('id1', 'id2', 'id3');

    });

    describe('->__invoke()', function () {

        context('when the given id is not in the list', function () {

            it('should return false', function () {

                $test = ($this->predicate)('id');

                expect($test)->toBeFalsy();

            });

        });

        context('when the given id is in the list', function () {

            it('should return true', function () {

                $test = ($this->predicate)('id2');

                expect($test)->toBeTruthy();

            });

        });

    });

});

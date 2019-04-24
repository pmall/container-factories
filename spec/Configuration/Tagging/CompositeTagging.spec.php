<?php

use function Eloquent\Phony\Kahlan\stub;

use Quanta\Container\Configuration\Tagging\CompositeTagging;

describe('CompositeTagging', function () {

    beforeEach(function () {

        $this->predicate1 = stub();
        $this->predicate2 = stub();
        $this->predicate3 = stub();

        $this->predicate = new CompositeTagging(
            $this->predicate1,
            $this->predicate2,
            $this->predicate3
        );

    });

    describe('->__invoke()', function () {

        context('when the given string does not satisfy any predicate', function () {

            it('should return false', function () {

                $this->predicate1->with('id')->returns(false);
                $this->predicate2->with('id')->returns(false);
                $this->predicate3->with('id')->returns(false);

                $test = ($this->predicate)('id');

                expect($test)->toBeFalsy();

            });

        });

        context('when the given string satisfy at least one predicate', function () {

            it('should return true', function () {

                $this->predicate1->with('id')->returns(false);
                $this->predicate2->with('id')->returns(true);
                $this->predicate3->with('id')->returns(false);

                $test = ($this->predicate)('id');

                expect($test)->toBeTruthy();

            });
        });

    });

});

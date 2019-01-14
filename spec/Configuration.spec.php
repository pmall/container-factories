<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\Configuration;
use Quanta\Container\ConfigurationInterface;
use Quanta\Container\ConfigurationEntryInterface;

describe('Configuration', function () {

    beforeEach(function () {

        $this->entry1 = mock(ConfigurationEntryInterface::class);
        $this->entry2 = mock(ConfigurationEntryInterface::class);
        $this->entry3 = mock(ConfigurationEntryInterface::class);

        $this->configuration = new Configuration(...[
            $this->entry1->get(),
            $this->entry2->get(),
            $this->entry3->get(),
        ]);

    });

    it('should implement ConfigurationInterface', function () {

        expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

    });

    describe('->entries()', function () {

        it('should return the entries', function () {

            $test = $this->configuration->entries();

            expect($test)->toBeAn('array');
            expect($test)->toHaveLength(3);
            expect($test[0])->toBe($this->entry1->get());
            expect($test[1])->toBe($this->entry2->get());
            expect($test[2])->toBe($this->entry3->get());

        });

    });

});

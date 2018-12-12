<?php

use function Eloquent\Phony\Kahlan\mock;

use Test\TestFactory;

use Interop\Container\ServiceProviderInterface;

use Quanta\Container\Factories\Extension;
use Quanta\Container\FactoryMapInterface;
use Quanta\Container\ServiceProviderFactoryMap;
use Quanta\Exceptions\ArrayReturnTypeErrorMessage;

require_once __DIR__ . '/test/classes.php';

describe('ServiceProviderFactoryMap', function () {

    it('should implement FactoryMapInterface', function () {

        expect(new ServiceProviderFactoryMap)->toBeAnInstanceOf(FactoryMapInterface::class);

    });

    describe('->factories()', function () {

        beforeEach(function () {

            $this->provider1 = mock(ServiceProviderInterface::class);
            $this->provider2 = mock(ServiceProviderInterface::class);
            $this->provider3 = mock(ServiceProviderInterface::class);

            $this->provider1->getFactories->returns([]);
            $this->provider2->getFactories->returns([]);
            $this->provider3->getFactories->returns([]);
            $this->provider1->getExtensions->returns([]);
            $this->provider2->getExtensions->returns([]);
            $this->provider3->getExtensions->returns([]);

        });

        context('when there is no service provider', function () {

            it('should return an empty array', function () {

                $map = new ServiceProviderFactoryMap;

                $test = $map->factories();

                expect($test)->toEqual([]);

            });

        });

        context('when there is at least one service provider', function () {

            beforeEach(function () {

                $this->map = new ServiceProviderFactoryMap(...[
                    $this->provider1->get(),
                    $this->provider2->get(),
                    $this->provider3->get(),
                ]);

            });

            context('when all the service providers ->getFactories() method return only callables', function () {

                context('when all the service providers ->getExtensions() method return only callables', function () {

                    beforeEach(function () {

                        $this->provider1->getFactories->returns([
                            'id1' => new TestFactory('f1'),
                            'id2' => new TestFactory('f2'),
                            'id3' => new TestFactory('f3'),
                        ]);

                        $this->provider2->getFactories->returns([
                            'id2' => new TestFactory('f4'),
                            'id3' => new TestFactory('f5'),
                            'id4' => new TestFactory('f6'),
                        ]);

                        $this->provider3->getFactories->returns([
                            'id3' => new TestFactory('f7'),
                            'id4' => new TestFactory('f8'),
                            'id5' => new TestFactory('f9'),
                        ]);

                    });

                    it('should merge the factories provided by the service providers', function () {

                        $test = $this->map->factories();

                        expect($test)->toBeAn('array');
                        expect($test)->toHaveLength(5);
                        expect($test['id1'])->toEqual(new TestFactory('f1'));
                        expect($test['id2'])->toEqual(new TestFactory('f4'));
                        expect($test['id3'])->toEqual(new TestFactory('f7'));
                        expect($test['id4'])->toEqual(new TestFactory('f8'));
                        expect($test['id5'])->toEqual(new TestFactory('f9'));

                    });

                    it('should extend the factories with the extensions provided by the service providers', function () {

                        $this->provider1->getExtensions->returns([
                            'id2' => new TestFactory('e1'),
                            'id3' => new TestFactory('e2'),
                        ]);

                        $this->provider2->getExtensions->returns([
                            'id1' => new TestFactory('e4'),
                            'id3' => new TestFactory('e5'),
                        ]);

                        $this->provider3->getExtensions->returns([
                            'id1' => new TestFactory('e7'),
                            'id2' => new TestFactory('e8'),
                        ]);

                        $test = $this->map->factories();

                        expect($test)->toBeAn('array');
                        expect($test)->toHaveLength(5);
                        expect($test['id1'])->toEqual(new Extension(
                            new Extension(new TestFactory('f1'), new TestFactory('e4')), new TestFactory('e7')
                        ));
                        expect($test['id2'])->toEqual(new Extension(
                            new Extension(new TestFactory('f4'), new TestFactory('e1')), new TestFactory('e8')
                        ));
                        expect($test['id3'])->toEqual(new Extension(
                            new Extension(new TestFactory('f7'), new TestFactory('e2')), new TestFactory('e5')
                        ));

                    });

                    it('should return extensions with no corresponding factory', function () {

                        $this->provider1->getExtensions->returns([
                            'id6' => new TestFactory('e1'),
                        ]);

                        $this->provider2->getExtensions->returns([
                            'id6' => new TestFactory('e2'),
                        ]);

                        $this->provider3->getExtensions->returns([
                            'id6' => new TestFactory('e3'),
                        ]);

                        $test = $this->map->factories();

                        expect($test)->toBeAn('array');
                        expect($test)->toHaveLength(6);
                        expect($test['id6'])->toEqual(new Extension(
                            new Extension(new TestFactory('e1'), new TestFactory('e2')), new TestFactory('e3')
                        ));

                    });

                });

                context('when one service provider ->getExtensions() method does not return only callable', function () {

                    it('should throw an UnexpectedValueException', function () {

                        $invalid = [
                            'id2' => new TestFactory('f4'),
                            'id3' => 1,
                            'id4' => new TestFactory('f6'),
                        ];

                        $this->provider1->getExtensions->returns([
                            'id1' => new TestFactory('f1'),
                            'id2' => new TestFactory('f2'),
                            'id3' => new TestFactory('f3'),
                        ]);

                        $this->provider2->getExtensions->returns($invalid);

                        $this->provider3->getExtensions->returns([
                            'id3' => new TestFactory('f7'),
                            'id4' => new TestFactory('f8'),
                            'id5' => new TestFactory('f9'),
                        ]);


                        expect([$this->map, 'factories'])->toThrow(
                            new UnexpectedValueException(
                                (string) new ArrayReturnTypeErrorMessage(
                                    sprintf('%s::getExtensions()', get_class($this->provider2->get())),
                                    'callable',
                                    $invalid
                                )
                            )
                        );

                    });

                });

            });

            context('when one service provider ->getFactories() method does not return only callable', function () {

                it('should throw an UnexpectedValueException', function () {

                    $invalid = [
                        'id2' => new TestFactory('f4'),
                        'id3' => 1,
                        'id4' => new TestFactory('f6'),
                    ];

                    $this->provider1->getFactories->returns([
                        'id1' => new TestFactory('f1'),
                        'id2' => new TestFactory('f2'),
                        'id3' => new TestFactory('f3'),
                    ]);

                    $this->provider2->getFactories->returns($invalid);

                    $this->provider3->getFactories->returns([
                        'id3' => new TestFactory('f7'),
                        'id4' => new TestFactory('f8'),
                        'id5' => new TestFactory('f9'),
                    ]);

                    expect([$this->map, 'factories'])->toThrow(
                        new UnexpectedValueException(
                            (string) new ArrayReturnTypeErrorMessage(
                                sprintf('%s::getFactories()', get_class($this->provider2->get())),
                                'callable',
                                $invalid
                            )
                        )
                    );

                });

            });

        });

    });

});

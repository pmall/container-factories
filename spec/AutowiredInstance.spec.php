<?php declare(strict_types=1);

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\Instance;
use Quanta\Container\FactoryInterface;
use Quanta\Container\AutowiredInstance;
use Quanta\Container\DefinitionInterface;
use Quanta\Container\Parsing\ParsedFactoryInterface;
use Quanta\Container\Parsing\ParameterParserInterface;

require_once __DIR__ . '/.test/classes.php';

describe('AutowiredInstance', function () {

    beforeEach(function () {

        $this->parser = mock(ParameterParserInterface::class);

    });

    context('when the string is not a class name', function () {

        beforeEach(function () {

            $this->definition = new AutowiredInstance('value', $this->parser->get());

        });

        it('should implement DefinitionInterface', function () {

            expect($this->definition)->toBeAnInstanceOf(DefinitionInterface::class);

        });

        describe('->factory()', function () {

            it('should return an instance with the string and no argument', function () {

                $test = $this->definition->factory();

                expect($test)->toEqual(new Instance('value'));

            });

        });

    });

    context('when the string is an interface name', function () {

        beforeEach(function () {

            $this->definition = new AutowiredInstance(
                Test\TestInterface::class,
                $this->parser->get()
            );

        });

        it('should implement DefinitionInterface', function () {

            expect($this->definition)->toBeAnInstanceOf(DefinitionInterface::class);

        });

        describe('->factory()', function () {

            it('should return an instance with the interface name and no argument', function () {

                $test = $this->definition->factory();

                expect($test)->toEqual(new Instance(Test\TestInterface::class));

            });

        });

    });

    context('when the string is an abstract class name', function () {

        beforeEach(function () {

            $this->definition = new AutowiredInstance(
                Test\TestAbstractClass::class,
                $this->parser->get()
            );

        });

        it('should implement DefinitionInterface', function () {

            expect($this->definition)->toBeAnInstanceOf(DefinitionInterface::class);

        });

        describe('->factory()', function () {

            it('should return an instance with the abstract class name and no argument', function () {

                $test = $this->definition->factory();

                expect($test)->toEqual(new Instance(Test\TestAbstractClass::class));

            });

        });

    });

    context('when the string is a trait name', function () {

        beforeEach(function () {

            $this->definition = new AutowiredInstance(
                Test\TestTrait::class,
                $this->parser->get()
            );

        });

        it('should implement DefinitionInterface', function () {

            expect($this->definition)->toBeAnInstanceOf(DefinitionInterface::class);

        });

        describe('->factory()', function () {

            it('should return an instance with the trait name and no argument', function () {

                $test = $this->definition->factory();

                expect($test)->toEqual(new Instance(Test\TestTrait::class));

            });

        });

    });

    context('when the string is a class name', function () {

        context('when the class has no constructor', function () {

            beforeEach(function () {

                $this->definition = new AutowiredInstance(
                    Test\TestClassWithNoConstructor::class,
                    $this->parser->get()
                );

            });

            it('should implement DefinitionInterface', function () {

                expect($this->definition)->toBeAnInstanceOf(DefinitionInterface::class);

            });

            describe('->factory()', function () {

                it('should return an instance with the class name and no argument', function () {

                    $test = $this->definition->factory();

                    expect($test)->toEqual(new Instance(Test\TestClassWithNoConstructor::class));

                });

            });

        });

        context('when the class has a constructor', function () {

            context('when the constructor has no parameter', function () {

                beforeEach(function () {

                    $this->definition = new AutowiredInstance(
                        Test\TestClassWithNoParameter::class,
                        $this->parser->get()
                    );

                });

                it('should implement DefinitionInterface', function () {

                    expect($this->definition)->toBeAnInstanceOf(DefinitionInterface::class);

                });

                describe('->factory()', function () {

                    it('should return an instance with the class name and no argument', function () {

                        $test = $this->definition->factory();

                        expect($test)->toEqual(new Instance(Test\TestClassWithNoParameter::class));

                    });

                });

            });

            context('when the constructor has parameters', function () {

                beforeEach(function () {

                    $this->definition = new AutowiredInstance(
                        Test\TestClassWithParameters::class,
                        $this->parser->get()
                    );

                });

                it('should implement DefinitionInterface', function () {

                    expect($this->definition)->toBeAnInstanceOf(DefinitionInterface::class);

                });

                describe('->factory()', function () {

                    beforeEach(function () {

                        $reflection = new ReflectionClass(Test\TestClassWithParameters::class);

                        $this->parameters = $reflection->getConstructor()->getParameters();

                        $this->factory1 = mock(FactoryInterface::class);
                        $this->factory2 = mock(FactoryInterface::class);
                        $this->factory3 = mock(FactoryInterface::class);

                        $this->parsed1 = mock(ParsedFactoryInterface::class);
                        $this->parsed2 = mock(ParsedFactoryInterface::class);
                        $this->parsed3 = mock(ParsedFactoryInterface::class);
                        $this->parsed4 = mock(ParsedFactoryInterface::class);

                        $this->parser->__invoke
                            ->with($this->parameters[0])
                            ->returns($this->parsed1);

                        $this->parser->__invoke
                            ->with($this->parameters[1])
                            ->returns($this->parsed2);

                        $this->parser->__invoke
                            ->with($this->parameters[2])
                            ->returns($this->parsed3);

                        $this->parser->__invoke
                            ->with($this->parameters[3])
                            ->returns($this->parsed4);

                        $this->parsed1->factory->returns($this->factory1);
                        $this->parsed2->factory->returns($this->factory2);
                        $this->parsed3->factory->returns($this->factory3);

                    });

                    context('when all the constructor parameters are successfully parsed as factories', function () {

                        it('should return an instance with the class name and the parsed factories', function () {

                            $this->parsed1->isParsed->returns(true);
                            $this->parsed2->isParsed->returns(true);
                            $this->parsed3->isParsed->returns(true);
                            $this->parsed4->isParsed->returns(false);

                            $test = $this->definition->factory();

                            expect($test)->toEqual(new Instance(Test\TestClassWithParameters::class, ...[
                                $this->factory1->get(),
                                $this->factory2->get(),
                                $this->factory3->get(),
                            ]));

                        });

                    });

                    context('when one constructor parameter is not successfully parsed as a factory', function () {

                        it('should throw a LogicException containing the parameter string representation', function () {

                            $this->parsed1->isParsed->returns(true);
                            $this->parsed2->isParsed->returns(false);
                            $this->parsed3->isParsed->returns(true);
                            $this->parsed4->isParsed->returns(false);

                            $test = '';

                            try {
                                $this->definition->factory();
                            }

                            catch (LogicException $e) {
                                $test = $e->getMessage();
                            }

                            expect($test)->not->toContain((string) $this->parameters[0]);
                            expect($test)->toContain((string) $this->parameters[1]);
                            expect($test)->not->toContain((string) $this->parameters[2]);

                        });

                    });

                    context('when more than one constructor parameter is not successfully parsed as a factory', function () {

                        it('should throw a LogicException containing the parameters string representation', function () {

                            $this->parsed1->isParsed->returns(false);
                            $this->parsed2->isParsed->returns(false);
                            $this->parsed3->isParsed->returns(false);
                            $this->parsed4->isParsed->returns(false);

                            $test = '';

                            try {
                                $this->definition->factory();
                            }

                            catch (LogicException $e) {
                                $test = $e->getMessage();
                            }

                            expect($test)->toContain(implode('', [
                                (string) $this->parameters[0],
                                ', ',
                                (string) $this->parameters[1],
                                ' and ',
                                (string) $this->parameters[2],
                            ]));

                        });

                    });

                });

            });

        });

    });

});

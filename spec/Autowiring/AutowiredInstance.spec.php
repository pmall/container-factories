<?php declare(strict_types=1);

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\Instance;
use Quanta\Container\FactoryInterface;
use Quanta\Container\DefinitionInterface;
use Quanta\Container\Parsing\ParsingResultInterface;
use Quanta\Container\Autowiring\AutowiredInstance;
use Quanta\Container\Autowiring\ArgumentParserInterface;

require_once __DIR__ . '/../.test/classes.php';

describe('AutowiredInstance', function () {

    beforeEach(function () {

        $this->parser = mock(ArgumentParserInterface::class);

    });

    context('when the string is not a class name', function () {

        beforeEach(function () {

            $this->definition = new AutowiredInstance($this->parser->get(), 'value');

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
                $this->parser->get(),
                Test\TestInterface::class
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
                $this->parser->get(),
                Test\TestAbstractClass::class
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
                $this->parser->get(),
                Test\TestTrait::class
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
                    $this->parser->get(),
                    Test\TestClassWithNoConstructor::class
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
                        $this->parser->get(),
                        Test\TestClassWithNoParameter::class
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
                        $this->parser->get(),
                        Test\TestClassWithParameters::class
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

                        $this->parsed1 = mock(ParsingResultInterface::class);
                        $this->parsed2 = mock(ParsingResultInterface::class);
                        $this->parsed3 = mock(ParsingResultInterface::class);

                        $this->parser->__invoke->with($this->parameters[0])->returns($this->parsed1);
                        $this->parser->__invoke->with($this->parameters[1])->returns($this->parsed2);
                        $this->parser->__invoke->with($this->parameters[2])->returns($this->parsed3);

                        $this->parsed1->factory->returns($this->factory1);
                        $this->parsed2->factory->returns($this->factory2);
                        $this->parsed3->factory->returns($this->factory3);

                    });

                    context('when all the constructor parameters are successfully parsed as factories', function () {

                        it('should return an instance with the class name and the parsed factories', function () {

                            $this->parsed1->isParsed->returns(true);
                            $this->parsed2->isParsed->returns(true);
                            $this->parsed3->isParsed->returns(true);

                            $test = $this->definition->factory();

                            expect($test)->toEqual(new Instance(Test\TestClassWithParameters::class, ...[
                                $this->factory1->get(),
                                $this->factory2->get(),
                                $this->factory3->get(),
                            ]));

                            // ensure variadic parameters are not parsed.
                            $this->parser->__invoke->never()->calledWith($this->parameters[3]);

                        });

                    });

                    context('when one constructor parameter is not successfully parsed as a factory', function () {

                        it('should throw a LogicException containing the parameter string representation', function () {

                            $this->parsed1->isParsed->returns(true);
                            $this->parsed2->isParsed->returns(false);
                            $this->parsed3->isParsed->returns(true);

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

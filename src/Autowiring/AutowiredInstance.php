<?php declare(strict_types=1);

namespace Quanta\Container\Autowiring;

use Quanta\Container\Instance;
use Quanta\Container\FactoryInterface;
use Quanta\Container\FactoryDefinitionInterface;

final class AutowiredInstance implements FactoryDefinitionInterface
{
    /**
     * The parser used to parse constructor parameter reflections as factories.
     *
     * @var \Quanta\Container\Autowiring\ArgumentParserInterface
     */
    private $parser;

    /**
     * The name of the class to instantiate.
     *
     * @var string
     */
    private $class;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\Autowiring\ArgumentParserInterface  $parser
     * @param string                                                $class
     */
    public function __construct(ArgumentParserInterface $parser, string $class)
    {
        $this->parser = $parser;
        $this->class = $class;
    }

    /**
     * @inheritdoc
     */
    public function factory(): FactoryInterface
    {
        $unbound = [];
        $factories = [];

        $parameters = $this->parameters();

        foreach ($parameters as $parameter) {
            $result = ($this->parser)($parameter);

            $result->isParsed()
                ? $factories[] = $result->factory()
                : $unbound[] = $parameter;
        }

        if (count($unbound) == 0) {
            return new Instance($this->class, ...$factories);
        }

        $last = array_pop($unbound);

        throw new \LogicException(
            vsprintf('Unable to autowire %s::__construct() because no argument is bound to %s', [
                $this->class,
                count($unbound) == 0 ? $last : vsprintf('%s and %s', [
                    implode(', ', array_map('strval', $unbound)),
                    $last,
                ])
            ])
        );
    }

    /**
     * Return the non variadic class constructor parameter reflections.
     *
     * When the string is not a class name the method does not fail and returns
     * an empty array. This way the script keeps going until the factory tries
     * to instantiate a non class and fail like it would by manually doing so.
     *
     * @return \ReflectionParameter[]
     */
    private function parameters(): array
    {
        try {
            $reflection = new \ReflectionClass($this->class);
        }

        catch (\ReflectionException $e) {
            return [];
        }

        $constructor = $reflection->getConstructor();

        $parameters = is_null($constructor) ? [] : $constructor->getParameters();

        return array_filter($parameters, function ($parameter) {
            return ! $parameter->isVariadic();
        });
    }
}

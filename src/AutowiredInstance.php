<?php declare(strict_types=1);

namespace Quanta\Container;

use Quanta\Container\Instance;
use Quanta\Container\FactoryInterface;
use Quanta\Container\DefinitionInterface;
use Quanta\Container\Parsing\ParameterParserInterface;

final class AutowiredInstance implements DefinitionInterface
{
    /**
     * The parser used to parse constructor parameter reflections as factories.
     *
     * @var \Quanta\Container\Parsing\ParameterParserInterface
     */
    private $parser;

    /**
     * The name of the class to instantiate.
     *
     * @var string
     */
    private $class;

    /**
     * The autowiring options.
     *
     * @var array
     */
    private $options;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\Parsing\ParameterParserInterface    $parser
     * @param string                                                $class
     * @param array                                                 $options
     */
    public function __construct(ParameterParserInterface $parser, string $class, array $options = [])
    {
        $this->parser = $parser;
        $this->class = $class;
        $this->options = $options;
    }

    /**
     * @inheritdoc
     */
    public function factory(): FactoryInterface
    {
        $factories = [];
        $unbound = [];

        foreach ($this->parameters() as $parameter) {
            $result = ($this->parser)($parameter, $this->options);

            if ($result->isParsed()) {
                $factories[] = $result->factory();
            } elseif (! $parameter->isOptional()) {
                $unbound[] = $parameter;
            }
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

        return is_null($constructor) ? [] : $constructor->getParameters();
    }
}

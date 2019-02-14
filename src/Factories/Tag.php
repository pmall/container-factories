<?php declare(strict_types=1);

namespace Quanta\Container\Factories;

use Psr\Container\ContainerInterface;

use Quanta\Container\Compilation\ArrayStr;
use Quanta\Container\Compilation\Template;

use function Quanta\Exceptions\areAllTypedAs;
use Quanta\Exceptions\ArrayArgumentTypeErrorMessage;

final class Tag implements CompilableFactoryInterface
{
    /**
     * The ids of the tagged container entries.
     *
     * @var string[]
     */
    private $ids;

    /**
     * Constructor.
     *
     * @param string[] $ids
     * @throws \InvalidArgumentException
     */
    public function __construct(array $ids = [])
    {
        if (! areAllTypedAs('string', $ids)) {
            throw new \InvalidArgumentException(
                (string) new ArrayArgumentTypeErrorMessage(1, 'string', $ids)
            );
        }

        $this->ids = $ids;
    }

    /**
     * @inheritdoc
     */
    public function __invoke(ContainerInterface $container, array $tagged = []): array
    {
        $entries = array_map([$container, 'get'], $this->ids);

        return array_merge($tagged, $entries);
    }

    /**
     * @inheritdoc
     */
    public function compiled(Template $template): string
    {
        return $template
            ->withPrevious('array $tagged = []')
            ->withBodyf('$entries = array_map([$%s, \'get\'], %s);', ...[
                $template->containerVariableName(),
                new ArrayStr(array_map([$this, 'quoted'], $this->ids)),
            ])
            ->strWithReturn('array_merge($tagged, $entries)');
    }

    /**
     * Return the given string with quotes.
     *
     * @param string $str
     * @return string
     */
    private function quoted(string $str): string
    {
        return sprintf('\'%s\'', $str);
    }
}

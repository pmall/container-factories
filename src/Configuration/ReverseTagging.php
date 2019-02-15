<?php declare(strict_types=1);

namespace Quanta\Container\Configuration;

use Quanta\Container\Factories\Tag;

final class ReverseTagging implements ConfigurationPassInterface
{
    /**
     * The id of the tag to register.
     *
     * @var string
     */
    private $id;

    /**
     * The interface/class names the entries must implement/extend to be tagged.
     *
     * @var string[] $classes
     */
    private $classes;

    /**
     * Constructor.
     *
     * @param string $id
     * @param string $class
     * @param string ...$classes
     */
    public function __construct(string $id, string $class, string ...$classes)
    {
        $this->id = $id;
        $this->classes = array_merge([$class], $classes);
    }

    /**
     * @inheritdoc
     */
    public function factories(array $factories, Metadata $metadata): array
    {
        $classes = array_keys($factories);
        $classes = array_filter($classes, [$this, 'filter']);

        // let the tag have the entry names as key.
        $classes = (array) array_combine($classes, $classes);

        return [$this->id => new Tag($classes)];
    }

    /**
     * Return whether the given string is the name of an interface/class
     * implementing/extending all the class names.
     *
     * @param string $class
     * @return bool
     */
    private function filter(string $class): bool
    {
        foreach ($this->classes as $type) {
            if (! is_a($class, $type, true)) {
                return false;
            }
        }

        return true;
    }
}

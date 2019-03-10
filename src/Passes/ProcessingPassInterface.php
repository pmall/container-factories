<?php declare(strict_types=1);

namespace Quanta\Container\Passes;

interface ProcessingPassInterface
{
    /**
     * Return an array of aliases for the given container entry id.
     *
     * Aliases are added to the associative array of factories so it wont
     * overwrite a preexising factory.
     *
     * @param string $id
     * @return string[]
     */
    public function aliases(string $id): array;

    /**
     * Return an associative array of tag to tagged ids from the given ids.
     *
     * Tags are added to the associative array of factories so it wont overwrite
     * a preexising factory.
     *
     * @param string ...$ids
     * @return array[]
     */
    public function tags(string ...$ids): array;

    /**
     * Return a processed factory from the given id => factory pair.
     *
     * @param string    $id
     * @param callable  $factory
     * @return callable
     */
    public function processed(string $id, callable $factory): callable;
}

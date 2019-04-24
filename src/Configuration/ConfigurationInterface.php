<?php declare(strict_types=1);

namespace Quanta\Container\Configuration;

interface ConfigurationInterface
{
    /**
     * Return the associative array of factories.
     *
     * @return callable[]
     */
    public function factories(): array;

    /**
     * Return an associative array of predicates used to tag factories.
     *
     * @return callable[]
     */
    public function mappers(): array;

    /**
     * Return an associative array of extension arrays.
     *
     * @return array[]
     */
    public function extensions(): array;
}

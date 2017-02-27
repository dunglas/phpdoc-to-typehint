<?php

/**
 * @var string
 */
$foo = 'bar';

/**
 * Must not be modified.
 *
 * @return string|null
 */
function foo()
{
}

/**
 * Must be converted.
 *
 * @param int|null $c
 * @param string   $d
 *
 * @return float
 */
function bar(\DateTime $a = null, array $b, int $c = null, string $d, bool $e, callable $f = null): float
{
    return 0.0;
}

/**
 * Must not be modified (no params defined).
 */
function baz($a)
{
}

/**
 * Must not be converted (already using type hints).
 *
 * @param int $a
 *
 * @return string
 */
function bat(int $a): string
{
}

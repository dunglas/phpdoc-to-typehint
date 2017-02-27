<?php

namespace bar;

class Child extends Foo implements BarInterface
{
    use BazTrait;

    public function test(float $a)
    {
        parent::test($a);
    }

    /**
     * {@inheritdoc}
     */
    public function baz(array $a, int $b): float
    {
    }
}

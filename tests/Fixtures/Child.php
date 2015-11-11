<?php

namespace bar;

class Child extends Foo implements BarInterface
{
    use BazTrait;

    public function test($a)
    {
        parent::test($a);
    }

    /**
     * {@inheritdoc}
     */
    public function baz(array $a, $b)
    {
    }
}

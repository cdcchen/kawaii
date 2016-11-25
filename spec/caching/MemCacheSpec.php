<?php

namespace spec\kawaii\caching;

use kawaii\caching\MemCache;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MemCacheSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(MemCache::class);
    }
}

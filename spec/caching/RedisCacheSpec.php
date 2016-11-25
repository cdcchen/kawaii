<?php

namespace spec\kawaii\caching;

use kawaii\caching\RedisCache;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RedisCacheSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(RedisCache::class);
    }
}

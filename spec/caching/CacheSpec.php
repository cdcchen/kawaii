<?php

namespace spec\kawaii\caching;

use kawaii\caching\Cache;
use PhpSpec\ObjectBehavior;

class CacheSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Cache::class);
    }

    public function it_is_has_count()
    {
        $this->count()->shouldBeLike('100');
    }

    public function it_is_throw_runtime_exception()
    {
        $this->shouldThrow('\RuntimeException')->duringExceptionTest(true);
    }

    public function it_is_specify_the_message_of_the_exception()
    {
        $this->shouldThrow(new \RuntimeException('xxx'))->duringExceptionTest(true);
    }

    function it_should_not_allow_negative_ratings()
    {
        $this->shouldNotThrow('\RuntimeException')->duringInstantiation();
    }

    function it_should_be_a_movie()
    {
        $this->shouldHaveType(Cache::class);
        $this->shouldReturnAnInstanceOf(Cache::class);
        $this->shouldBeAnInstanceOf(Cache::class);
        $this->shouldImplement(Cache::class);
    }
}

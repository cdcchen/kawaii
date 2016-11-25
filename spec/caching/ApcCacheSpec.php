<?php
/**
 * kawaii\caching\ApcCache
 */

namespace spec\kawaii\caching;

use PhpSpec\ObjectBehavior;

class ApcCacheSpec extends ObjectBehavior
{
    public function it_is_throw_runtime_exception()
    {
        $this->shouldThrow('\RuntimeException')->duringExceptionTest(true);
    }

}
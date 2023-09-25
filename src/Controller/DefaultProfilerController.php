<?php 
use Symfony\Component\HttpKernel\Profiler\Profiler;
// this controller is for practicing disabling the profiler programatically, not in use in production

class DefaultProfilerController
{
    // ...

    public function lockProfiler(?Profiler $profiler)
    {
        // $profiler won't be set if your environment doesn't have the profiler (like prod, by default)
        if (null !== $profiler) {
            // if it exists, disable the profiler for this particular controller action
            $profiler->disable();
        }

        // ...
    }
}
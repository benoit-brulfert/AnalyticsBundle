<?php

namespace Cethyworks\AnalyticsBundle\Listener;

require_once '../vendor/Analytics-php/lib/analytics.php';

use aba\StudentBundle\Events\UserExerciseWorkedEvent;
/*
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\Response;*/
/*
use aba\StudentBundle\Events\UserExerciseEvents;*/

class Listener 
{
    public function onExerciseStart(UserExerciseWorkedEvent $event)
    {
      var_dump($event->getUserExerciseWorked());die;
      
        \Analytics::init("gf6hp3k4v6");
        \Analytics::identify("019mr8mf4r", array(
        ));
        
        Analytics::track("019mr8mf4r", "start exercise", array(
        ));
    }
}



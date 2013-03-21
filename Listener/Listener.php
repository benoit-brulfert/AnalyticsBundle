<?php

namespace Cethyworks\AnalyticsBundle\Listener;

require_once '../vendor/Analytics-php/lib/analytics.php';

use aba\StudentBundle\Events\UserExerciseWorkedEvent;
use aba\QCMBundle\Events\QcmWorkedEvent;
use Cethyworks\AnalyticsBundle\AnalyticsHandler\AnalyticsHandler;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use aba\VideoBundle\Events\UserVideoWatchEvent;

class Listener 
{
    private $init;

    public function __construct(AnalyticsHandler $init) 
    {
        $this->init = $init;
    }
    
    public function onExerciseStart(UserExerciseWorkedEvent $event)
    {
        $this->init->initialize($event->getUser());
        $uid = $event->getUser()->getUser()->getUid();
        $ex_id = $event->getExerciseId();

        \Analytics::track("$uid", "start.exercise", array(
            "exercise_id"   => "$ex_id",
        ));
    }
    
    public function onAskResult(UserExerciseWorkedEvent $event)
    {
        $this->init->initialize($event->getUser());
        $ex_id = $event->getExerciseId();
        $uid = $event->getUser()->getUser()->getUid();

        \Analytics::track("$uid", "ask.result", array(
            "exercise_id"   => "$ex_id",
        ));
    }
    
    public function onUserConnect(InteractiveLoginEvent $event)
    {
        $this->init->initialize($event->getAuthenticationToken());
        $uid = $event->getAuthenticationToken()->getUser()->getUid();

        \Analytics::track("$uid", "user.connect", array(
        ));   
    }
    
    public function onExerciseAskTip(QcmWorkedEvent $event)
    {       
        $this->init->initialize($event->getUser());
        $uid = $event->getUser()->getUser()->getUid();
        
       $questionId = $event->getQuestionId();
       $tipPosition = $event->getTipPosition();
       
        \Analytics::track("$uid", "exercise.ask_tip", array(
            "question_id"   => "$questionId",
            "tip_position"  => "$tipPosition"
        )); 
    }
    
    public function onVideoPlay(UserVideoWatchEvent $event)
    {

        $this->init->initialize($event->getUser());
        $uid = $event->getUser()->getUser()->getUid();
        
        $videoId = $event->getVideoId();
        
        \Analytics::track("$uid", "exercise.ask_tip", array(
            "video_id"   => "$videoId",
        )); 
    }
}



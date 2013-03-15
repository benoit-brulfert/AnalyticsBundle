<?php

namespace Cethyworks\AnalyticsBundle\AnalyticsHandler;

require_once '../vendor/Analytics-php/lib/analytics.php';

class AnalyticsHandler
{
    public function initialize($user)
    {
        $uid = ($user->getUser()->getUid());
        \Analytics::init("gf6hp3k4v6");
        \Analytics::identify("$uid", array(
        ));
    }   
}
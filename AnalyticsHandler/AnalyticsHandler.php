<?php

namespace Cethyworks\AnalyticsBundle\AnalyticsHandler;

require_once '../vendor/Analytics-php/lib/analytics.php';

class AnalyticsHandler
{
    public function initialize($user)
    {
        $uid = ($user->getUser()->getUid());
        $email = $user->getUser()->getEmail();
        $relatedEmail = $user->getUser()->getRelatedEmail();
        $lastLogin = $user->getUser()->getLastLogin()->format('Y-m-d H:i:s');
        $academy = $user->getUser()->getAcademy();
        $facebook = $user->getUser()->getFacebookID();
        $subscription = $user->getUser()->getCreated()->format('Y-m-d H:i:s');
        $role = $user->getUser()->getType();
        $courseId = $user->getUser()->getCourse()->getId();
        $expireAt = $user->getUser()->getCredentialsExpireAt()->format('Y-m-d H:i:s');
        
        \Analytics::init("gf6hp3k4v6");
        \Analytics::identify("$uid", array(
            "email" => "$email",
            "relatedEmail"  => "$relatedEmail",
            "lastLogin" => "$lastLogin",
            "academy"   => "$academy",
            "facebook"  => "$facebook",
            "courseID"  => "$courseId",
            "subscription"  => "$subscription",
            "role"  => "$role",
            "expireAt"  => "$expireAt",
            
        ));
    }   
}
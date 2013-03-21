<?php

namespace Cethyworks\AnalyticsBundle\Handler;

class AnalyticsHandler
{
    /**
     * @var string
     */
    private $accountId;
    
    public function __construct($accountId)
    {
        $this->accountId = $accountId;
    }
    
    public function initialize($user)
    {
        $uid = ($user->getUser()->getUid());
        \Analytics::init($this->accountId);
        \Analytics::identify("$uid", array(
        ));
    }   
}
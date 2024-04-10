<?php

namespace B24\Devtools\Agent;

class AgentFactory
{
    public static function normal(): AgentManager
    {
        return new AgentManager('N');
    }
}
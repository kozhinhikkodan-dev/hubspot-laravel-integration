<?php

namespace App\Enums;

enum LeadStatusEnum: string
{
    case NEW = 'new';
    case OPEN = 'open';
    case IN_PROGRESS = 'in progress';
    case OPEN_DEAL = 'open deal';
    case UNQUALIFIED = 'unqualified';
    case ATTEMPTED_TO_CONTACT = 'attempted to contact';
    case CONNECTED = 'connected';
    case BAD_TIMING = 'bad timing';

}

<?php

namespace App\Enums;

enum LifeCycleStagesEnum: string
{
    case SUBSCRIBER = 'subscriber';
    case LEAD = 'lead';
    case MARKETING_QUALIFIED_LEAD = 'marketingqualifiedlead';
    case SALES_QUALIFIED_LEAD = 'salesqualifiedlead';
    case OPPORTUNITY = 'opportunity';
    case CUSTOMER = 'customer';
    case EVANGELIST = 'evangelist';
    case OTHER = 'other';

}

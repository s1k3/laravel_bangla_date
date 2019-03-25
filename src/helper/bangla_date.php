<?php

use S1K3\Bangla\Date\BanglaDate;

function bangla_date($timestamp)
{
    return BanglaDate::Instance($timestamp)->get_date();
}


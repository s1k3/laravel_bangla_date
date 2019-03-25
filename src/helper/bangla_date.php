<?php

use Robin\Bangla\Date\BanglaDate;

function bangla_date($timestamp)
{
    return BanglaDate::Instance($timestamp)->get_date();
}


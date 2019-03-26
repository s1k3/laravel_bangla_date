<?php

use S1K3\Bangla\Date\BanglaDate;

function bangla_date($timestamp, $format = "")
{
    $format = !empty($format) ? $format : config("bangla_date.format");
    if ($format === 'bn') {
        return BanglaDate::Instance($timestamp)->get_bangla_date();
    }
    return BanglaDate::Instance($timestamp)->get_english_date();
}


<?php


namespace Robin\Bangla\Date;


class BanglaDate
{
    private $morning;
    private $engHour;
    private $engDate;
    private $engMonth;
    private $engYear;
    private $bangDate;
    private $bangMonth;
    private $bangYear;

    private $bn_months = array("পৌষ", "মাঘ", "ফাল্গুন", "চৈত্র", "বৈশাখ", "জ্যৈষ্ঠ", "আষাঢ়", "শ্রাবণ", "ভাদ্র", "আশ্বিন", "কার্তিক", "অগ্রহায়ণ");
    private $bn_month_dates = array(30, 30, 30, 30, 31, 31, 31, 31, 31, 30, 30, 30);
    private $bn_month_middate = array(13, 12, 14, 13, 14, 14, 15, 15, 15, 15, 14, 14);
    private $lipyearindex = 3;


    private function __construct($timestamp)
    {
        $this->engDate = date('d', $timestamp);
        $this->engMonth = date('m', $timestamp);
        $this->engYear = date('Y', $timestamp);
        $this->morning = config("bangla_date.hour");
        $this->engHour = date('G', $timestamp);
        $this->calculate_date();
        $this->calculate_year();
        $this->convert();
    }

    public static function Instance($timestamp)
    {
        return new BanglaDate($timestamp);
    }

    private function calculate_date()
    {
        $this->bangDate = $this->engDate - $this->bn_month_middate[$this->engMonth - 1];
        if ($this->engHour < $this->morning)
            $this->bangDate -= 1;

        if (($this->engDate <= $this->bn_month_middate[$this->engMonth - 1]) || ($this->engDate == $this->bn_month_middate[$this->engMonth - 1] + 1 && $this->engHour < $this->morning)) {
            $this->bangDate += $this->bn_month_dates[$this->engMonth - 1];
            if ($this->is_leapyear() && $this->lipyearindex == $this->engMonth)
                $this->bangDate += 1;
            $this->bangMonth = $this->bn_months[$this->engMonth - 1];
        } else {
            $this->bangMonth = $this->bn_months[($this->engMonth) % 12];
        }
    }

    function is_leapyear()
    {
        return ($this->engYear % 400 == 0 || ($this->engYear % 100 != 0 && $this->engYear % 4 == 0));
    }

    function calculate_year()
    {
        $this->bangYear = $this->engYear - 593;
        if (($this->engMonth < 4) || (($this->engMonth == 4) && (($this->engDate < 14) || ($this->engDate == 14 && $this->engHour < $this->morning))))
            $this->bangYear -= 1;
    }

    function bangla_number($int)
    {
        $engNumber = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 0);
        $bangNumber = array('১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯', '০');

        $converted = str_replace($engNumber, $bangNumber, $int);
        return $converted;
    }


    function convert()
    {
        $this->bangDate = $this->bangla_number($this->bangDate);
        $this->bangYear = $this->bangla_number($this->bangYear);
    }

    function get_date()
    {
        return $this->bangDate . " " . $this->bangMonth . " " . $this->bangYear;
    }
}
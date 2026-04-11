<?php

namespace S1K3\Bangla\Date;

class BanglaDate
{
    private const MONTHS = [
        'পৌষ', 'মাঘ', 'ফাল্গুন', 'চৈত্র', 'বৈশাখ', 'জ্যৈষ্ঠ',
        'আষাঢ়', 'শ্রাবণ', 'ভাদ্র', 'আশ্বিন', 'কার্তিক', 'অগ্রহায়ণ',
    ];

    private const MONTH_DAYS = [30, 30, 30, 30, 31, 31, 31, 31, 31, 30, 30, 30];

    private const MONTH_MIDDATE = [13, 12, 14, 13, 14, 14, 15, 15, 15, 15, 14, 14];

    /** English month (1-based) in which Phalguna gains an extra day during leap years */
    private const LEAP_MONTH = 3;

    private readonly int $engDate;
    private readonly int $engMonth;
    private readonly int $engYear;
    private readonly int $engHour;
    private readonly int $morning;

    private int $bangDateInt = 0;
    private string $bangDate = '';
    private string $bangMonth = '';
    private int $bangYearInt = 0;
    private string $bangYear = '';

    private function __construct(
        private readonly int $timestamp,
        ?int $morningHour = null,
    ) {
        $this->engDate  = (int) date('d', $timestamp);
        $this->engMonth = (int) date('m', $timestamp);
        $this->engYear  = (int) date('Y', $timestamp);
        $this->engHour  = (int) date('G', $timestamp); // 24-hour (0–23); fixes PM rollback bug
        $this->morning  = $morningHour ?? (int) config('bangla_date.hour', 6);

        $this->calculateDate();
        $this->calculateYear();
        $this->convert();
    }

    /**
     * Create a BanglaDate from a Unix timestamp.
     *
     * @param int      $timestamp   Unix timestamp
     * @param int|null $morningHour Hour (0–23) at which the Bangla day starts;
     *                              defaults to the bangla_date.hour config value.
     */
    public static function instance(int $timestamp, ?int $morningHour = null): static
    {
        return new static($timestamp, $morningHour);
    }

    private function calculateDate(): void
    {
        $middate = self::MONTH_MIDDATE[$this->engMonth - 1];

        $this->bangDateInt = $this->engDate - $middate;

        if ($this->engHour < $this->morning) {
            $this->bangDateInt--;
        }

        $isStillPreviousMonth = $this->engDate <= $middate
            || ($this->engDate === $middate + 1 && $this->engHour < $this->morning);

        if ($isStillPreviousMonth) {
            $days = self::MONTH_DAYS[$this->engMonth - 1];

            if ($this->isLeapYear() && $this->engMonth === self::LEAP_MONTH) {
                $days++;
            }

            $this->bangDateInt += $days;
            $this->bangMonth    = self::MONTHS[$this->engMonth - 1];
        } else {
            $this->bangMonth = self::MONTHS[$this->engMonth % 12];
        }
    }

    public function isLeapYear(): bool
    {
        return $this->engYear % 400 === 0
            || ($this->engYear % 100 !== 0 && $this->engYear % 4 === 0);
    }

    /** @deprecated Use isLeapYear() */
    public function is_leapyear(): bool
    {
        return $this->isLeapYear();
    }

    private function calculateYear(): void
    {
        $this->bangYearInt = $this->engYear - 593;

        $isBeforeNewYear = $this->engMonth < 4
            || ($this->engMonth === 4 && (
                $this->engDate < 14
                || ($this->engDate === 14 && $this->engHour < $this->morning)
            ));

        if ($isBeforeNewYear) {
            $this->bangYearInt--;
        }
    }

    private function convert(): void
    {
        $this->bangDate = $this->banglaNumber($this->bangDateInt);
        $this->bangYear = $this->banglaNumber($this->bangYearInt);
    }

    public function banglaNumber(int|string $number): string
    {
        return str_replace(
            ['1', '2', '3', '4', '5', '6', '7', '8', '9', '0'],
            ['১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯', '০'],
            (string) $number,
        );
    }

    /** @deprecated Use banglaNumber() */
    public function bangla_number(int|string $int): string
    {
        return $this->banglaNumber($int);
    }

    public function banglaFontMonth(string $abbr): string
    {
        static $months = [
            'Jan' => 'জানুয়ারি', 'Feb' => 'ফেব্রুয়ারি', 'Mar' => 'মার্চ',
            'Apr' => 'এপ্রিল',   'May' => 'মে',           'Jun' => 'জুন',
            'Jul' => 'জুলাই',    'Aug' => 'আগস্ট',        'Sep' => 'সেপ্টেম্বর',
            'Oct' => 'অক্টোবর',  'Nov' => 'নভেম্বর',      'Dec' => 'ডিসেম্বর',
        ];

        return $months[$abbr] ?? $abbr;
    }

    /** @deprecated Use banglaFontMonth() */
    public function bangla_font_month(string $name): string
    {
        return $this->banglaFontMonth($name);
    }

    public function getBanglaDate(string $format): string
    {
        return str_replace(
            ['d', 'm', 'y'],
            [$this->bangDate, $this->bangMonth, $this->bangYear],
            $format,
        );
    }

    /** @deprecated Use getBanglaDate() */
    public function get_bangla_date(string $format): string
    {
        return $this->getBanglaDate($format);
    }

    public function getEnglishDate(string $format): string
    {
        return str_replace(
            ['d', 'm', 'y'],
            [
                $this->banglaNumber((int) date('d', $this->timestamp)),
                $this->banglaFontMonth(date('M', $this->timestamp)),
                $this->banglaNumber((int) date('Y', $this->timestamp)),
            ],
            $format,
        );
    }

    /** @deprecated Use getEnglishDate() */
    public function get_english_date(string $format): string
    {
        return $this->getEnglishDate($format);
    }
}

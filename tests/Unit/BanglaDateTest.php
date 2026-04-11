<?php

use S1K3\Bangla\Date\BanglaDate;

// -------------------------------------------------------------------------
// Month transitions
// -------------------------------------------------------------------------

it('shows 1 Baisakh on Bangla New Year (April 14 noon)', function () {
    $bd = BanglaDate::instance(mktime(12, 0, 0, 4, 14, 2024), 6);

    expect($bd->getBanglaDate('d m, y'))->toBe('১ বৈশাখ, ১৪৩১');
});

it('shows 30 Chaitra on the last day of the Bangla year (April 13 noon)', function () {
    $result = BanglaDate::instance(mktime(12, 0, 0, 4, 13, 2024), 6)->getBanglaDate('d m, y');

    expect($result)
        ->toContain('চৈত্র')
        ->toContain('১৪৩০')
        ->toContain('৩০');
});

it('shows 1 Magh on January 14 noon', function () {
    $result = BanglaDate::instance(mktime(12, 0, 0, 1, 14, 2024), 6)->getBanglaDate('d m, y');

    expect($result)->toContain('মাঘ')->toContain('১');
});

it('shows Poush on January 13 noon', function () {
    expect(BanglaDate::instance(mktime(12, 0, 0, 1, 13, 2024), 6)->getBanglaDate('d m, y'))
        ->toContain('পৌষ');
});

// -------------------------------------------------------------------------
// Morning-hour rollback (the Bangla day does not start at midnight)
// -------------------------------------------------------------------------

it('still shows Chaitra at 3 AM on April 14 (before morning threshold)', function () {
    expect(BanglaDate::instance(mktime(3, 0, 0, 4, 14, 2024), 6)->getBanglaDate('d m, y'))
        ->toContain('চৈত্র');
});

it('shows Baisakh at 3 PM on April 14 (PM hours must not roll back)', function () {
    expect(BanglaDate::instance(mktime(15, 0, 0, 4, 14, 2024), 6)->getBanglaDate('d m, y'))
        ->toContain('বৈশাখ');
});

it('shows Baisakh at 1 PM on April 14 (regression: 12-hour format bug)', function () {
    expect(BanglaDate::instance(mktime(13, 0, 0, 4, 14, 2024), 6)->getBanglaDate('d m, y'))
        ->toContain('বৈশাখ');
});

it('shows Baisakh at exactly the morning threshold hour', function () {
    expect(BanglaDate::instance(mktime(6, 0, 0, 4, 14, 2024), 6)->getBanglaDate('d m, y'))
        ->toContain('বৈশাখ');
});

// -------------------------------------------------------------------------
// Leap year
// -------------------------------------------------------------------------

it('recognises 2024 as a leap year', function () {
    expect(BanglaDate::instance(mktime(12, 0, 0, 3, 15, 2024), 6)->isLeapYear())->toBeTrue();
});

it('recognises 2023 as a non-leap year', function () {
    expect(BanglaDate::instance(mktime(12, 0, 0, 3, 15, 2023), 6)->isLeapYear())->toBeFalse();
});

it('recognises 2000 as a leap year (divisible by 400)', function () {
    expect(BanglaDate::instance(mktime(12, 0, 0, 3, 15, 2000), 6)->isLeapYear())->toBeTrue();
});

it('recognises 1900 as a non-leap year (divisible by 100 but not 400)', function () {
    expect(BanglaDate::instance(mktime(12, 0, 0, 3, 15, 1900), 6)->isLeapYear())->toBeFalse();
});

it('gives Phalguna 31 days in a leap year', function () {
    // March 14 noon 2024: bangDateInt = 14-14=0, still-prev-month, days=30+1=31
    $result = BanglaDate::instance(mktime(12, 0, 0, 3, 14, 2024), 6)->getBanglaDate('d m, y');

    expect($result)->toContain('ফাল্গুন')->toContain('৩১');
});

it('gives Phalguna 30 days in a non-leap year', function () {
    $result = BanglaDate::instance(mktime(12, 0, 0, 3, 14, 2023), 6)->getBanglaDate('d m, y');

    expect($result)->toContain('ফাল্গুন')->toContain('৩০');
});

// -------------------------------------------------------------------------
// Bangla number conversion
// -------------------------------------------------------------------------

it('converts single digits to Bangla numerals', function () {
    $bd = BanglaDate::instance(mktime(12, 0, 0, 4, 14, 2024), 6);

    expect($bd->banglaNumber(0))->toBe('০')
        ->and($bd->banglaNumber(1))->toBe('১')
        ->and($bd->banglaNumber(9))->toBe('৯');
});

it('converts a multi-digit string to Bangla numerals', function () {
    $bd = BanglaDate::instance(mktime(12, 0, 0, 4, 14, 2024), 6);

    expect($bd->banglaNumber('1234567890'))->toBe('১২৩৪৫৬৭৮৯০')
        ->and($bd->banglaNumber('1431'))->toBe('১৪৩১');
});

// -------------------------------------------------------------------------
// English date in Bangla font
// -------------------------------------------------------------------------

it('returns the English date with Bangla month name and numerals', function () {
    $result = BanglaDate::instance(mktime(12, 0, 0, 4, 14, 2024), 6)->getEnglishDate('d m, y');

    expect($result)->toContain('এপ্রিল')->toContain('১৪')->toContain('২০২৪');
});

it('has a Bangla name for every English month abbreviation', function () {
    $bd = BanglaDate::instance(mktime(12, 0, 0, 4, 14, 2024), 6);

    $map = [
        'Jan' => 'জানুয়ারি', 'Feb' => 'ফেব্রুয়ারি', 'Mar' => 'মার্চ',
        'Apr' => 'এপ্রিল',   'May' => 'মে',           'Jun' => 'জুন',
        'Jul' => 'জুলাই',    'Aug' => 'আগস্ট',        'Sep' => 'সেপ্টেম্বর',
        'Oct' => 'অক্টোবর',  'Nov' => 'নভেম্বর',      'Dec' => 'ডিসেম্বর',
    ];

    foreach ($map as $abbr => $expected) {
        expect($bd->banglaFontMonth($abbr))->toBe($expected);
    }
});

// -------------------------------------------------------------------------
// Format string
// -------------------------------------------------------------------------

it('replaces d, m, y placeholders and keeps other characters', function () {
    $result = BanglaDate::instance(mktime(12, 0, 0, 4, 14, 2024), 6)->getBanglaDate('d-m-y');

    expect($result)
        ->toContain('-')
        ->not->toContain('d')
        ->not->toContain('y');
});

// -------------------------------------------------------------------------
// Backward-compatibility aliases
// -------------------------------------------------------------------------

it('resolves Instance() via PHP case-insensitive method lookup', function () {
    // @phpstan-ignore-next-line
    expect(BanglaDate::Instance(mktime(12, 0, 0, 4, 14, 2024)))->toBeInstanceOf(BanglaDate::class);
});

it('snake_case aliases return the same result as camelCase methods', function () {
    $bd = BanglaDate::instance(mktime(12, 0, 0, 4, 14, 2024), 6);

    expect($bd->get_bangla_date('d m, y'))->toBe($bd->getBanglaDate('d m, y'))
        ->and($bd->get_english_date('d m, y'))->toBe($bd->getEnglishDate('d m, y'))
        ->and($bd->bangla_number('123'))->toBe($bd->banglaNumber('123'))
        ->and($bd->bangla_font_month('Apr'))->toBe($bd->banglaFontMonth('Apr'))
        ->and($bd->is_leapyear())->toBe($bd->isLeapYear());
});

// -------------------------------------------------------------------------
// Year boundary (December / January)
// -------------------------------------------------------------------------

it('places December 2023 in Bangla year 1430', function () {
    expect(BanglaDate::instance(mktime(12, 0, 0, 12, 31, 2023), 6)->getBanglaDate('d m, y'))
        ->toContain('১৪৩০');
});

it('places January 2025 in Bangla year 1431', function () {
    expect(BanglaDate::instance(mktime(12, 0, 0, 1, 1, 2025), 6)->getBanglaDate('d m, y'))
        ->toContain('১৪৩১');
});

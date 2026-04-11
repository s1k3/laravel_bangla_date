<?php

it('returns a Bangla date string by default (language = bn)', function () {
    $result = bangla_date(mktime(12, 0, 0, 4, 14, 2024));

    expect($result)->toContain('বৈশাখ')->toContain('১৪৩১');
});

it('returns the English date in Bangla font when language is "en"', function () {
    $result = bangla_date(mktime(12, 0, 0, 4, 14, 2024), 'en');

    expect($result)->toContain('এপ্রিল')->toContain('২০২৪');
});

it('returns a Bangla date when language is explicitly set to "bn"', function () {
    expect(bangla_date(mktime(12, 0, 0, 4, 14, 2024), 'bn'))->toContain('বৈশাখ');
});

it('respects a custom format string', function () {
    $result = bangla_date(mktime(12, 0, 0, 4, 14, 2024), 'bn', 'd-m-y');

    expect($result)->toContain('-')->toContain('বৈশাখ');
});

it('uses the config format when none is provided', function () {
    // Default config format is 'd m, y'
    expect(bangla_date(mktime(12, 0, 0, 4, 14, 2024)))->toContain(', ');
});

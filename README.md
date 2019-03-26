#####Laravel Library for converting English Date to Bangla

For Installation follow the following steps:

1. **composer require s1k3/bangla-date**
2. **(You may skip this step when using Laravel >= 5.5))**. Navigate to the path/to/config/app.php and add  to the providers array 
 ``` php
 'providers' => [
        S1K3\Bangla\Date\BanglaDateServiceProvider::class
 ]
 ```


3. Execute the following command from the command-line to publish the package's config and the default template to start generating awesome code.
``` php
php artisan vendor:publish --provider="S1K3\Bangla\Date\BanglaDateServiceProvider"
```

Basic Usage:
``` php
bangla_date(time()) // ১২ চৈত্র ১৪২৫
```
``` php
bangla_date(time(),"en") // OUTPUT ২৬ মার্চ ২০১৯
```
``` php
bangla_date(time(),"bn") // ১২ চৈত্র ১৪২৫
```

Configuration: 

``` php
return [
    'hour' => 6,
    'format' => 'bn'
];
```

property | Description
------------ | -------------
format | 'en' for english format. 'bn' for bangla format
hour | If 0, then date will change instantly. If it's 6, date will change at 6'0 clock at the morning. Default is 6'0 clock at the morning
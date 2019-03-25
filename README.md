#####Laravel Library for converting english Date to Bangls

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
php artisan vendor:publish --provider="S1K3\Bangla\Date\BanglaDateServiceProvider" --tag=default
```

Basic Usage:
``` php
{{bangla_date(time())}}
```

Configuration: 

``` php
return [
    'hour' => 6
];
```

hour | Description
------------ | -------------
0 | If 0, then date will change instantly
6 | If it's 6, date will change at 6'0 clock at the morning. Default is 6'0 clock at the morning
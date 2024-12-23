# laravel-brhk

A Brhk Laravel Library Developed By Guolei

# Installation

```shell
composer require guolei19850528/laravel-brhk
```
# Example
```php
use Guolei19850528\Laravel\Brhk\Speaker;
$speaker = new Speaker(
            'id',
            'token',
            'version'
        );
$state = $speaker->notify('test message');
if ($state){
    print_r('success');
}else{
    print_r('failed')
}
```

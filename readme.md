# etude_sentinel

## Simple Sentinel demo on Laravel 5.2

1. register
2. activation
3. re-activation
4. login
5. logout
6. password-reset

## Install

```php:httpd.conf
<Directory /var/www/html/etude_sentinel/public>
    AllowOverride All
    Order allow,deny
    Allow from all
</Directory>
$ sudo systemctl restart http
```

```
$ cd /var/www/html
$ git clone git@github.com:Isotake/etude_sentinel.git
$ cd etude_sentinel
$ composer install
```

## References and Credits
* [Sentinel Manual :: Cartalyst](https://cartalyst.com/manual/sentinel/2.0)
* [tanaka's Programming Memo (lang:ja)](http://am1tanaka.hatenablog.com/entry/2016/06/29/003308)

## License

The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).

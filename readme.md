# etude_sentinel

## Simple Sentinel demo on Laravel 5.2

1. register
2. activation
3. re-activation
4. login
5. logout
6. password-reset

## Install

```ruby:httpd.conf
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

```
$ cp .env.example .env
$ php artisan key:generate
$ vim .env
APP_URL=http://(your domain)/etude_sentinel/public

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=(db name)
DB_USERNAME=(db user)
DB_PASSWORD=(db pass)

MAIL_DRIVER=smtp
MAIL_HOST=localhost
MAIL_PORT=25
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_NAME=‘mail_from_name’
MAIL_FROM_ADDRESS=from@mail.addr
MAIL_SENDMAIL='/usr/sbin/sendmail -bs'
MAIL_PRETEND=false
```

```
$ /usr/bin/mysql -u (db user) -p
mysql > CREATE DATABASE (db name) CHARACTER SET utf8 COLLATE utf8_general_ci;

$ php artisan vendor:publish --provider="Cartalyst\Sentinel\Laravel\SentinelServiceProvider"
$ php artisan migrate
```

## References and Credits
* [Sentinel Manual :: Cartalyst](https://cartalyst.com/manual/sentinel/2.0)
* [tanaka's Programming Memo (lang:ja)](http://am1tanaka.hatenablog.com/entry/2016/06/29/003308)

## License

The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).

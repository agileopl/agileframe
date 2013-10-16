agileframe install
==========

**Create rw dirs**

data/cache/cms/
data/cache/mds/
data/cache/purifier/
data/cache/scripts/
data/cache/web/
data/cache/web-pages/
data/logs/
data/lucene/
data/sessions/cms/
data/sessions/web/
data/tmp/
data/upload/
mds/public/f/
mds/public/p/

**Create database and set config**

_db/agileframe.sql

**Set APPLICATION_EXTERNAL_LIBS and download libraries**

You need:

* web/public/index.php

> realpath(APPLICATION_EXTERNAL_LIBS . DIRECTORY_SEPARATOR . 'ZendFramework' . DIRECTORY_SEPARATOR . '1.12.0'),
> realpath(APPLICATION_EXTERNAL_LIBS . DIRECTORY_SEPARATOR . 'PHPImageWorkshop' . DIRECTORY_SEPARATOR . '2.0'),
> realpath(APPLICATION_EXTERNAL_LIBS . DIRECTORY_SEPARATOR . 'HTMLPurifier' . DIRECTORY_SEPARATOR . '4.4.0'),

* cms/public/index.php

> realpath(APPLICATION_EXTERNAL_LIBS . DIRECTORY_SEPARATOR . 'ZendFramework' . DIRECTORY_SEPARATOR . '1.12.0'),
> realpath(APPLICATION_EXTERNAL_LIBS . DIRECTORY_SEPARATOR . 'HTMLPurifier' . DIRECTORY_SEPARATOR . '4.4.0'),
> realpath(APPLICATION_EXTERNAL_LIBS . DIRECTORY_SEPARATOR . 'PHPImageWorkshop' . DIRECTORY_SEPARATOR . '2.0'),

* mds/public/index.php

> realpath(APPLICATION_EXTERNAL_LIBS . DIRECTORY_SEPARATOR . 'ZendFramework' . DIRECTORY_SEPARATOR . '1.12.0'),
> realpath(APPLICATION_EXTERNAL_LIBS . DIRECTORY_SEPARATOR . 'PHPImageWorkshop' . DIRECTORY_SEPARATOR . '2.0'),


**Virtual hosts**

> <VirtualHost *:80>
>     DocumentRoot "/path/agileframe/web/public"
>     ServerName agileframe.local
> </VirtualHost>
> <VirtualHost *:80>
>     DocumentRoot "/path/agileframe/cms/public"
>     ServerName cms.agileframe.local
> </VirtualHost>
> <VirtualHost *:80>
>     DocumentRoot "/path/agileframe/mds/public"
>     ServerName mds.agileframe.local
> </VirtualHost>

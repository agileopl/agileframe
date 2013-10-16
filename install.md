agileframe install
==========

**Create rw dirs**

> data/cache/cms/
> 
> data/cache/mds/
> 
> data/cache/purifier/
> 
> data/cache/scripts/
> 
> data/cache/web/
> 
> data/cache/web-pages/
> 
> data/logs/
> 
> data/lucene/
> 
> data/sessions/cms/
> 
> data/sessions/web/
> 
> data/tmp/
> 
> data/upload/
> 
> mds/public/f/
> 
> mds/public/p/

**Create database and set config**

_db/agileframe.sql

**Set APPLICATION\_EXTERNAL\_LIBS and download libraries**

You need:

* web/public/index.php

> realpath(APPLICATION\_EXTERNAL\_LIBS . DIRECTORY\_SEPARATOR . 'ZendFramework' . DIRECTORY\_SEPARATOR . '1.12.0'),
> 
> realpath(APPLICATION\_EXTERNAL\_LIBS . DIRECTORY\_SEPARATOR . 'PHPImageWorkshop' . DIRECTORY\_SEPARATOR . '2.0'),
> 
> realpath(APPLICATION\_EXTERNAL\_LIBS . DIRECTORY\_SEPARATOR . 'HTMLPurifier' . DIRECTORY\_SEPARATOR . '4.4.0'),

* cms/public/index.php

> realpath(APPLICATION\_EXTERNAL_LIBS . DIRECTORY\_SEPARATOR . 'ZendFramework' . DIRECTORY\_SEPARATOR . '1.12.0'),
> 
> realpath(APPLICATION\_EXTERNAL_LIBS . DIRECTORY\_SEPARATOR . 'HTMLPurifier' . DIRECTORY\_SEPARATOR . '4.4.0'),
> 
> realpath(APPLICATION\_EXTERNAL_LIBS . DIRECTORY\_SEPARATOR . 'PHPImageWorkshop' . DIRECTORY\_SEPARATOR . '2.0'),

* mds/public/index.php

> realpath(APPLICATION\_EXTERNAL\_LIBS . DIRECTORY\_SEPARATOR . 'ZendFramework' . DIRECTORY\_SEPARATOR . '1.12.0'),
> 
> realpath(APPLICATION\_EXTERNAL\_LIBS . DIRECTORY\_SEPARATOR . 'PHPImageWorkshop' . DIRECTORY\_SEPARATOR . '2.0'),


**Virtual hosts**

>
>     DocumentRoot "/path/agileframe/web/public"
>     ServerName agileframe.local
>
>     DocumentRoot "/path/agileframe/cms/public"
>     ServerName cms.agileframe.local
>
>     DocumentRoot "/path/agileframe/mds/public"
>     ServerName mds.agileframe.local
>

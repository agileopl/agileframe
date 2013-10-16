
1. Create rw dirs 

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

2. Create database and set config

_db/agileframe.sql

3. Virtual hosts

<VirtualHost *:80>
    DocumentRoot "/path/agileframe/web/public"
    ServerName agileframe.local
</VirtualHost>
<VirtualHost *:80>
    DocumentRoot "/path/agileframe/cms/public"
    ServerName cms.agileframe.local
</VirtualHost>
<VirtualHost *:80>
    DocumentRoot "/path/agileframe/mds/public"
    ServerName mds.agileframe.local
</VirtualHost>

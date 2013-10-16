agileframe
==========

**Social, CMS, B2C skeleton based on ZF1**

![Agileframe CMS](http://agileo.pl/agileframe/agileframe-cmst.png)

*cms demo: admin/admin*

**Modules built in:**

* administration panel
* user profile
* facebook connect
* multimedia database
* mds (media delivery service) - it's possible to move to cloud
  - resize and crop images on demond
  - caching

**Model - high scalability and performance**

* mapping data for objects
* mysql master-stave architecture supported
* mysql relations use only for small tables inside some components 
  don't used constraint between components
* noSQL ideas are used to be faster and more flexible, 
  in some cases it is faster not to use joins. 
  In this situations you can treat table as bunch of 
  data and select the data by PK or by list PKs

        

> select * from articles join users on usr_id = art_usr_id ...*

         
In some cases better solusions is use php:
         
> 1. get users id's: select ..,art_usr_id from articles limit ...
> 
> 2. select * from users where id in ($usersIds)
> 
> 3. fill article obejcts with user objects.

        
* it's posible mysql sharding
* you shouldn't use mysql procedure, function, triggers 
  all logical should be in PHP in model

**Forms**

* zend_form is used with jquery jvalidator
* mistakes handling should be done in JS, in PHP only check for data validationand if sth is wrong, it should throw an exception.

**View**

* used Twitter bootstrap

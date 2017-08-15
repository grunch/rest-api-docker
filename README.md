Technical test
===================

Overview
--------
Very simple REST Api to handle users entities.

The api allow the user to:
- Get his information
- Delete his profile
- Change his profile data
- Upload an image

Environment
-----------
By the docker environments are binding ports to the host machine, if you are running services on ports like 80 and 3306,
you can change this in docker-compose.yml file.

Coding standards
----------------
This project is using the symfony coding standards, symfony follows the standards defined in the PSR-0, PSR-1, PSR-2 and PSR-4 [documents](http://symfony.com/doc/current/contributing/code/standards.html).

Environment Set Up
------------------
Run docker containers with this command

$ docker-compose up

In the code directory run this to create de database

$ mysql -h127.0.0.1 -uroot -p < sql/schema.sql # mysql password is "pass"

Get all users
-----------------------
GET => curl -i -X GET http://localhost/user

Get user with id 1
------------------
GET => curl -i -X GET http://localhost/user/1

Insert a new user
-----------------
curl -i -X POST -d 'name=Pedro&address=Av. Cordoba 123' http://localhost/user

Update user
-----------
curl -i -X PUT -d 'address=Av. Rivadavia 456' http://localhost/user/1

Delete user
-----------
curl -i -X DELETE http://localhost/user/1

Upload an image
---------------
curl -i -X POST http://localhost/userImage/1 -F "file=@imagefilename.jpg"

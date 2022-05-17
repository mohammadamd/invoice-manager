#Invoice Manager
___
It is a simple laravel application which handles invoice for you.

###Run Application
In order to start application in development purpose you have to run following command:
```shell
$ docker-compose up
```
By this you will have a database and a redis, and they will let you start your developments and test application.

After that you have to run migration to create your database tables by following command:
```shell
$ php artisan migrate
```
after that you will need to serve your application for this you need to run:
```shell
$ php artisan serve
```
Now everything is ready to test and develop your application.

###Run Tests
In order to run tests after run application (docker-compose is enough) you have to run: 
```shell
$ ./vendor/bin/phpunit
```
###Pipeline
This application already have a pipeline which running tests and creating docker image for the application 
in order to deploy it you need to push the image to your image repository and deploy it!  

###ToDo List
- [X] Add k6 loadtest
- [ ] Add k6 loadtest for other endpoints
- [ ] Add Nginx to docker-compose file
- [ ] Write tests for other endpoints
- [ ] Add a Swagger documentation for api
- [ ] Integrate with an APM service monitor like New Relic or Sentry
- [ ] ...


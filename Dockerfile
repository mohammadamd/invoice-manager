FROM php:8.1-fpm-alpine

COPY . /app

RUN mkdir /.config/

RUN chgrp -R 0 /app && chmod -R g=u /app
RUN chgrp -R 0 /.config && chmod -R g=u /.config

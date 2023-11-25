FROM ipti/yii2

RUN chown -R www-data:www-data /usr/local/bin/composer
RUN chmod 777 /usr/local/bin/composer
RUN chmod 777 /usr/local/bin/docker-run.sh

RUN composer global update
ADD composer.json /app
RUN composer dump-autoload
RUN composer install --no-scripts --no-interaction --no-autoloader --no-dev --prefer-dist
RUN composer update --no-scripts --no-interaction --prefer-dist
COPY . /app


RUN chown -R www-data:www-data /app/runtime/ \
&& chown -R www-data:www-data /app/web/assets


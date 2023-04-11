FROM php:7.4-cli
COPY . /usr/src/myapp
WORKDIR /public
CMD [ "php", "./index.php" ]
FROM php:7.4-cli
COPY . /usr/src/Checkout
WORKDIR /usr/src/Checkout
CMD [ "php", "./RegistrarTituloBradesco.php" ]

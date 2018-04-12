FROM phpdockerio/php72-fpm:latest
WORKDIR "/application"

# Install selected extensions and other stuff
RUN apt-get update \
    && apt-get -y --no-install-recommends install  php7.2-mysql php-yaml \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Install git
RUN apt-get update \
    && apt-get -y install git \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Install nodejs for @symfony/webpack-encore
RUN curl -L -o /tmp/nodejs.tar.gz https://nodejs.org/dist/v6.9.4/node-v6.9.4-linux-x64.tar.gz && \
    tar xfvz /tmp/nodejs.tar.gz -C /usr/local --strip-components=1 && \
    rm -f /tmp/nodejs.tar.gz && \
    npm install yarn -g

ENV COMPOSER_ALLOW_SUPERUSER 1
RUN echo 'alias sf="php bin/console"' >> ~/.bashrc

# confirm installation
RUN php -v
RUN composer -V
RUN node -v
RUN yarn -v
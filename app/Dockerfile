FROM php:8.3-cli

# Working location
WORKDIR /app

# Composer inside the container
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

#  copy the project files
COPY . /app

# Run Composer install (only if composer.json exists)
RUN if [ -f "composer.json" ]; then composer install; fi

# Default
CMD ["php", "index.php"]

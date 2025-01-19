# Open Data Visualization Tool

This project is a web-based visualization tool for exploring and analyzing open data sets. Follow these setup instructions to get started:

## Setup instructions - Non-dockerized

1. Install PHPBrew (PHP version manager):
   - Follow installation guide at: https://github.com/phpbrew/phpbrew
   - Install phpbrew with variants
     ```
     phpbrew install php-8.2.27 +default+sqlite+openssl # builds out php with some dependent variants
     phpbrew switch php-8.2.27 # sets php version
     ```
   - Install required PHP extensions:
     ```
     phpbrew ext install iconv
     phpbrew ext install ctype
     ```

3. Install Composer (PHP dependency manager):
   - Follow installation steps at: https://getcomposer.org/download/
   - Run `composer install` to install dependencies

4. Set up local development server:
   - Install Symfony CLI from: https://symfony.com/download
   - Build database schema: `symfony console doctrine:schema:update --force`
   - Start the server: `symfony server:start`

5. Access the application:
   - Open your browser and navigate to: http://localhost:8000/

## Setup instructions - Dockerized

1. Install Docker and Docker Compose
2. Run `docker compose up` to start the container
3. Access the application:
   - Open your browser and navigate to: http://localhost:8000/


## Running tests

- Build database schema: `symfony console doctrine:schema:update --force --env=test`
- Run tests: `php bin/phpunit`

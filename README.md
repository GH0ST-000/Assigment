# Assigment
Small application built with Laravel 11 <br>

## Installation
Make sure you have environment setup properly. You will need MySQL, PHP8.3, Composer and Postman.

### Install
1. Download the project (or clone using GIT)
2. Copy `.env.example` into `.env` and configure database credentials,Note we need to 2 database to implement all features,
    ```
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=Task
    DB_USERNAME=root
    DB_PASSWORD=
    
    DB_TESTING_CONNECTION=mysql
    DB_TESTING_HOST=127.0.0.1
    DB_TESTING_PORT=3306
    DB_TESTING_DATABASE=testing_database
    DB_TESTING_USERNAME=root
    DB_TESTING_PASSWORD=
   ```
First used to main database to store data and second used to test features. Make sure both of them is set correctly .env file
3. Navigate to the project's root directory using terminal
4. Run `composer install`
5. Set the encryption key by executing `php artisan key:generate`
6. Run migrations `php artisan migrate`
7. Run Seeder `php artisan db:seed `
8. Before run  `php artisan test ` it's optional to comment `test_poll_and_activate_expired_code` test method,cause here you need to wait 5 minutes before test ended

## POSTMAN
In the project files, you'll find a folder named POSTMAN, which contains Assignment.postman_collection.json. You can import this file into Postman for easy access to all API routes.
The project includes five routes:
Two are public, accessible without authentication.
The remaining three are protected, requiring authentication
### Public routes
```
POST: /login
POST: /register
```
### Protected routes

```
POST: /generate-tv-code
POST: /active-tv-code
POST: /poll-tv-code

```

### Description
1. Log in or Register – First, you need to create an account or sign in.
2. Generate a TV Code – Visit the `/generate-tv-code` endpoint to generate a new code.
3. Poll the TV Code – Use the `/poll-tv-code endpoint` to sync the mobile device and TV.
4. Activate the TV Code – Finally, call the `/activate-tv-code` endpoint to obtain the token.


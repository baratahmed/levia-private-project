# Levia

The "master" branch is maintained by Arif Reza and "Zisad" has his own branch named after him.

## Getting Started

Please follow along to have a version of this project up and running on your local machine / server.

### Prerequisites

What things you need to install the software and how to install them

```
 - PHP 7.1
 - MySQL
 - Composer
 - Nodejs (LTS preferred) [ DEV only ]
 - NPM / Yarn [ DEV only ]
```

### Installing

The master branch is maintained by me. Let's run this project from scratch.

Clone the project

```
git clone THIS_REPO
```

Install the dependencies

```
composer install
```

If 'key' is not automatically generated

```
php artisan key:generate
```

Configure the database on .env file (Rename .env.example to .env if .env is not generated automatically), then set up database credentials and execute this command:-

```
php artisan migrate
```

We need some initial data for cities, categories etc table. To populate the essential database tables automatically with test data:-

```
php artisan levia:config
```

To automatically generate some test data for all contents like users, restaurants, foods, ratings, reviews, just run this command

```
php artisan db:seed
```

Finally, since we are still in development period of this software, sometimes hard reset may be required in order to keep up with other team members. I've automated the task of resetting everything. Just run:-

```
php artisan levia:hardReset
```

To keep up with other members of the group, run these commands after every commit of other devs:-

```
git pull
php artisan migrate
```




## Running the tests

Tests are not available for now. Rapid development won't allow extra time for unit testing.



## Authors

* **Arif Reza** - *master branch* - [Masterboy](https://github.com/masterboy)
* **Sharif Noor Zisad** - *Zisad branch* - [SNZisad](https://github.com/snzisad)
* **Syed Mohammad Yasir** - *Administrator*
* **Rashedul Alam** - *Front-end*
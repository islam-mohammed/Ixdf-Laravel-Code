# Welcome to the IxDF Back-End Code!

## Feature Description

> In order to improve the gamification of our course platform, and to improve our users’ engagement in the platform, we would like to implement a "Course Leaderboard". We want to make learning just as addictive as playing a computer game and need your help.
>
> The leaderboard should display a list of users who have successfully completed a course and their corresponding position at a given course. Each course must have its own slug, which follows this pattern `/courses/{slug}`, e.g. `/courses/beginners-guide-to-user-experience`). That way, every course should have a unique leaderboard URL.
>
> Here is how the leaderboard section should look like:
>
> ![image](https://user-images.githubusercontent.com/5278175/63923279-f0ef8680-ca4e-11e9-8707-4d51154ce31e.png)


## Specifications of the leaderboard

1. Display up to 9 users on leaderboards for a given course.
2. Always display 3 users with the **highest** score of a given course (top tier).
3. Always display 3 users with the **lowest** score of a given course (bottom tier).
4. Always display currently logged-in user, surrounded by 2 other course participants (if any). When a logged-in user does not belong to the top or bottom tiers:
    1. Extend the top or bottom tier if it’s possible to create a continuous sequence of positions and not violate the rest of the specs.
    2. Create a middle tier and display logged-in user there (this is **the only** case when we need to display the middle tier).
5. Logged-in user *must be* in bold text (just like in the image).
6. Logged-in user *must have* higher precedence when compared to users with the same score.
7. Tiers *must be* separated by a line.
8. Display two leaderboards: worldwide and for the user country.

So, the goal of this project is to implement both back-end and front-end parts of this feature.

## How to setup a working environment

This project is a simple Laravel 8 application.


### A) Docker

This is a preferable way.

For the docker environment, we prepared a special `.env` file example: `.env.docker.example`.
In addition to that, we included a basic Docker Compose config.
So, if you are already a docker user, you simply need to execute the following commands:

```sh
# Copy the example .env file
cp .env.docker.example .env

# Build, (re)create and start containers for a service.
docker-compose up -d

# Install composer dependencies
docker-compose exec app composer install

# Run all migrations and seed the DB
docker-compose exec app php artisan migrate:fresh --seed

# Generate application key
docker-compose exec app php artisan key:generate

# Install front end dependencies
docker-compose exec front yarn install

# Build front end assets (CSS, JS)
docker-compose exec front yarn dev
```

If everything worked well, a project should be accessible by [http://localhost:8080](http://localhost:8080).

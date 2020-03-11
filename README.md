# CourseHunter Downloader

Download all episodes for a given course on [coursehunter.net](https://coursehunter.net/)

## Installation

- Clone this repo to your local machine.
- Install project dependencies with ``composer install`` command.

### Download premium courses (premium subscription required)
- Copy course slug from url.
- Start the app by this command:
```sh
php start.php -c course-slug -u your-email -p your-password
```

### Download free courses
- Copy course slug from url.
- Run the app 
``php start.php -c course-slug``

Assume that url is https://coursehunter.net/course/udemy-vuejs-2.

So, you must execute ``php start.php -c udemy-vuejs-2``.

Episodes save on **Downloads** folder.

> The app caches scrapped data for faster execution.
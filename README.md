# Stubby for Laravel

#### Recipes for the things Artisans Make

I'm allergic to repetition. I notice myself doing things over and over all day long and it actually causes me physical pain. So from time to time, I take a day to improve how I get things done. The workflow I created with this package has saved me countless hours of mindless typing and clicking.

Instead of creating, for instance, a controller, a blade template, and a feature test in 3 steps, I run a single command: `php artisan new controller MyController`. It creates the three files I need and opens them in PHPStorm for me. As an added bonus, it opens the routes/web.php file in PHPStorm so I can add my new route easily.
 
Stubby is a package that contains my customized stubs and recipes for creating new stuff fast. It's by no means a complete solution for every developer. It covers the stuff I create the most and does it in a way that works well for me. This very easy to customize package operates mostly by running multiple existing commands in sequence to do things in a fraction of the time it would normally take.

### Installation

```shell script
composer require grosv/stubby --dev
```

If you love my recipes and customized stubs, then you can skip to the "How To Use" section. Otherwise, you'll need this:

### Customization

The way to customize this is to fork it. Then you can add a repository to your composer.json file to load your own fork into your project like so:

```json
"repositories": [
  {
    "type": "git",
    "url": "https://github.com/{your_github_username}/stubby"
  }
]
```

Once you've done that, clone your forked repo and make it great for you. The customizations available to you are recipes (in src/StubbyCommand.php) and the stubs (in stubs/).


### How To Use

Assuming you haven't made any modifications to my recipes or stubs, start with publishing the stubs. Some of these overwrite stubs provided by Laravel and other popular packages. Some are my own creations. This command will forcefully overwrite any files that already exist in the stubs/ folder of your Laravel app (where stubs are placed when you run `php artisan stub:publish`) with the contents of the stubs/folder of this package.

```shell script
php artisan vendor:publish --provider="Grosv\Stubby\StubbyProvider" --tag="stubs"  --force
```

If you're like me, you want your files opened for you immediately upon creation. To that end, set a value in .env to correspond to the command your IDE uses to open a file. This is for my PHPStorm setup:

```dotenv
STUBBY_FILE_OPEN_COMMAND=pstorm
```

If you want to be super quick about it, just run `php artisan new ide {file_open_command}` (so `php artisan new ide pstorm` for PHPStorm or `php artisan new ide sublime` for Sublime Text) and I'll just append it to your .env file for you.


Then you can create stuff by running the recipes like this:

```shell script
php artisan new {thing} {name}
```

### Powering Up

I'm pro-alias. I created this alias to make my development time a true delight.  

```shell script
alias new="php artisan new "
```

Which means, for instance, that I can type `new model Order` and I get `database/migrations/xxxx_xx_xx_xxxxxx_create_orders_table.php`, `database/factories/OrderFactory.php`, and `app/Order.php` in a split second, all three automatically opened in my IDE.

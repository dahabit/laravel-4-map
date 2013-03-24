A Laravel 4 package which makes it easy to discover the concrete classes behind the abstract facades.

## The problem
Laravel 4 uses a lot of static interfaces, but keeps everything nice and testable by translating these into instance method calls behind the scenes.

This works very nicely, but can cause problems when you want to start digging through the Laravel codebase. Most of the time you find yourself looking at a facade class containing half a dozen lines of code, none of which are of any interest or use to you.

## The solution
This is where the `map` package comes in. It lets you use Laravel's `artisan` command line tool to easily locate the class behind the facade.

## Usage

### Default behaviour
By default, the package will output a "table" of all the facade-to-class mapping:

    $> php artisan map:facade

    ---------------------------------------------------------------
    | Facade      | Class                                         |
    ---------------------------------------------------------------
    | App         | Illuminate/Foundation/Application.php         |
    | Artisan     | Illuminate/Console/Application.php            |
    | Auth        | Illuminate/Auth/AuthManager.php               |
    | Blade       | Illuminate/View/Compilers/BladeCompiler.php   |
    | Etc.        | ...                                           |
    ---------------------------------------------------------------

### Investigate a single facade
If you just want to see the class for a single facade, include the facade name after the `map:facade` command, like this.

    $> php artisan map:facade Artisan

    Illuminate/Console/Application.php

### Retrieve the full path
By default, the package will display the path to the class file relative to the `vendor/laravel/framework/src/` directory.

If you'd like to see the full path, just use the `--path` flag.

    $> php artisan map:facade Artisan --path

    /path/to/your/site/vendor/laravel/framework/src/Illuminate/Console/Application.php

This works with a single facade, and also with the default "table" of all facades.

### Tips and tricks
The `--path` flag comes in very handy when you want to quickly open the class file in your chosen code editor.

    // Open the file in the default editor for this filetype.
    $> php artisan map:facade Artisan --path | xargs -I{} open {}

    // Open the file in MacVim
    $> php artisan map:facade Artisan --path | xargs -I{} mvim -p {}

    // Open the file in Sublime Text
    $> php artisan map:facade Artisan --path | xargs -I{} subl -n {}

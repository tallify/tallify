# Tallify

<p align="center"><img src="/art/tall.svg"></p>


### Introduction

---

Tallify is a small - *but very customisable* - helper that aims to quickly convert fresh new laravel application into fully capable **Tall Stack** apps.

Tallify comes shipped with default files that aims at getting you up-and-ready in no time so that you don't have to install and config all what's needed in order to get cracking developping rather than setting up bits and bobs. 

⚠️ Tallify works with [Laravel Valet](https://laravel.com/docs/9.x/valet) in order to allow `vite.js` to work over https. 

We know that everyone's bootstrap setup is different so we tried to make this as customisable as possible. If you don't like our base setup, feel free to tweak it as you like. Work on your perfect bootstrap setup once and for all and get cracking working your wonders ☺️

Here's a brief look at each command in this repo:

| Command                                          | Description                                                  |
| ------------------------------------------------ | ------------------------------------------------------------ |
| [install](#Installation)                         | Install the Tallify services                                 |
| [park](#The-park-command)                        | Tells tallify where all your Laravel applications lives to be able to "Tallify" them. |
| [parked](#The-parked-command)                    | Shows the directory where all your Laravel applivations live. |
| [package:add](#The-packageadd-command)           | Add custom packages to your tallify configuration file.      |
| [package:remove](#The-packageremove-command)     | Remove custom packages to your tallify configuration file.   |
| [package:list](#The-packagelist-command)         | List all packages you want to add to the default Laravel Application packages. |
| [detach:add](#The-detachadd-command)             | Add packages you want removed from the Laravel application packages. |
| [detach:remove](#The-detachremove-command)       | Remove packages you want removed from the Laravel application packages. |
| [detach:list](#The-detachlist-command)           | List all packages you want to add to the default Laravel Application packages. |
| [stub:add](#The-stubadd-command)                 | Add custom stubs to your tallify configuration file.         |
| [stub:remove](#The-stubremove-command)           | Remove custom stubs to your tallify configuration file.      |
| [stub:list](#The-stublist-command)               | List all stubs from your tallify configuration file.         |
| [command:add](#The-commandadd-command)           | Add custom artisan command to run post install to your tallify configuration file. |
| [command:remove](#The-commandremove-command)     | Remove custom artisan command to run post install to your tallify configuration file. |
| [command:list](#The-commandlist-command)         | List all post install/update artisan commands from the Tallify configuration file. |
| [env:add](#The-envadd-command)                   | Add custom environment variable to add to your `.env` file.  |
| [env:remove](#The-envremove-command)             | remove previously added custom environment variable to be added to your `.env` file. |
| [env:list](#The-envlist-command)                 | List all environment variables you wish to add to you `.env` file. |
| [gitignore:add](#The-gitignoreadd-command)       | Add custom files to the `.gitignore` file.                   |
| [gitignore:remove](#The-gitignoreremove-command) | Remove custom files previously added from the `.gitignore` file. |
| [gitignore:list](#The-gitignorelist-command)     | List all custom files you want to add to the `.gitignore` file. |
| [config:reset](#The-configreset-command)         | Reset the tallify configuration file to its default state.   |
| [publish](#The-publish-command)                  | Publish the tallify configuration and files for personal customization. |
| [published](#The-published-command)              | Shows the directory where the config file and stubs are.     |
| [unpublish](#The-unpublish-command)              | Unpublish the tallify configuration and files for personal customization. |
| [build](#The-build-command)                      | Tallify a given Laravel application.                         |




### Installation

---

> **Requires [PHP 8.0+](https://php.net/releases/)**
> **Requires [Laravel 9.0+](https://laravel.com/docs/9.x)**
> **Requires [Laravel Valet](https://laravel.com/docs/9.x/valet)**

Pull in Tallify using [Composer](https://getcomposer.org):

```bash
composer global require tallify/tallify
```

After adding Tallify to your global composer packages, execute Tallify's install command. This will install Tallify stubs and configuration default files:

```bash
tallify install
```

⚠️ Tallify is now installed but it can't work it's magic yet! In order for Tallify to add it's default `Tall Stack` flavours to any fresh `Laravel` application, you will need to tell Tallify where you keep all your Laravel applications.

Out of the box, Tallify includes, but is not limited to:

| **Npm packages**                                             | Composer developpement packages                              | Composer packages                                         |
| ------------------------------------------------------------ | ------------------------------------------------------------ | --------------------------------------------------------- |
| [tailwindcss/aspect-ratio](https://github.com/tailwindlabs/tailwindcss-aspect-ratio) | [pestphp/pest-plugin-parallel](https://github.com/pestphp/pest-plugin-parallel) | [livewire/livewire](https://github.com/livewire/livewire) |
| [tailwindcss/typography](https://github.com/tailwindlabs/tailwindcss-typography) | [pestphp/pest-plugin-livewire](https://github.com/pestphp/pest-plugin-livewire) |                                                           |
| [tailwindcss/line-clamp](https://github.com/tailwindlabs/tailwindcss-line-clamp) | [pestphp/pest-plugin-laravel](https://github.com/pestphp/pest-plugin-laravel) |                                                           |
| [tailwindcss/forms](https://github.com/tailwindlabs/tailwindcss-forms) | [barryvdh/laravel-ide-helper](https://github.com/barryvdh/laravel-ide-helper) |                                                           |
| [tailwindcss](https://github.com/tailwindlabs/tailwindcss)   | [barryvdh/laravel-debugbar](https://github.com/barryvdh/laravel-debugbar) |                                                           |
| [alpinejs/collapse](https://alpinejs.dev/plugins/collapse)   | [nunomaduro/larastan](https://github.com/nunomaduro/larastan) |                                                           |
| [alpinejs/intersect](https://alpinejs.dev/plugins/intersect) | [pestphp/pest](https://github.com/pestphp/pest)              |                                                           |
| [alpinejs/persist](https://alpinejs.dev/plugins/persist)     | [laravel/pint](https://github.com/laravel/pint)              |                                                           |
| [alpinejs/morph](https://alpinejs.dev/plugins/morph)         |                                                              |                                                           |
| [alpinejs/focus](https://alpinejs.dev/plugins/focus)         |                                                              |                                                           |
| [alpinejs/mask](https://alpinejs.dev/plugins/mask)           |                                                              |                                                           |
| [alpinejs/ui](https://github.com/alpinejs/alpine/tree/main/packages/ui) |                                                              |                                                           |
| [alpinejs](https://github.com/alpinejs/alpine)               |                                                              |                                                           |
| [vite-livewire-plugin](https://github.com/defstudio/vite-livewire-plugin) |                                                              |                                                           |
| [autoprefixer](https://github.com/postcss/autoprefixer)      |                                                              |                                                           |
| [postcss](https://github.com/postcss/postcss)                |                                                              |                                                           |

This is an exaustive list of add-ons we feel you might be using in your Tall Stack project but some of them you might not want. Go to the [Configuration](#Configuration) section to understand more about adding or removing some of those default packages

---

#### The `park` command

The `park` command registers a directory on your machine that contains your Laravel applications. Once the directory has been "parked" with Valet, all of the directories within that directory will be accessible for Tallify to `tallify` your applications. 

```
cd ~/Code/Laravel

tallify park
```

🔥 And voila! Now you can `tallify` any of the fresh Laravel application you want to. Tallify will add its default files and setup in order for you to get cracking as fast as possible.

---

#### The `parked` command

If you are unsure to wether you did use the `tallify park` command, if you struggle remembering where you actually 'parked' Tallify, or if you know you 'parked' Tallify in the wrong place, use the `parked` command to see what is stored withing the Tallify configuration file.

```bash
tallify parked
```

This will output the path to the directory on your machine that should contains your Laravel applications.

🔥 **After you successfully parked your Laravel applications directory, Tallify will be completely installed and ready to be used to 'tallify' any new Laravel application.**



### Configuration

---

For small tweaks or customisation of your default Tallify setup, we profived helpers command so you can easily change the default config file to suits your need. For a more substantial customisation, go to the [Customisation](#Customisation) section.

#### The `package:add` command

Some of you might want to add specific packages to the default configuration so that every new 'tallified' installation comes ship with it. To do so, simply add and **existing** package to the default configuration file by using the `package:add` command. This command has optional arguments `--composer`, `--npm`, and `--dev` to tell Tallify what library you might be using. Please ensure you tell Tallify wether it is an `npm` or a `composer` package by adding the optional commands available: 

Type `--composer` to add composer packages

Type `--composer` `--dev` to add composer **development** packages

Type `--npm` to add npm packages.

```bash
// Adding a composer package
tallify package:add laravel-lang/lang --composer

// Adding a development composer package
tallify package:add laravel/breeze --composer --dev

// Adding a npm package
tallify package:add moment --npm
```

This will add any given packages (provided it exists) to your default Tallify configuration file. If you don't know or don't remember the list of packages you potentially added in the past, checkout the [`package:list`](#The-packagelist-command) command.

---

#### The `package:remove` command

Exactly like you can add packages to your default Tallify configuration, you can remove packages. If you don't need one of the default packages or if you added a package in the past that is no longer need, use the `package:remove` command to take it off the default Tallify configuration file. It works exactly like the [`package:add`](#The-packageadd-command) command (including its arguments).

Type `--composer` to remove composer packages

Type `--composer` `--dev` to remove composer **development** packages

Type `--npm` to remove npm packages.

```bash
// Removing a composer package
tallify package:remove laravel-lang/lang --composer

// Removing a development composer package
tallify package:remove laravel/breeze --composer --dev

// Removing a npm package
tallify package:remove moment --npm
```

This will remove any packages from your default Tallify configuration file. If you don't know or don't remember the list of packages you potentially added in the past, checkout the [`package:list`](#The-packagelist-command) command.

---

#### The `package:list` command

In order to check what are the Tallify default packages or the packages you might have added in the past, feel free to use the `package:list` command. This command has optional arguments `--composer`, `--npm`, and `--dev` to tell Tallify what package list you'd like to see. This will output an array of packages included in your Tallify configuration file.

Type `--composer` to see default composer packages

Type `--composer` `--dev` to see default composer **development** packages

Type `--npm` to see default npm packages.

```
// Outputs the default composer packages
tallify package:list --composer

// Outputs the default development composer packages
tallify package:list --composer --dev

// Outputs the default npm packages
tallify package:list --npm
```

---

#### The `detach:add` command

If you do not need some of the Laravel application default `composer` or `npm` packages, Tallify allows you to detach them from the fresh Laravel application. To do so, you can use the `detach:add` command. This command has optional arguments `--composer`, `--npm`, and `--dev` to tell Tallify what library you might be using. 

Type `--composer` to add composer packages to be removed from the default Laravel composer packages

Type `--composer` `--dev` to add composer **development** packages to be removed from the default Laravel composer development packages

Type `--npm` to add npm packages to be removed from the default Laravel npm packages

```bash
// Remove axios from the Laravel default npm packages
tallify detach:add axios --npm

// Remove laravel/sanctum from the Laravel default composer packages
tallify detach:add laravel/sanctum --composer

// Remove laravel/sail from the Laravel default composer development packages
tallify detach:add laravel/sail --composer --dev
```

---

#### The `detach:remove` command

If you change your mind about a default packages you DO NOT want to remove from the default Laravel application packages, you can remove them from the list of packages you added in the past by using the `detach:remove` command. It works exactly like the [`detach:add`](#The-detachadd-command) command (including its arguments).

Type `--composer` to keep composer packages from the default Laravel composer packages you once wanted out

Type `--composer` `--dev` to keep composer **development** packages from the default Laravel development composer packages you once wanted out

Type `--npm` to keep npm packages from the default Laravel composer packages you once wanted out

```bash
// Keep axios from the Laravel default npm packages when you previously wanted it out
tallify detach:remove axios --npm

// Keep laravel/sanctum from the Laravel default composer packages when you previously wanted it out
tallify detach:remove laravel/sanctum --composer

// Remove laravel/sail from the Laravel default composer development packages when you previously wanted it out
tallify detach:remove laravel/sail --composer --dev
```

---

#### The `detach:list` command

In order to check what are the Tallify packages you want to remove from the default Laravel packages, feel free to use the `detach:list` command. This command has optional arguments `--composer`, `--npm`, and `--dev` to tell Tallify what package list you'd like to see. This will output an array of packages included in your Tallify configuration file.

Type `--composer` to see default composer packages you want out

Type `--composer` `--dev` to see default composer **development** packages you want out

Type `--npm` to see default npm packages you want out

```bash
// Outputs the default Laravel composer packages you want out
tallify detach:list --composer

// Outputs the default Laravel development composer packages you want out
tallify detach:list --composer --dev

// Outputs the default Laravel npm packages you want out
tallify detach:list --npm
```

---

#### The `stub:add` command

Like with the [`package:add`](#The-packageadd-command), you might want to provide new default stubs so that they can get copied to your fresh Laravel application. The `stub:add` command will add a "stub-name": "stub-path" to the default "stubs" array within your default Tallify configuration file. The `stub:add` takes two arguments: `stub-name` and `stub-path` and an additional optional argument `--directory` argument to tell Tallify that the stub to copy is **NOT** a file but a **directory**. 

```bash
// Add stub to default configuration file
// tallify stub:add stub-name stub-path
tallify stub:add webpack.mix.js /

// Add a stub director to the default configuration file
tallify stub:add icons resources/views/components --directory
```

The above code tell Tallify that, (first example) *as part of the default tallifying process*, you want to copy the `webpack.mix.js` stub file to the `root` folder of our fresh Laravel application and, (second example) the `icons` folder and what is inside needs to be copied to the `resources/views/components` folder

⚠️ **If you don't actually create a stub with the specified `stub-name` in the stubs config folder, the tallify process will end up having an error.**

---

#### The `stub:remove` command

Exactly like you can add stubs to your default Tallify configuration, you can remove stubs. If you don't need one of the default stubs or if you added a stub in the past that is no longer need, use the `stub:remove` command to take it off the default Tallify configuration file. 

⚠️ The `stub:command` **differs** from the [`stub:add`](#The-stubadd-command) command as it only takes **one argument** `stub-name` and an optional argument `--directory` to let Tallify know what kind of stub you want to remove from the stubs to be copied.

```bash
// Remove stub to default configuration file
tallify stub:remove webpack.mix.js

// Remove a stub director to the default configuration file
tallify stub:remove icons resources/views/components --directory
```

---

#### The `stub:list` command

In order to check what are the Tallify default stubs or the custom stubs you might have added in the past, feel free to use the `stub:list` command. This will output an array of stubs included in your Tallify configuration file. You can add the optional `--directory` argument to let Tallify know you want to display all the stub folders to be copied during the 'tallification' of your fresh Laravel application.

```bash
// Outputs all your default stub files
tallify stub:list

// Outputs all your default stub folders
tallify stub:list --directory
```

---

#### The `command:add` command

Depending on some packages you want to add to your default Tallify configuration file, some might require post installation or post update commands to be run. For this Tallify allows you to add commands to be run after the package is installed. This command has an optional argument `--post-update` to tell Tallify if the command needs to be run once after the install or (with the `--post-update` option) if the command has to be run after all composer updates.

⚠️ For commands that contains spaces, ensure you put the whole command between quotation marks (see the example below).

Type `--post-update` to add an artisan command to the `post-update-cmd` object whitin your `composer.json` file.

```bash
// Installing Laravel vapor for your Laravel application
tallify command:add vapor-ui:install

// Adding Laravel Vapor post update command
tallify command:add '@php artisan vapor-ui:publish --ansi' --post-update
```

---

#### The `command:remove` command

If your default setup changes over time and you are not installing packages that require post install/update artisan command to be run, you can easily use the `command:remove` to take them out of your post install/update artisan command list to be run after packages installation. It works exactly like the [`command:add`](#The-commandadd-command) command (including its argument).

⚠️ Again, for commands that contains spaces, ensure you put the whole command between quotation marks (see the example below).

Type `--post-update` to remove a previously added artisan command to the `post-update-cmd` object whitin your `composer.json` file.

```bash
// Remove the post install Laravel vapor command
tallify command:remove vapor-ui:install

// Remove Laravel Vapor post update command
tallify command:remove '@php artisan vapor-ui:publish --ansi' --post-update
```

---

#### The `command:list` command

In order to check what are the post install/update artisan commands you might have added in the past, feel free to use the `command:list` command. This will output an array of artisan commands included in your Tallify configuration file.

Type `--post-update` to see all post-update commands.

```bash
// Outputs all your post install artisan commands
tallify command:list

// Outputs all your post update artisan commands
tallify command:list --post-update
```

---

#### The `env:add` command

Sometimes you want specific **Environment Variables** to be automatically added to the `.env` file. For this you can tell Tallify what environment variables you'd like to automatically add to your Laravel Project by using the `env:add` command.

⚠️ For variables that contains spaces, ensure you put the whole variable between quotation marks (see the example below).

```bash
// Add a vite.js environment variable
tallify env:add 'VITE_BROWSER="google-chrome"'
```

---

#### The `env:remove` command

If your default setup changes over time and you **do not** need specific environment variables to be added to your `.env` file, you can easily use the `env:remove` to take them out of list of variables your previously needed to be added. 

⚠️ Again, for variables that contains spaces, ensure you put the whole variable between quotation marks (see the example below).

```bash
// Remove a vite.js environment variable
tallify env:remove VITE_LIVEWIRE_OPT_IN=true
```

---

#### The `env:list` command

In order to check what are the post install/update artisan commands you might have added in the past, feel free to use the `command:list` command. This will output an array of artisan commands included in your Tallify configuration file.

```bash
// Outputs all the environment variables you want to add to the .env file
tallify env:list
```

---

#### The `gitignore:add` command

If you add development files or secret files you need **NOT** to go to github and you need to automatically add them to your `.gitignore` file, you can use the `gitignore:add` command.

```bash
// Add .env.staging to .gitignore file
tallify gitignore:add .env.staging
```

---

#### The `gitignore:add` command

If your default setup changes over time and you do not need specific filesto be added to your `.gitignore` file, you can easily use the `gitignore:remove` to take them out of list of files your previously needed to be added. 

```bash
// Remove _ide_helper from .gitignore file
tallify gitignore:remove _ide_helper
```

---

#### The `gitignore:add` command

In order to check what are thefiles you might have added in the past, feel free to use the `gitignore:list` command. This will output an array of files included in your Tallify configuration file.

```bash
// Outputs all the files you want to add to the .gitignore file
tallify gitignore:list
```

---

#### The `config:reset` command

If things go south, or if you simply want to start from fresh, you can use the `config:reset` command. This will erase your current Tallify configuration file and recreate a fresh default Tallify configuration file. Careful as this commands will delete any modifications you previously made to the default configuration file.

```bash
tallify config:reset
```



### Tallifying

---

You have everything setup to be able to 'tallify' a fresh Laravel application. Let's 'tallify'.

#### The `build` command

```bash
tallify build my-project
```



### Customisation

---

For a more substantial customisation of the default Tallify configuration file, we strongly recommend publishing the configuration file and assets. This will allow you to fully customise the Tallify setup so that it does exactly what you want it to do. 

Tallify comes shipped with default stub files that is uses to modify a fresh Laravel project. If you want to have more modifications from the start, and you want to create many stub files, it is easier to do so by publishing the Tallify default configuration file and stubs and add your own.

#### The `publish` command

Publishing the default files to a path on your computer of your choosing, will copy the default files to this newly created `path/you/want/` `tallify` directory. Simply open this directory with the code editor of your choice and start tweaking everything you want.

```
tallify publish /path/for/custom/config

// creates a tallify directory in /path/for/custom/config
```

Now if you want to start customising, open the newly created tallify directory at the path your provided.

```bash
// Example using Visual Code Editor
code /path/for/custom/config/tallify
```

This is the project tree you should be seing within your code editor.

```
tallify
   |--- stubs
   config.json
```

You can now add or remove stub files as you please. 

⚠️ **Bear in mind that if you add your own stub files, you need to tell Tallify about them!**

To do so, you can either use the [`stub:add`](#The-stubadd-command) command **or** add the manually from your code editor within the `stubs` object in the `config.json` file. The `key` is the name of the stub file and the `value` represents the path to where in your Laravel application you want to add it.

---

#### The `published` command

If you are unsure to wether you did use the `publish` command, if you struggle remembering where you actually 'published' Tallify, or if you know you 'published' Tallify in the wrong place, use the `published` command to see where your custom Tallify configuration file is.

```bash
// Outputs where the customed Tallify files are located on your computer
tallify published
```

---

#### The `unpublish` command

If you want to go back to the original config or if you just want to start customising from fresh, you can use the `unpublish` command. This will delete your previous customed Tallify configuration and files.

```bash
tallify unpublish
```





### <u>Here is an example of customisation:</u>

Let say we want to use `laravel-mix`. The first step is to publish the Tallify files to a specific path on our machine.

```bash
tallify publish ~/Code/Config

// creates a tallify directory at ~/Code/Config
```

Then we should add the `laravel-mix` package to the list of `npm` packages using the [`package:add`](#The-packageadd-command) command.

```bash
tallify package:add laravel-mix --npm
```

Now we want to provide our own `laravel-mix` stub file so that Tallify can use it as default when 'tallifying' a fresh Laravel application. Let's create a new `webpack.mix.js` file in the `tallify/stubs` directory.

```bash
touch ~/Code/Config/tallify/stubs/webpack.mix.js
```

We edit the `webpack.mix.js` to our own taste until it is ready. Once ready, we now have to add it to the Tallify `stubs` array.

Using the [`stub:add`](#The-stubadd-command) command:

```
tallify stub:add webpack.mix.js /
```

Using a code editor of your choice to edit the `config.json` file

```json
{
  // [... Omitted code]
  "stubs": {
        // [... Omitted code],
    		"name/of/the/stub": "path/in/laravel/project"
    		"webpack.mix.js": "/"
    },
}
```

🔥 That is it. Now when we use the Tallify [`build`](#The-build-command) command. Laravel mix will be installed as a `npm` dependency and our `webpack.mix.js` stub will be copied to its defined location *(Laravel project root folder in this case)*.



### Tallify directory & files

---

Here are where the Tallify default files are situated in your computer.

`~/.config/tallify`

Contains all of Tallify's configuration. If things go south, you can always use the [`config:reset`](#The-configreset-command) command to restore it to its default state. 

`/.config/tallify/stubs`

Contains all of Tallify's default stubs. You may wish to maintain a backup of this directory.



### Uninstallation

---

If anything went wrong or if you want to uninstall the Tallify services, simply use the `uninstall` command. This will remove all of Tallify configuration files and directory from your system, from default configuration files and assests to customised files and assets.

```bash
tallify uninstall
```

To complete uninstallation and remove Tallify from your global `composer` dependencies, use the `global remove` composer command.

```
composer global remove tallify/tallify
```

That's it. 

<p align="center"><img src="/art/tall.svg"></p>



#### Introduction

---

Tallify is a small - *but very customisable* - helper that aims to quickly convert fresh new laravel application into fully capable **Tall Stack** apps.

Tallify comes shipped with default files that aims at getting you up-and-ready in no time so that you don't have to install and config all what's needed in order to get cracking developping rather than setting up bits and bobs. 

We know that everyone's bootstrap setup is different so we tried to make this as customisable as possible. If you don't like our base setup, feel free to tweak it as you like. Work on your perfect bootstrap setup once and for all and get cracking working your wonders ☺️

Here's a brief look at each command in this repo:

| Command                                         | Description                                                  |
| ----------------------------------------------- | ------------------------------------------------------------ |
| [install](#Installation)                        | Install the Tallify services                                 |
| [park](#The `park` command)                     | Tells tallify where all your Laravel applications lives to be able to "Tallify" them. |
| [parked](#The `parked` command)                 | Shows the directory where all your Laravel applivations live. |
| [package:add](#The `package:add` command)       | Add custom packages to your tallify configuration file.      |
| [package:remove](#The `package:remove` command) | Remove custom packages to your tallify configuration file.   |
| [package:list](#The `package:list` command)     | List all packages.                                           |
| [stub:add](#The `stub:add` command)             | Add custom stubs to your tallify configuration file.         |
| [stub:remove](#The `stub:remove` command)       | Remove custom stubs to your tallify configuration file.      |
| [stub:list](#The `stub` command)                | List all stubs.                                              |
| [config:reset](#The `config:reset` command)     | Reset the tallify configuration file to its default state.   |
| [config:publish](#The `config:publish` command) | Publish the tallify configuration and files for personal customization. |
| [build](#The `build` command)                   | Tallify a given Laravel application.                         |




#### Installation

---

> **Requires [PHP 8.0+](https://php.net/releases/)**

Require Tallify using [Composer](https://getcomposer.org):

```bash
composer global require tallify/tallify
```

After adding Tallify to your global composer packages, execute Tallify's install command. This will install Tallify stubs and configuration default files:

```bash
tallify install
```

Tallify is now installed but it can't work it's magic yet! In order for Tallify to add it's default `Tall Stack` flavours to any fresh `Laravel` application, you will need to tell Tallify where you keep all your Laravel applications.

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

##### The park command

---

The `park` command registers a directory on your machine that contains your Laravel applications. Once the directory has been "parked" with Valet, all of the directories within that directory will be accessible for Tallify to `tallify` your applications. 

```
cd ~/Code/Laravel

tallify park
```

And voila! Now you can `tallify` any of the fresh Laravel application you want to. Tallify will add its default files and setup in order for you to get cracking as fast as possible.

##### The parked command

---

If you are unsure to wether you did use the `tallify park` command, if you struggle remembering where you actually 'parked' Tallify, or if you know you 'parked' Tallify in the wrong place, use the `parked` command to see what is stored withing the Tallify configuration file.

```bash
tallify parked
```

This will output the path to the directory on your machine that should contains your Laravel applications.

**After you successfully parked your Laravel applications directory, Tallify will be completely installed and ready to be used to 'tallify' any new Laravel application.**



#### Configuration

---

For small tweaks or customisation of your default Tallify setup, we profived helpers command so you can easily change the default config file to suits your need. For a more substantial customisation, go to the [Customisation](#Customisation) section.

##### The package:add command

Some of you might want to add specific packages to the default configuration so that every new 'tallified' installation comes ship with it. To do so, simply add and **existing** package to the default configuration file by using the `package:add` command. This command has optional arguments `--composer`, `--npm`, and `--dev` to tell Tallify what of package you might be adding. Please ensure you tell Tallify wether it is an `npm` or a `composer` package by adding the optional commands available: 

Type `--composer` to add composer packages

Type `--composer` `--dev` to add composer <u>development</u> packages

Type `--npm` to add npm packages.

```bash
// Adding a composer package
tallify package:add laravel-lang/lang --composer

// Adding a development composer package
tallify package:add laravel/breeze --composer --dev

// Adding a npm package
tallify package:add moment --npm
```

This will add any given packages (provided it exists) to your default Tallify configuration file. If you don't know or don't remember the list of packages you potentially added in the past, checkout the [`package:list`](#The package:list command) command.

##### The package:remove command

Exactly like you can add packages to your default Tallify configuration, you can remove packages. If you don't need one of the default packages or if you added a package in the past that is no longer need, use the `package:remove` command to take it off the default Tallify configuration file. It works exactly like the [`package:add`](#The package:add command) command (including its arguments).

Type `--composer` to remove composer packages

Type `--composer` `--dev` to remove composer <u>development</u> packages

Type `--npm` to remove npm packages.

```bash
// Removing a composer package
tallify package:remove laravel-lang/lang --composer

// Removing a development composer package
tallify package:remove laravel/breeze --composer --dev

// Removing a npm package
tallify package:remove moment --npm
```

This will remove any packages from your default Tallify configuration file. If you don't know or don't remember the list of packages you potentially added in the past, checkout the [`package:list`](#The package:list command) command.

##### The package:list command

In order to check what are the Tallify default packages or the packages you might have added in the past, feel free to use the `package:list` command. This command has optional arguments `--composer`, `--npm`, and `--dev` to tell Tallify what package list you'd like to see. This will output an array of packages included in your Tallify configuration file.

Type `--composer` to see default composer packages

Type `--composer` `--dev` to see default composer <u>development</u> packages

Type `--npm` to see default npm packages.

```
// Outputs the default composer packages
tallify package:list --composer

// Outputs the default development composer packages
tallify package:list --composer --dev

// Outputs the default npm packages
tallify package:list --npm
```

##### The stub:add command

##### The stub:remove command

##### The stub:list command

##### The config:reset command

If things go south, or if you simply want to start from fresh, you can use the `config:reset` command. This will erase your current Tallify configuration file and recreate a fresh default Tallify configuration file. Careful as this commands will delete any modifications you previously made to the default configuration file.

```bash
tallify config:reset
```



#### Tallifying

---

You have everything setup to be able to 'tallify' a fresh Laravel application. Let's 'tallify'.

##### The build command

```bash
tallify build my-project
```



#### Customisation

---

For a more substantial customisation of the default Tallify configuration file, we strongly recommend publishing the configuration file and assets. This will allow you to fully customise the Tallify setup so that it does exactly what you want it to do. 

Tallify comes shipped with default stub files that is uses to modify a fresh Laravel project. If you want to have more modifications from the start, and you want to create many stub files, it is easier to do so by publishing the Tallify default configuration file and stubs and add your own.

#### The config:publish command

Publishing the default files to a path on your computer of your choosing, will copy the default files to this newly created `path/you/want/` `tallify` directory. Simply open this directory with the code editor of your choice and start tweaking everything you want.

```
tallify config:publish /path/for/custom/config

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

**Bear in mind that if you add your own stub files, you need to tell Tallify about them!**

To do so, you can either use the [`stub:add`](#The stub:add command) command <u>or</u> add the manually from your code editor within the `stubs` object in the `config.json` file. The `key` is the name of the stub file and the `value` represents the path to where in your Laravel application you want to add it.

##### <u>Here is an example:</u>

Let say we want to use `laravel-mix`. The first step is to publish the Tallify files to a specific path on our machine.

```bash
tallify config:publish ~/Code/Config

// creates a tallify directory at ~/Code/Config
```

Then we should add the `laravel-mix` package to the list of `npm` packages using the [`package:add`](#The package:add command) command.

```bash
tallify package:add laravel-mix --npm
```

Now we want to provide our own `laravel-mix` stub file so that Tallify can use it as default when 'tallifying' a fresh Laravel application. Let's create a new `webpack.mix.js` file in the `tallify/stubs` directory.

```bash
touch ~/Code/Config/tallify/stubs/webpack.mix.js
```

We edit the `webpack.mix.js` to our own taste until it is ready. Once ready, we now have to add it to the Tallify `stubs` array.

Using the [`stub:add`](#The stub:add command) command:

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

That is it. Now when we use the Tallify [`build`](#The build command) command. Laravel mix will be installed as a `npm` dependency and our `webpack.mix.js` stub will be copied to its defined location *(Laravel project root folder in this case)*.



#### Uninstallation

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

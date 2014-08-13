# Grav Error Plugin

`error` is a [Grav](http://github.com/getgrav/grav) Plugin and allows to redirect errors to nice output pages.

This plugin is required and you'll find it in any package distributed that contains Grav. If you decide to clone Grav from GitHub you will most likely want to install this.


# Installation

To install this plugin, just download the zip version of this repository and unzip it under `/your/site/grav/user/plugins`. Then, rename the folder to `error`.

You should now have all the plugin files under

	/your/site/grav/user/plugins/error

>> NOTE: This plugin is a modular component for Grav which requires [Grav](http://github.com/getgrav/grav), the [Error](https://github.com/getgrav/grav-plugin-error) and [Problems](https://github.com/getgrav/grav-plugin-problems) plugins, and a theme to be installed in order to operate.

# Usage

The `error` plugin doesn't require any configuration. The moment you install it, it is ready to use.

Something you might want to do is to override the look and feel of the error page, and with Grav it is super easy.

### Template

Copy the template file [error.html.twig](templates/error.html.twig) into the `templates` folder of your custom theme and that is it. 

```
/your/site/grav/user/themes/custom-theme/templates/error.html.twig
```

You can now edit the override and tweak it however you prefer.

### Page

Copy the page file [error.md](pages/error.error) into the `pages` folder of your user directory and that is it. 

```
/your/site/grav/user/pages/error/error.md
```

You can now edit the override and tweak it however you prefer.

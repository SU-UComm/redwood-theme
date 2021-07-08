# Contributing to Redwood

## One-time setup

### Install required tools
You will need the following tool to set up your development environment:
* [composer](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx) -
  I recommend installing this [globally](https://getcomposer.org/doc/00-intro.md#globally)

### Create a local WordPress site
University Communications uses [Local](https://localwp.com/) to create local WordPress dev environments.

### Clone and configure Redwood's repo
1. `cd` to the themes directory in your local install. If you're using Local, that would be
     `app/public/wp-content/themes`.
1. Clone the [redwood-theme](https://github.com/SU-UComm/redwood-theme) repo into the `redwood` directory:  
   `git clone git@github.com:SU-UComm/redwood-theme.git redwood`

### Install dependencies and configure autoload
1. `cd` into the theme directory. If you're using Local, that would be
   `app/public/wp-content/themes/redwood`.
1. `composer install` - this installs Timber, Twig, and the PHP packages they depend on
1. `composer dump -o` - this generates an optimized file that enables autoloading of php files

### Configure front-end development environment
The [Redwood Dev](https://github.com/SU-UComm/redwood-dev) repo contains the source SCSS
and JS for Redwood and all child themes of Redwood. If you plan to work on the front-end
of Redwood or any child themes, follow the
[instructions in the Redwood Dev repo](https://github.com/SU-UComm/redwood-dev)
to clone that repo and configure the front-end dev environment.

> Note: You should **clone and configure this repo before cloning and configuring
> Redwood Dev**, as the last step in configuring Redwood Dev copies Decanter's
> Twig templates to this theme's directory. This theme directory must therefore
> exist prior to doing `npm install` in the `dev/` directory.
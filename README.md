# ReadMe

* Xeneco is a PHP application Framework
* Xeneco does not use composer
* Xeneco is light-wight
* Xeneco is platform and location agnostic
* Xeneco is NOT MVC

### If Xeneco is not MVC, then what is it?

The paradigm for xeneco is not not to be all things to all people, nor does the framework insist that you work in a certain way, nor does it assume that is the only framework in play.

Instead, Xenco allows for a light weight way to route a request to an endpoint and respond; "Route-Endpoint-Respond"

If you apprach Xenco from an MVC background or point of view then the "Endpoints" with Xeneco map nicely to the controllers in MVC. In Xeneco Views and Models are optional, treated almost like libraries you would implement yourself.

### No Models? No Views?

Basic database adapters to MySQL, Filesystem, APC and couchbase are provided

A simple dispatch loop (more on that later) provides events to which a view layer could be bound

## Autoloading

The xeneco autoloader works by scanning all files in the xeneco framework folder and the application folder implemeting xeneco. From these PHP source files a manifest is created mapping fully qualified class names to full filepaths.

If running in "DEV" mode the autoloader will generate this manifest whenever a class is requested but does not exist. If the mode is not "DEV" then this manifest generation does not execute; assuming that the minifest already exists.

A build script could trigger this manifest generation

## Modes

Development / Production / Environment modes can be set by the implementing application.

"DEV" is set by default, no others are assumed

The farmework carries out other tasks when in "DEV" mode

Set the mode in bootstrap.php with

    \settings\general::Load()->set(['XENECO','ENV'], 'DEV');
	
## General Concepts

### Contexts and Endpoints

An endpoint is ultimately the class that will be instantiated for given request.

Contexts allow grouping and routing rules to be applied to a set of Endpoints

All endpoints must be declared inside a Context

The application must have at least one context

When using more than one context, one of the contexts must be defined as the default context

Examples of different contexts may include

* web (default) - Where all front end web traffic for general browsing is routed too
* admin - where your build your application's back end
* api - somewhere to expose your ReSTful API

Set the contexts in bootstrap.php

Context routing may be folder based, eg

* www.mywebsite.com/
* www.mywebsite.com/admin/
* www.mywebsite.com/api/

or subdomain based, eg

* www.mywebsite.com/
* admin.mywebsite.com/
* api.mywebsite.com/

### Dispatch Loop and Filters

### Events and Plugins

### Modules

# Setup


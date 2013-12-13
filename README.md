# Epi

[![Build Status](https://secure.travis-ci.org/vespakoen/epi.png?branch=master)](http://travis-ci.org/vespakoen/epi)

Creating an API can be a tedious task, and if you need to filter your data in specific ways, things are bound to get nasty.
Epi comes with a controller that handles your basic CRUD tasks for an Eloquent model, but the index / "read all" method it where Epi really shines.

Imagine that you have a `Page` model that has got a relationship with a `PageTranslation` model.
Perhaps you would want to retrieve a page by it's slug that lives in the PageTranslation model.
With Epi, it would be a simple as requesting the following URL: `api/pages?filter[translation.slug]=some-slug`.

## Installation
To add Epi to your Laravel application, follow these steps:

Add the following to your `composer.json` file:

```json
"vespakoen/epi" : "dev-master"
```

Then, run `composer update` or `composer install` if you have not already installed packages.

Add the line below to the `providers` array (at the end) in the `app/config/app.php` configuration file:

```php
'Vespakoen\Epi\EpiServiceProvider',
```

Optionally, if you don't want to add "use" statements to your controller, add the line below to the `aliases` array (at the end) in the `app/config/app.php` configuration file:

```php
'EpiController' => 'Vespakoen\Epi\Controllers\EpiController',
```

## Configuration
You will want to run the following command to publish the config to your application, otherwise it will be overwritten when the package is updated.

```shell
php artisan config:publish vespakoen/epi
```

## Usage
To build an Api using Epi we will need to create a controller that extends from the EpiController and is then registered with the router.
First we need to create a controller, preferrably, this controller is seperated from the rest of your controllers by putting the controllers for the Api in a new folder.
This folder could be `app/controllers/api` (make sure you add this path to the autoloader in `app/start/global.php` !) or `/src/Somevendor/SomePackage/Controllers/Api` depending on your situation.

Here is an example:
```php
<?php

class ApiUsersController extends Controller {

	/**
	 * Create a new ApiUsersController instance.
	 *
	 * @param  Illuminate\Database\Eloquent\Model  $model
	 * @return void
	 */
	public function __construct(User $model)
	{
		// Epi will look for $this->model, so make sure it is set like this.
		$this->model = $model;
	}

	/**
	 * $eagerLoad Relations to eagerload by default
	 *
	 * @var array
	 */
	public $eagerLoad = array();

	/**
	 * $indexRules Validation rules used when getting a list of resources
	 *
	 * @var array
	 */
	public $indexRules = array();

	/**
	 * $storeRules Validation rules used when storing a resource
	 *
	 * @var array
	 */
	public $storeRules = array(
		'email' => 'required|email'
	);

	/**
	 * $updateRules Validation rules used when updating a resource
	 *
	 * @var array
	 */
	public $updateRules = array(
		'email' => 'required|email'
	);

}

```

Now we need to register this controller with the router, which is as easy as adding the following lines to your `app/routes.php` file.
```php
Route::resource('api/users', 'ApiUsersController');
```

Done!

## How does it work?
Under the hood Epi works it's magic to make all the good stuff happen, I can imagine you got curious how it works,
to understand this, let's first take a look at the objects that live in the Epi codebase.

### Manipulators
A manipulator is an object that modifies the query that will be made.
Manipulators will also add and use aliases to avoid any conficts with self-referencing relationships.
Epi comes with the following manipulators built-in.

##### Filter
Calls `->where()` on the query and aliases tables
##### Join
Calls `->join()` on the query and aliases tables
##### Limiter
Calls `->take()` and optionally `->skip()` on the query and aliases tables
##### Sorter
Calls `->orderBy()` on the query and aliases tables

### Extractors
Extractors get all the input, and have to make sure to extract the correct manipulators from them.
Epi comes with the following extractors built-in.

##### FilterExtractor
...
##### JoinExtractor
...
##### LimiterExtractor
...
##### SorterExtractor
...

### Relations
Epi has it's own Relation object for every Laravel relation, these objects have a `->getJoins()` method that will returns Join manipulators that
are needed for filtering or sorting on a related table.

More soon...

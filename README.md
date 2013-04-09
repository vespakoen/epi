# EPI - The API builder for Eloquent

## BIG FAT WARNING

This package is a work in progress, at this time of writing, only the ManyToMany and OneToMany relationships are supported.
Also, self-relating stuff has some issues, for example:
Product -> hasMany -> Product
This has to do with the aliasing, a fix is on the way.


## Why did you make this?

Building an API is a tedious task, especially the index methods.
EPI is here to help.


## What does it do?

it's main task is to provide a simple way to filter, sort
and limit the results on your query for the index method, it does this by adding the necessery joins to eloquent's first query
and adds the where's, ordering and limiting for you, based on the input (see: How to use it)

Besides this it provides a base controller that provides all the methods for you.
If you need something sub-standard, just override that method with your own code (see: Example controller).


## How to make it work

First, you have to add the EPI package's service provider to the `providers` array
in your `app/config/app.php` file.
The name of the service to add is `Vespakoen\EPI\EPIServiceProvider`.

Now create a controller that extends from `Vespakoen\EPI\Controllers\EPIController`,
You can checkout the example controller in `vendor/vespakoen/epi/src/Vespakoen/EPI/Example/Controllers/ProductController.php`

If you don't want to use the provided controller, and just want to use the filtering / sorting / limiting functionality,
you can use the following code:

```php
	$results = EPI::modelInstance(new Product)
		->with(array('stuff', 'to.eagerload'))
		->get();

	// or

	$results = EPI::model('Product')
		->with(array('stuff', 'to.eagerload'))
		->get();
```

## How to use it

After you have setup a controller, and registered it with the router (see: `vendor/vespakoen/epi/src/Vespakoen/EPI/EPIServiceProvider.php`)
You can easily view the result of the `index` and `show` method in your browser (they are the only GET requests)


For the index method (`GET /api/product`), EPI is used, below is a list of it's functionality:


You can add your filters like this:

?filter[relation1.relation2.column]=value

?filter[categories.translation.slug]=product-name


You can sort like this:

?sort[relation1.relation2.column]=ASC/DESC

?sort[categories.translation.slug]=DESC

?sort=categories.translation.slug (will default to ASC)


You can limit like this:

?offset=10&limit=25


or this:

?page=1&perpage=25


Supported URL's for EPIController are (depends on how they are routed):

`GET /resource`
`GET /resource/{id}`
`POST /resource`
`PUT /resource/{id}`
`DELETE /resource/{id}`


# Enjoy!
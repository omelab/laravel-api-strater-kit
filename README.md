## passport
to install passport on laravel 10 hou should use this command

```bash
composer require laravel/passport -W
#or
composer require laravel/passport  --with-all-dependencies
```

you can flow this link for generate [personal access client](https://laravel.com/docs/10.x/passport#creating-a-personal-access-client)




## generate refresh token
- https://www.w3adda.com/blog/laravel-7-passport-refresh-token-example
- https://www.laravelia.com/post/laravel-10-passport-api-authentication-tutorial


## Forse json on api routes

- https://dev.to/arxeiss/force-json-response-on-all-api-routes-in-laravel-29h



## Design pattern
- https://dev.to/zhukmax/design-patterns-in-php-8-simple-factory-o0l
- https://dev.to/zhukmax/design-patterns-in-php-8-flyweight-5e0h

## Git

```bash
#set upstream
git push --set-upstream origin master

```



## Laravel Permission

- https://spatie.be/docs/laravel-permission/v5/installation-laravel
- https://spatie.be/docs/laravel-permission/v5/basic-usage/middleware
- https://github.com/spatie/laravel-permission/blob/main/docs/basic-usage/middleware.md

- https://medium.com/@parthpatel0516/laravel-multi-auth-using-guards-and-spatie-permission-with-example-api-authentication-4d5376f60d76

Using Permissions via Roles you can [flow this tutorials](https://spatie.be/docs/laravel-permission/v5/basic-usage/role-permissions)


A role can be assigned to any user:

```php
$user->assignRole('writer');

// You can also assign multiple roles at once
$user->assignRole('writer', 'admin');
// or as an array
$user->assignRole(['writer', 'admin']); 
```

A role can be removed from a user:

```php
$user->removeRole('writer');
```

Roles can also be synced:

```php
// All current roles will be removed from the user and replaced by the array given
$user->syncRoles(['writer', 'admin']);
``` 

get all assign roles on without pivot table, it will return you all roles information

```php
 $roles = $user->roles->makeHidden(['pivot']); 
``` 
but if hyou want only role name as array like ['admin','writer']

```php
 $roles = $user->roles()->pluck('name');  
```


get all assign permissions on without pivot table, it will return you all permissions information

```php
 $permissions = $user->permissions->makeHidden(['pivot']); 
``` 
but if hyou want only permissions name as array like ['create','edit','view']

```php
 $permissions = $user->permissions()->pluck('name');  
```



### Checking Roles

You can determine if a user has a certain role:

```php
$user->hasRole('writer');

// or at least one role from an array of roles:
$user->hasRole(['editor', 'moderator']);
```
You can also determine if a user has any of a given list of roles:


```php
$user->hasAnyRole(['writer', 'reader']);
// or
$user->hasAnyRole('writer', 'reader');
```


```php

//in laravel 10 we need to add middlewre on App\Http\Kernel
protected $middlewareAliases = [
    // ...
    'role' => \Spatie\Permission\Middlewares\RoleMiddleware::class,
    'permission' => \Spatie\Permission\Middlewares\PermissionMiddleware::class,
    'role_or_permission' => \Spatie\Permission\Middlewares\RoleOrPermissionMiddleware::class,
];
```

Now we can check user role with route middleware

```php 
Route::group(['prefix'=>'permissions', 'middleware' => ['role:super-admin']], function () {
    Route::get('/', [PermissionController::class, 'index']); 
});
```

For checking against a single permission (see Best Practices) using can, you can use the built-in Laravel middleware provided by `\Illuminate\Auth\Middleware\Authorize::class` like this:

```php 
Route::group(['middleware' => ['can:publish articles']], function () {
    //
}); 
```


## Database relation with references

you can easily find out column type just [click here](https://laravel.com/docs/10.x/migrations#available-column-types)


if you want to set nulable foreign keys

```php
$table->foreignId('article_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('set null');
```

also if you want to cascade relation

```php
 $table->foreign('article_id') ->references('id') ->on('articles') ->onCascade('delete');
```

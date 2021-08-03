# camohub/laravel-datagrid
## Laravel datagrid

This is the datagrid for Laravel models. 
Accepts Illuminate\Database\Eloquent\Builder and Illuminate\Database\Query\Builder as datasource.

## Installation
```
composer install camohub/laravel-datagrid
```

## Description

Datagrid constructor required 

This package is based on form GET request. Whole table is a form. 
You can simply catch submit event if you need.
Empty inputs are disabled on submit by js and automatically removed from url.
Datagrid contains this groups of inputs:

- sort inputs - every sortalbe field has its own hidden input. 
	After click on the sortable column js sets the hidden input value. 
	If value is empty hidden input is disabled.
- filter inputs - filter inputs triggers form submit on input event. 
	There is also timeout as throttling to wait for another input events.
	This timeout can be set in php grid definition globally by setJSFilterTimeout().
- perPage select - onchange event triggers form submit immediately.
- page - paginator page param is also as hidden input.

This form submit implementation has one little disadvantage. 
It removes all other GET parameters from url. But it is easy to fix it. 
You can set all necessary GET parameters via $grid->addGetParam('name').



## Example

Controller code could implement a method which returns datagrid instance.
```php
public function getArticlesDatagrid()
{
    $grid = new Datagrid(Article::with('user'));

    $grid->addColumn('id')
        ->setSort();

    $grid->addColumn('title')
        ->setSort()
        ->setFilter(function($model, $value) {
            return $value ? $model->where('title', 'like', "%$value%") : $model;
        });

    $grid->addColumn('created_at', 'Created')
        // Needs valid js regexp pattern.
        ->setJSFilterPattern('\d{2}\.\d{2}\.\d{4} \d{2}:\d{2}')
        ->setRender(function($value, $item) {
            return '<b>' . $value->format('d.m.Y H:i') . '</b>';
        })
        // Turns off template html escaping.
        ->setNoEscape()
        ->setSort();

    $grid->addColumn('visible', 'Visible')
        // Outher is td element
        ->setOutherClass(function($value, $item) {
            return $value ? 'bg-success' : 'bg-danger';
        });

    // HasOne relation
    $grid->addColumn('user.name', 'User');

    // ManyHasMany relation
    $grid->addColumn('user.roles', 'Roles')
        ->setRender(function($value, $item) {
            return $value->map( function($value) { return $value->name; } )->join(', ');
        });

    // TYPE_CUSTOM intended for content not related to model.
    $grid->addColumn('', '', Column::TYPE_CUSTOM)
        ->setNoEscape()
        ->setRender(function($value, $item) {
            return '
                <a href="' . route('admin.articles.edit', ['id' => $item->id]) . '">edit</a>
                <a href="' . route('admin.articles.visibility', ['id' => $item->id]) . '">visibility</a>
                <a href="' . route('admin.articles.delete', ['id' => $item->id]) . '" class="text-danger">delete</a>
            ';
        });

    return $grid;
}
```
And the template could look like
```blade
{{$grid->render()}}
```


## Options

There are two groups of options. 
Global datagrid options and column specific options.

### Datagrid options

- **setDefaultPerPage()** - yes it really sets the default perPage items number.

- **setPerPage()** - expects array with possible dropdown options like [10, 25, 50, 100].

- **setOnEachSide()** - it is the wrapper above the Laravel pagination onEachSide() option.

- **setTableClass()** - default is 'table table-striped table-hover table-bordered';

- **setJSFilterTimeout()** - sets javascript timeout on input event. Default is 250ms.
	
- **setSubmitOnEnter()** - prevent submit on input event and will wait for hit enter key to submit.
	This option is possible to set for the whole grid or for one column. 
	Does not affect sorting, pagination and perPage select. They are still automatically submited.

- **setGetParams()** - form submit removes all GET params from url which are not
	the part of the form. Request will contain only form inputs as GET prameters. 
	setGetParams('paramName') will include all necessary GET params 
	which should be included in all datagrid GET requests. 
	
### Column options

- **setRender()** - accepts callback with two parameters - value and row.

- **setSort()** - accepts empty to simple sort according flied name 
	or callback which gets two params - queryBuilder and sort value.

- **setFilter()** - accepts callback with two parameters - queryBuilder and filter value. 
	Filter callback is not called if filter value is NULL or empty string. Other values like 0 will call the filter.

- **setJSFilterPattern()** - accepts js regexp patterns as string. If value does not match 
	the pattern validator will block the request and will add .text-danger class to input field.
	
- **setSubmitOnEnter()** - prevent submit on input event and will wait for hit enter key to submit.
	This option is possible to set for the whole grid or for one column. 
	Does not affect sorting, pagination and perPage select. They are still automatically submited.

- **setFilterRender()** - allows you to render filter input manually. 
	Be sure rendered filter input has css class chgrid-filter.

- **setNoEscape()** - custom render wont be escaped. Template use {!! !!} instead of {{}}.

- **setOutherClass()** - accepts callback. Callback will be useful if you 
	need to make some conditional styles for the field. 
	Callback will get two parameters - value and row.

- **setOutherTitleClass** - accepts string value. Will set up css class of TH element with title.


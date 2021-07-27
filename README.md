# camohub/laravel-datagrid
## Laravel datagrid

This is the datagrid for Laravel models.

Installation
------------
```
composer install camohub/laravel-datagrid
```

Description
-----------
Datagrid constructor required 

This package is based on form GET request. Whole table is a form. 
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



Examples
------------
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


Options
------------

Datagrid requires non optional parameters. 
1. Request object
2. Model without pagination - datagrid will paginate it.

# Lara-Service-Model

Generalized Service layer

## Installation

Run the following command from you terminal:

 ```bash
 composer require "omnicode/lara-service-model: 3.0.*"
 ```

or add this to require section in your composer.json file:

 ```
 "omnicode/lara-service-model": "3.0.*"
 ```

then run ```composer update```


## Usage

First, create your Service class like shown below with example `UserService`

```php
<?php

namespace App\Services;

use App\Models\User;
use App\Validators\UserValidator;
use LaraServiceModel\LaraServiceModel;

/**
* Class UserService
 * @package App\Services
 */
class UserService extends LaraServiceModel
{
    public function __construct(User $userModel)
    {
        parent::__construct($userModel);
    }
}

```

To implement the validation used [LaraValidation](https://github.com/omnicode/lara-validation).
And finally, use the service and validator in the controller:

```php
<?php

namespace App\Services;

use App\Models\User;
use App\Validators\UserValidator;
use LaraServiceModel\LaraServiceModel;

/**
* Class UserService
 * @package App\Services
 */
class UserService extends LaraServiceModel
{
    public function __construct(User $userModel, UserValidator $userValidator)
    {
        parent::__construct($userModel, $userValidator);
    }
}

```

## Available Methods

The following methods are available in LaraServiceModel:

```php
    $this->setBaseModel($userModel);
    
    $this->setBaseValidator($userValidator);
    
    $this->all($columns = null): Collection;
    
    $this->paginate(int $count = 20, $columns = null, $group = 'list'): array;
    
    $this->simplePaginate(int $count = 20, $columns = null): Paginator;
    
    $this->create(array $data, string $ruleValidate = 'default');
    
    $this->createWith(array $data, $relations, string $ruleValidate = 'default');
    
    $this->update(array $data, $id, string $ruleValidate = 'default');
    
    $this->updateWith(array $data, $id, $relations = null, $rule = 'default');
    
    $this->delete(int $id, string $deletedMethod = 'delete');
    
    $this->deleteBy(string $column, int $id, string $deletedMethod = 'delete');
    
    $this->deleteAll(); // Use caution (you can delete all data from the table)
    
    $this->destroy(int $id, string $deletedMethod = 'delete');
    
    $this->destroyAll(); // Use caution (you can delete all data from the table)
    
    $this->first($columns = null);
    
    $this->last($columns = null);
    
    $this->find($id, $columns = null);
    
    $this->findBy(string $attribute, $value, $columns = null);
    
    $this->findForShow($id, $columns = null);
    
    $this->findAllBy(string $attribute, $value, $columns = null);
    
    $this->findList(?array $listable = null);
    
    $this->findListBy(string $attribute, $value, ?array $listable = null);
    
    $this->findCount(?string $attribute = null, $cmpOrValue = '=', $value = null);
    
    $this->increment(string $column, $value = 1);
    
    $this->decrement(string $column, $value = 1);
    
    $this->pushWhere($column, $cmp = '=', $value = null);
    
    $this->pushOrWhere($column, $cmp = '=', $value = null);
    
    $this->pushWhereNull($column);
    
    $this->pushHas(string $relation, $cmpOrValue = '=', $value = null);
    
    $this->pushWhereHas(string $relation, $condition);
    
    $this->pushDoesntHave(string $relation);
    
    $this->pushWhereDoesntHave(string $relation, $condition);
    
    $this->pushOrderBy(string $column, $order = 'asc');
    
    $this->pushLimit(int $limit);
    
    $this->pushOffset(int $count);
    
    $this->pushSkip(int $count);
    
    $this->pushTake(int $count);
    
    $this->pushHaving($column, $cmpOrValue = '=', $value = null);
    
    $this->pushGroupBy(...$columns);
    
    $this->pushSelect($columns);
    
    $this->pushWhereBetween(string $column, array $values);
    
    $this->pushWhereNotBetween(string $column, array $values);
    
    $this->pushWhereIn(string $column, array $values);
    
    $this->pushWhereNotIn(string $column, array $values);
    
    $this->pushSearch($column, $search = null);
    
    $this->pushWhereDate(string $column, string $value);
    
    $this->pushWhereMonth(string $column, string $value);
    
    $this->pushWhereDay(string $column, string $value);
    
    $this->pushWhereTime(string $column, string $value);
    
    $this->pushWith($with);
    
    $this->pushWithCount($with);
    
    $this->setValidationErrors($errors);
    
    $this->getValidationErrors();
    
    $this->startTransaction();
    
    $this->commitTransaction();
    
    $this->rollbackTransaction();
```

## Usage examples

```php
<?php

namespace App\Services;

use App\Models\Article;
use App\Validators\ArticleValidator;
use LaraServiceModel\LaraServiceModel;

/**
* Class ArticleService
* @package App\Services
*/
class ArticleService extends LaraServiceModel
{
    /**
     * ArticleService constructor.
     * @param Article $articleModel
     * @param ArticleValidator $articleValidator
     */
    public function __construct(Article $articleModel, ArticleValidator $articleValidator)
    {
        parent::__construct($articleModel, $articleValidator);
    }
    
    /**
     * @return mixed
     */
    public function index()
    {
        $relatedCount = [
            'category' => ['name', '=', 'name'],
            'images'
        ];
        
        $this->pushWithCount($relatedCount);
        return $this->paginate();
    }
    
    /**
     * @param int $id
     * @return mixed
     */
    public function show(int $id)
    {
        $related = [
            'category' => [
                'pushSelect' => [
                    'id',
                    'article_id',
                    'name'
                ],
                'pushLimit' => 5,
                'pusWhere' => [
                    ['status', 'active'],
                    ['children', '>', 5]
                ]
            ]
        ];
        
        $columns = [
            'id',
            'title',
            'description',
            'keywords',
            'name',
            'header',
            'content',
            'views'
        ];
        
        return $this->pushSearch('name', 'article name')
             ->pushWhere('views', '>', 5)
             ->pushWith($related)
             ->find($id, $columns);
    }
    
    /**
     * @param array $data
     * @return bool|mixed
     */
    public function createArticle(array $data)
    {
        return $this->create($data, 'customValidatorName');
    }
    
    /**
     * @param array $data
     * @return mixed
     */
    public function createArticleAndRelations(array $data)
    {
        $data = [
            'title' => 'article title',
            'description' => 'article description',
            'keywords' => 'article keywords',
            'name' => 'article name',
            'header' => 'article header',
            'content' => 'article content',
            'images' => [ // HasMany relation
                ['url' => 'url one'],
                ['url' => 'url two']
            ],
            'categories_ids' => [ // BelongsToMany relation
                1,
                2,
                15
            ],
            'type' => [ // HasOne relation
                'type_name' => 'article type name'
            ]
        ];
        
        return $this->createWith($data, ['images', 'categories']);
    }
    
    /**
     * @param int $id
     * @return mixed
     */
    public function deleteArticle(int $id)
    {
        return $this->delete($id);
    }
    
    /**
     * @param string $slug
     * @return mixed
     */
    public function deleteArticleBySlug(string $slug)
    {
        return $this->deleteBy('slug', $slug);
    }
}

```

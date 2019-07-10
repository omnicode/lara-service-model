<?php

namespace LaraServiceModel\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\Paginator;
use phpDocumentor\Reflection\Types\Mixed_;

/**
 * Interface LaraServiceModelInterface
 * @package PhpScrapper\Interfaces
 */
interface LaraServiceModelInterface
{
    /**
     * @param $currentModel
     * @return mixed
     */
    public function setBaseModel($currentModel);

    /**
     * @param $currentValidator
     * @return mixed
     */
    public function setBaseValidator($currentValidator);

    /**
     * @param null $columns
     * @return mixed
     */
    public function all($columns = null): Collection;

    /**
     * @param int $count
     * @param null $columns
     * @param string $group
     * @return array
     */
    public function paginate(int $count = 20, $columns = null, $group = 'list'): array;

    /**
     * @param int $count
     * @param null $columns
     * @return Paginator
     */
    public function simplePaginate(int $count = 20, $columns = null): Paginator;

    /**
     * Validate and create new data
     *
     * @param array $data
     * @param null|string $ruleValidate
     * @return mixed
     */
    public function create(array $data, ?string $ruleValidate = 'default');

    /**
     * Validate and create new data and relations
     *
     * @param array $data
     * @param $relations
     * @param string $ruleValidate
     * @return mixed
     */
    public function createWith(array $data, $relations, ?string $ruleValidate = 'default');

    /**
     * Validate and update data
     *
     * @param array $data
     * @param $id
     * @param null|string $ruleValidate
     * @return mixed
     */
    public function update(array $data, $id, ?string $ruleValidate = 'default');
    
    /**
     * Update items by column
     *
     * @param string $column
     * @param $value
     * @param array $data
     * @return mixed
     */
    public function updateBy(string $column, $value, array $data);

    /**
     * Validate, update data and relations
     *
     * @param array $data
     * @param $id
     * @param null $relations
     * @param null|string $rule
     * @return mixed
     */
    public function updateWith(array $data, $id, $relations = null, ?string $ruleValidate = 'default');

    /**
     * Delete item
     *
     * @param int $id
     * @param string $deletedMethod
     * @return mixed
     */
    public function delete(int $id, string $deletedMethod = 'delete');

    /**
     * Delete items by column
     *
     * @param string $column
     * @param int $id
     * @param string $deletedMethod
     * @return mixed
     */
    public function deleteBy(string $column, int $id, string $deletedMethod = 'delete');

    /**
     * Delete all items
     *
     * @return mixed
     */
    public function deleteAll();

    /**
     * Destroy item
     *
     * @param int $id
     * @param string $deletedMethod
     * @return mixed
     */
    public function destroy(int $id, string $deletedMethod = 'delete');

    /**
     * Destroy all items
     *
     * @return mixed
     */
    public function destroyAll();

    /**
     * Get first item
     *
     * @param null $columns
     * @return mixed
     */
    public function first($columns = null);

    /**
     * Get last item
     *
     * @param null $columns
     * @return mixed
     */
    public function last($columns = null);

    /**
     * Find item
     *
     * @param $id
     * @param null $columns
     * @return mixed
     */
    public function find($id, $columns = null);

    /**
     * Find item by column
     *
     * @param string $attribute
     * @param $value
     * @param null $columns
     * @return mixed
     */
    public function findBy(string $attribute, $value, $columns = null);

    /**
     * Find item for show page
     *
     * @param $id
     * @param null $columns
     * @return mixed
     */
    public function findForShow($id, $columns = null);

    /**
     * Find items by column
     *
     * @param string $attribute
     * @param $value
     * @param null $columns
     * @return mixed
     */
    public function findAllBy(string $attribute, $value, $columns = null);

    /**
     * Find items in the form list
     *
     * @param array|null $listable
     * @return mixed
     */
    public function findList(?array $listable = null);

    /**
     * Find items by column in the form list
     *
     * @param string $attribute
     * @param $value
     * @param array|null $listable
     * @return mixed
     */
    public function findListBy(string $attribute, $value, ?array $listable = null);

    /**
     * Find items count
     *
     * @param null|string $attribute
     * @param string $cmpOrValue
     * @param null $value
     * @return mixed
     */
    public function findCount(?string $attribute = null, $cmpOrValue = '=', $value = null);

    /**
     * Exist key
     *
     * @param $id
     * @return mixed
     */
    public function exists($id);

    /**
     * Exist key where
     *
     * @param $attribute
     * @param $value
     * @return mixed
     */
    public function existsWhere($attribute, $value);

    /**
     * Increment column
     *
     * @param string $column
     * @param int $value
     * @return mixed
     */
    public function increment(string $column, $value = 1);

    /**
     * Decrement column
     *
     * @param string $column
     * @param int $value
     * @return mixed
     */
    public function decrement(string $column, $value = 1);

    /**
     * Condition where in query
     *
     * @param $column
     * @param string $cmp
     * @param null $value
     * @return mixed
     */
    public function pushWhere($column, $cmp = '=', $value = null);

    /**
     * Condition orWhere in query
     *
     * @param $column
     * @param string $cmp
     * @param null $value
     * @return mixed
     */
    public function pushOrWhere($column, $cmp = '=', $value = null);

    /**
     * Condition whereNull in query
     *
     * @param string $column
     * @return mixed
     */
    public function pushWhereNull(string $column);

    /**
     * Condition has in query
     *
     * @param string $relation
     * @param string $cmpOrValue
     * @param null $value
     * @return mixed
     */
    public function pushHas(string $relation, $cmpOrValue = '=', $value = null);

    /**
     * Condition whereHas in query
     *
     * @param string $relation
     * @param $condition
     * @return mixed
     */
    public function pushWhereHas(string $relation, $condition);

    /**
     * Condition doesntHave in query
     *
     * @param string $relation
     * @return mixed
     */
    public function pushDoesntHave(string $relation);

    /**
     * Condition whereDoesntHave in query
     *
     * @param string $relation
     * @param $condition
     * @return mixed
     */
    public function pushWhereDoesntHave(string $relation, $condition);

    /**
     * Condition OrderBy in query
     *
     * @param string $column
     * @param string $order
     * @return array
     */
    public function pushOrderBy(string $column, $order = 'asc');

    /**
     * Condition limit in query
     *
     * @param int $limit
     * @return mixed
     */
    public function pushLimit(int $limit);

    /**
     * Condition offset in query
     *
     * @param int $count
     * @return mixed
     */
    public function pushOffset(int $count);

    /**
     * Condition skip in query
     *
     * @param int $count
     * @return mixed
     */
    public function pushSkip(int $count);

    /**
     * Condition take in query
     *
     * @param int $count
     * @return mixed
     */
    public function pushTake(int $count);

    /**
     * Condition having in query
     *
     * @param $column
     * @param string $cmpOrValue
     * @param null $value
     * @return mixed
     */
    public function pushHaving($column, $cmpOrValue = '=', $value = null);

    /**
     * Condition groupBy in query
     *
     * @param mixed ...$columns
     * @return mixed
     */
    public function pushGroupBy(...$columns);

    /**
     * Condition select in query
     *
     * @param $columns
     * @return mixed
     */
    public function pushSelect($columns);

    /**
     * Condition whereBetween in query
     *
     * @param string $column
     * @param array $values
     * @return mixed
     */
    public function pushWhereBetween(string $column, array $values);

    /**
     * Condition whereNotBetween in query
     *
     * @param string $column
     * @param array $values
     * @return mixed
     */
    public function pushWhereNotBetween(string $column, array $values);

    /**
     * Condition whereIn in query
     *
     * @param string $column
     * @param array $values
     * @return mixed
     */
    public function pushWhereIn(string $column, array $values);

    /**
     * Condition whereNotIn in query
     *
     * @param string $column
     * @param array $values
     * @return mixed
     */
    public function pushWhereNotIn(string $column, array $values);

    /**
     * Condition Search in query
     *
     * @param $column
     * @param null $search
     * @return mixed
     */
    public function pushSearch($column, $search = null);

    /**
     * Condition whereDate in query
     *
     * @param string $column
     * @param string $value
     * @return mixed
     */
    public function pushWhereDate(string $column, string $value);

    /**
     * Condition whereMonth in query
     *
     * @param string $column
     * @param string $value
     * @return mixed
     */
    public function pushWhereMonth(string $column, string $value);

    /**
     * Condition whereDay in query
     *
     * @param string $column
     * @param string $value
     * @return mixed
     */
    public function pushWhereDay(string $column, string $value);

    /**
     * Condition whereTime in query
     *
     * @param string $column
     * @param string $value
     * @return mixed
     */
    public function pushWhereTime(string $column, string $value);

    /**
     * Condition with relation in query
     *
     * @param $with
     * @return mixed
     */
    public function pushWith($with);

    /**
     * Condition withCount relation in query
     *
     * @param $with
     * @return mixed
     */
    public function pushWithCount($with);

    /**
     * Add new validation errors
     *
     * @param $errors
     * @return mixed
     */
    public function setValidationErrors($errors);

    /**
     * Get all validation errors
     *
     * @return mixed
     */
    public function getValidationErrors();

    /**
     * Start transaction for database
     *
     * @return mixed
     */
    public function startTransaction();

    /**
     * Commit transaction for database
     *
     * @return mixed
     */
    public function commitTransaction();

    /**
     * Rollback transaction for database
     *
     * @return mixed
     */
    public function rollbackTransaction();
}

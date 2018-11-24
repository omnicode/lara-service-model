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
     * @param int $id
     * @param string $deletedMethod
     * @return mixed
     */
    public function delete(int $id, string $deletedMethod = 'delete');

    /**
     * @param string $column
     * @param int $id
     * @param string $deletedMethod
     * @return mixed
     */
    public function deleteBy(string $column, int $id, string $deletedMethod = 'delete');

    /**
     * @param int $id
     * @param string $deletedMethod
     * @return mixed
     */
    public function destroy(int $id, string $deletedMethod = 'delete');

    /**
     * @param null $columns
     * @return mixed
     */
    public function first($columns = null);

    /**
     * @param null $columns
     * @return mixed
     */
    public function last($columns = null);

    /**
     * @param $id
     * @param null $columns
     * @return mixed
     */
    public function find($id, $columns = null);

    /**
     * @param string $attribute
     * @param $value
     * @param null $columns
     * @return mixed
     */
    public function findBy(string $attribute, $value, $columns = null);

    /**
     * @param $id
     * @param null $columns
     * @return mixed
     */
    public function findForShow($id, $columns = null);

    /**
     * @param string $attribute
     * @param $value
     * @param null $columns
     * @return mixed
     */
    public function findAllBy(string $attribute, $value, $columns = null);

    /**
     * @param array|null $listable
     * @return mixed
     */
    public function findList(?array $listable = null);

    /**
     * @param string $attribute
     * @param $value
     * @param array|null $listable
     * @return mixed
     */
    public function findListBy(string $attribute, $value, ?array $listable = null);

    /**
     * @param null|string $attribute
     * @param string $cmpOrValue
     * @param null $value
     * @return mixed
     */
    public function findCount(?string $attribute = null, $cmpOrValue = '=', $value = null);

    /**
     * @param string $column
     * @param int $value
     * @return mixed
     */
    public function increment(string $column, $value = 1);

    /**
     * @param string $column
     * @param int $value
     * @return mixed
     */
    public function decrement(string $column, $value = 1);

    /**
     * @param $column
     * @param string $cmp
     * @param null $value
     * @return mixed
     */
    public function pushWhere($column, $cmp = '=', $value = null);

    /**
     * @param $column
     * @param string $cmp
     * @param null $value
     * @return mixed
     */
    public function pushOrWhere($column, $cmp = '=', $value = null);

    /**
     * @param string $column
     * @return mixed
     */
    public function pushWhereNull(string $column);

    /**
     * @param string $column
     * @param string $order
     * @return array
     */
    public function pushOrderBy(string $column, $order = 'asc');

    /**
     * @param int $limit
     * @return mixed
     */
    public function pushLimit(int $limit);

    /**
     * @param int $count
     * @return mixed
     */
    public function pushOffset(int $count);

    /**
     * @param $columns
     * @return mixed
     */
    public function pushSelect($columns);

    /**
     * @param string $column
     * @param array $values
     * @return mixed
     */
    public function pushWhereBetween(string $column, array $values);

    /**
     * @param string $column
     * @param array $values
     * @return mixed
     */
    public function pushWhereNotBetween(string $column, array $values);

    /**
     * @param string $column
     * @param array $values
     * @return mixed
     */
    public function pushWhereIn(string $column, array $values);

    /**
     * @param string $column
     * @param array $values
     * @return mixed
     */
    public function pushWhereNotIn(string $column, array $values);

    /**
     * @param $column
     * @param null $search
     * @return mixed
     */
    public function pushSearch($column, $search = null);

    /**
     * @param string $column
     * @param string $value
     * @return mixed
     */
    public function pushWhereDate(string $column, string $value);

    /**
     * @param string $column
     * @param string $value
     * @return mixed
     */
    public function pushWhereMonth(string $column, string $value);

    /**
     * @param string $column
     * @param string $value
     * @return mixed
     */
    public function pushWhereDay(string $column, string $value);

    /**
     * @param string $column
     * @param string $value
     * @return mixed
     */
    public function pushWhereTime(string $column, string $value);

    /**
     * @param $with
     * @return mixed
     */
    public function pushWith($with);

    /**
     * @param $with
     * @return mixed
     */
    public function pushWithCount($with);

    /**
     * @param $errors
     * @return mixed
     */
    public function setValidationErrors($errors);

    /**
     * @return mixed
     */
    public function getValidationErrors();

    /**
     * @return mixed
     */
    public function startTransaction();

    /**
     * @return mixed
     */
    public function commitTransaction();

    /**
     * @return mixed
     */
    public function rollbackTransaction();
}

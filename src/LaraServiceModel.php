<?php

namespace LaraServiceModel;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use LaraServiceModel\Interfaces\LaraServiceModelInterface;
use LaraModel\Models\LaraModel;
use LaraServiceModel\Traits\FunctionsTrait;
use LaraValidation\LaraValidator;
use Illuminate\Pagination\Paginator;

/**
 * Class PhpScrapper
 * @package PhpScrapper
 */
class LaraServiceModel implements LaraServiceModelInterface
{
    use FunctionsTrait;

    /**
     *
     */
    const GROUP = 'list';

    /**
     * @var LaraModel|null
     */
    protected $baseModel;

    /**
     * @var LaraValidator|null
     */
    protected $baseValidator;

    /**
     * last validation errors
     * @var array
     */
    protected $validationErrors;

    /**
     * @var
     */
    protected $query;

    /**
     * LaraServiceModel constructor.
     * @param $currentModel
     * @param $currentValidator
     */
    public function __construct($currentModel, $currentValidator = null)
    {
        $this->setBaseModel($currentModel);
        $this->setBaseValidator($currentValidator);
    }

    /**
     * Make Model Based abstract modelClass and set baseModel
     *
     * @param $currentModel
     */
    public function setBaseModel($currentModel)
    {
        $this->baseModel = $currentModel;
        $this->resetQuery();
    }

    /**
     * Make Model Based abstract modelClass and set baseModel
     *
     * @param $currentValidator
     */
    public function setBaseValidator($currentValidator)
    {
        $this->baseValidator = $currentValidator;
    }

    /**
     * Get all columns
     *
     * @param null $columns
     * @return Collection
     */
    public function all($columns = null): Collection
    {
        $columns = $this->fixSelectedColumns($columns);
        $all = $this->query->get($columns);
        $this->resetQuery();
        return $all;
    }

    /**
     * Get all columns in the form of pagination
     *
     * @param int $perPage
     * @param null $columns
     * @param string $group
     * @param array $options
     * @return array
     */
    public function paginate(int $perPage = 20, $columns = null, $group = self::GROUP, $options = []): array
    {
        $usedColumns = $this->getIndexableColumns(true, false, $group);
        if ( ! is_null($columns)) {
            $columns = (array)$columns;
            $usedColumns = $this->fixSelectedColumns($columns);
        }

        $this->setSortingOptions([], $group);
        $items = $this->query->paginate($perPage, $usedColumns);
        $this->resetQuery();

        return [
            $items,
            $usedColumns
        ];
    }

    /**
     * Get all columns in the form of simplePaginate
     *
     * @param int $count
     * @param null $columns
     * @return Paginator
     */
    public function simplePaginate(int $count = 20, $columns = null): Paginator
    {
        $columns = $this->fixSelectedColumns($columns);
        return $this->query->simplePaginate($count, $columns);
    }

    /**
     * Validate and create new data
     *
     * @param array $data
     * @param null|string $ruleValidate
     * @return bool|mixed
     */
    public function create(array $data, ?string $ruleValidate = 'default')
    {
        if ( ! is_null($ruleValidate) && ! $this->validate($this->baseValidator, $data)) {
            return false;
        }

        return $this->query->create($data);
    }

    /**
     * Validate and create new data and relations
     *
     * @param array $data
     * @param $relations
     * @param null|string $ruleValidate
     * @return bool|mixed
     */
    public function createWith(array $data, $relations, ?string $ruleValidate = 'default')
    {
        if ( ! is_null($ruleValidate) && ! $this->validate($this->baseValidator, $data)) {
            return false;
        }

        $relations = $this->getRelationForSaveAssociated($relations);
        return $this->getModel()->saveAssociated($data, $relations);
    }

    /**
     * Validate and update data
     *
     * @param array $data
     * @param $id
     * @param null|string $ruleValidate
     * @return bool|mixed
     */
    public function update(array $data, $id, ?string $ruleValidate = 'default')
    {
        $data[$this->getKeyName()] = $id;

        if ( ! is_null($ruleValidate) && ! $this->validate($this->baseValidator, $data, ['rule' => $ruleValidate])) {
            return false;
        }

        $model = $this->find($id, [$this->getKeyName()]);
        if (is_null($model)) {
            return false;
        }

        $result = $this->getModel()->saveAssociated($data, [], $model);
        $this->resetQuery();
        return $result;
    }

    /**
     * Validate, update data and relations
     *
     * @param array $data
     * @param $id
     * @param null $relations
     * @param null|string $ruleValidate
     * @return bool|mixed
     */
    public function updateWith(array $data, $id, $relations = null, ?string $ruleValidate = 'default')
    {
        $data[$this->getKeyName()] = $id;

        if ( ! is_null($ruleValidate) && ! $this->validate($this->baseValidator, $data, ['rule' => $rule])) {
            return false;
        }

        $model = $this->find($id, [$this->getKeyName()]);
        if (is_null($model)) {
            return false;
        }

        $relations = $this->getRelationForSaveAssociated($relations);

        $result = $this->getModel()->saveAssociated($data, $relations, $model);
        $this->resetQuery();
        return $result;
    }

    /**
     * Delete item
     *
     * @param int $id
     * @param string $deletedMethod
     * @return mixed
     */
    public function delete(int $id, string $deletedMethod = 'delete')
    {
        return $this->deleteBy($this->getKeyName(), $id, $deletedMethod);
    }

    /**
     * Delete items by column
     *
     * @param string $column
     * @param int $id
     * @param string $deletedMethod
     * @return mixed
     */
    public function deleteBy(string $column, int $id, string $deletedMethod = 'delete')
    {
        $result = $this->query->where($column, $id)->{$deletedMethod}();
        $this->resetQuery();
        return $result;
    }

    /**
     * Destroy item
     *
     * @param int $id
     * @param string $deletedMethod
     * @return mixed
     */
    public function destroy(int $id, string $deletedMethod = 'delete')
    {
        return $this->delete($id, $deletedMethod);
    }

    /**
     * Get first item
     *
     * @param null $columns
     * @return mixed
     */
    public function first($columns = null)
    {
        $this->fixSelectedColumns($columns);
        $result = $this->query->first($columns);
        $this->resetQuery();
        return $result;
    }

    /**
     * Get last item
     *
     * @param null $columns
     * @return mixed
     */
    public function last($columns = null)
    {
        $this->fixSelectedColumns($columns);
        $this->pushOrderBy($this->getKeyName(), 'desc');
        return $this->first($columns);
    }

    /**
     * Find item
     *
     * @param $id
     * @param null $columns
     * @return mixed
     */
    public function find($id, $columns = null)
    {
        $columns = $this->fixSelectedColumns($columns);
        $item = $this->query->find($id, $columns);
        $this->resetQuery();
        return $item;
    }

    /**
     * Find item by column
     *
     * @param $attribute
     * @param $value
     * @param null $columns
     * @return mixed
     */
    public function findBy(string $attribute, $value, $columns = null)
    {
        $this->fixSelectedColumns($columns);
        $this->pushWhere($attribute, $value);
        $item = $this->query->first($columns);
        $this->resetQuery();
        return $item;
    }

    /**
     * Find item for show page
     *
     * @param $id
     * @param array $columns
     * @return mixed
     */
    public function findForShow($id, $columns = null)
    {
        $selectedColumns = (array)$columns;
        if (is_null($columns)) {
            $selectedColumns = $this->getShowAbleColumns();
        }

        return $this->find($id, $selectedColumns);
    }

    /**
     * Find items by column
     *
     * @param string $attribute
     * @param $value
     * @param null $columns
     * @return mixed
     */
    public function findAllBy(string $attribute, $value, $columns = null)
    {
        $this->fixSelectedColumns($columns);
        $this->pushSelect($columns);
        $this->pushWhere($attribute, $value);
        $items = $this->query->get();
        $this->resetQuery();
        return $items;
    }

    /**
     * Find items in the form list
     *
     * @param array|null $listable
     * @return array|mixed
     */
    public function findList(?array $listable = null)
    {
        if (is_null($listable)) {
            $listable = $this->getListableColumns();
        }
        $list = $this->all($this->fixColumns($listable['columns']))->pluck($listable['value'],
            $listable['key'])->all();
        $this->resetQuery();
        return $list;
    }

    /**
     * Find items by column in the form list
     *
     * @param string $attribute
     * @param $value
     * @param array|null $listable
     * @return array|mixed
     */
    public function findListBy(string $attribute, $value, ?array $listable = null)
    {
        $this->pushWhere($attribute, $value);
        return $this->findList($listable);
    }

    /**
     * Find items count
     *
     * @param null|string $attribute
     * @param string $cmpOrValue
     * @param null $value
     * @return mixed
     */
    public function findCount(?string $attribute = null, $cmpOrValue = '=', $value = null)
    {
        if (is_null($value)) {
            $value = $cmpOrValue;
            $cmpOrValue = '=';
        }
        if (!empty($attribute) && !empty($value)) {
            if (is_array($value)) {
                $this->pushWhereIn($attribute, $value);
            } else {
                $this->pushWhere($attribute,$cmpOrValue, $value);
            }
            $this->pushSelect($attribute);
        }

        $count = $this->query->count();
        $this->resetQuery();
        return $count;
    }

    /**
     * Exist key
     *
     * @param $id
     * @return bool
     */
    public function exists($id)
    {
        $primaryKey = $this->getKeyName();
        return $this->existsWhere($primaryKey, $id);
    }

    /**
     * Exist key where
     *
     * @param $attribute
     * @param $value
     * @return bool
     */
    public function existsWhere($attribute, $value)
    {
        $this->pushWhere($attribute, $value);
        $result = $this->findCount() > 0;
        $this->resetQuery();
        return $result;
    }

    /**
     * Increment column
     *
     * @param string $column
     * @param int $value
     * @return mixed
     */
    public function increment(string $column, $value = 1)
    {
        return $this->query->increment($column, $value);
    }

    /**
     * Decrement column
     *
     * @param string $column
     * @param int $value
     * @return mixed
     */
    public function decrement(string $column, $value = 1)
    {
        return $this->query->decrement($column, $value);
    }

    /**
     * Condition where in query
     *
     * @param $column
     * @param string $cmpOrValue
     * @param null $value
     * @return LaraServiceModel|mixed
     */
    public function pushWhere($column, $cmpOrValue = '=', $value = null)
    {
        return $this->where($column, $cmpOrValue, $value);
    }

    /**
     * Condition orWhere in query
     *
     * @param $column
     * @param string $cmpOrValue
     * @param null $value
     * @return LaraServiceModel|mixed
     */
    public function pushOrWhere($column, $cmpOrValue = '=', $value = null)
    {
        return $this->where($column, $cmpOrValue, $value, 'orWhere');
    }

    /**
     * Condition whereNull in query
     *
     * @param string $column
     * @return LaraServiceModel|mixed
     */
    public function pushWhereNull(string $column)
    {
        return $this->query->whereNull($column);
    }

    /**
     * Condition OrderBy in query
     *
     * @param string $column
     * @param string $order
     * @return array|mixed
     */
    public function pushOrderBy(string $column, $order = 'asc')
    {
        $this->query->orderBy($column, $order);
        return $this;
    }

    /**
     * Condition limit in query
     *
     * @param int $limit
     * @return mixed
     */
    public function pushLimit(int $limit)
    {
        $this->query->limit($limit);
        return $this;
    }

    /**
     * Condition offset in query
     *
     * @param int $count
     * @return mixed
     */
    public function pushOffset(int $count)
    {
        $this->query->offset($count);
        return $this;
    }

    /**
     * Condition select in query
     *
     * @param $columns
     * @return $this
     */
    public function pushSelect($columns)
    {
        $columns = (array)$columns;
        $this->query->select($columns);
        return $this;
    }

    /**
     * Condition whereBetween in query
     *
     * @param string $column
     * @param array $values
     * @return $this
     */
    public function pushWhereBetween(string $column, array $values)
    {
        $$this->betweenAndIn($column, $values);
        return $this;
    }

    /**
     * Condition whereNotBetween in query
     *
     * @param string $column
     * @param array $values
     * @return $this
     */
    public function pushWhereNotBetween(string $column, array $values)
    {
        $this->betweenAndIn($column, $values, 'notBetween');
        return $this;
    }

    /**
     * Condition whereIn in query
     *
     * @param string $attribute
     * @param array $values
     * @return $this
     */
    public function pushWhereIn(string $column, array $values)
    {
        $this->betweenAndIn($column, $values, 'in');
        return $this;
    }

    /**
     * Condition whereNotIn in query
     *
     * @param string $column
     * @param array $values
     * @return $this
     */
    public function pushWhereNotIn(string $column, array $values)
    {
        $this->betweenAndIn($column, $values, 'notIn');
        return $this;
    }

    /**
     * Condition Search in query
     *
     * @param $attribute
     * @param null $search
     * @return $this
     */
    public function pushSearch($column, $search = null)
    {
        $searchArray = $column;
        if ( ! is_array($search)) {
            $searchArray = [$column => $search];
        }

        foreach ($searchArray as $key => $value) {
            // @TODO use full text or not??
            $this->query->orWhere($key, 'like', '%' . $value . '%');
        }
        return $this;
    }

    /**
     * Condition whereDate in query
     *
     * @param string $date
     * @param string $value
     * @return $this
     */
    public function pushWhereDate(string $date, string $value)
    {
        $this->whereDateManipulations($date, $value);
        return $this;
    }

    /**
     * Condition whereMonth in query
     *
     * @param string $date
     * @param string $value
     * @return $this
     */
    public function pushWhereMonth(string $date, string $value)
    {
        $this->whereDateManipulations($date, $value, 'month');
        return $this;
    }

    /**
     * Condition whereDay in query
     *
     * @param string $day
     * @param string $value
     * @return $this
     */
    public function pushWhereDay(string $day, string $value)
    {
        $this->whereDateManipulations($day, $value, 'day');
        return $this;
    }

    /**
     * Condition whereTime in query
     *
     * @param string $time
     * @param string $value
     * @return $this
     */
    public function pushWhereTime(string $time, string $value)
    {
        $this->whereDateManipulations($time, $value, 'time');
        return $this;
    }

    /**
     * Condition with relation in query
     *
     * @param $with
     * @return mixed
     */
    public function pushWith($with)
    {
        $with  = (array) $with;
        foreach ($with as $relation => $relOptions) {
            if (false === $relOptions) {
                continue;
            }

            if (is_numeric($relation)) {
                $relation = $relOptions;
                $relOptions = [];
            }

            $this->with($relation, $relOptions);
        }
        return $this;
    }

    /**
     * Condition withCount relation in query
     *
     * @param $with
     * @return mixed
     */
    public function pushWithCount($with)
    {
        $with  = (array) $with;
        foreach ($with as $relation => $relOptions) {
            if (false === $relOptions) {
                continue;
            }

            if (is_numeric($relation)) {
                $relation = $relOptions;
                $relOptions = [];
            }

            if ( ! isset($relOptions['pushWhere'])) {
                $relOptions = ['pushWhere' => $relOptions];
            }

            $this->with($relation, $relOptions, 'withCount');
        }
        return $this;
    }

    /**
     * Add new validation errors
     *
     * @param $errors
     */
    public function setValidationErrors($errors)
    {
        $this->validationErrors = $errors;
    }

    /**
     * Get all validation errors
     *
     * @return mixed returns the validation errors
     */
    public function getValidationErrors()
    {
        return $this->validationErrors;
    }

    /**
     * Start transaction for database
     *
     * @return mixed
     */
    public function startTransaction()
    {
        return DB::beginTransaction();
    }

    /**
     * Commit transaction for database
     *
     * @return mixed
     */
    public function commitTransaction()
    {
        return DB::commit();
    }

    /**
     * Rollback transaction for database
     *
     * @return mixed
     */
    public function rollbackTransaction()
    {
        return DB::rollBack();
    }
}

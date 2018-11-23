<?php

namespace LaraServiceModel\Traits;

use LaraSupport\Facades\LaraDB;

/**
 * Trait Functions
 * @package LaraServiceModel\Traits
 */
trait FunctionsTrait
{
    /**
     * Validation data
     *
     * @param $validator
     * @param $data
     * @param array $options
     * @return bool
     */
    protected function validate($validator, $data, $options = [])
    {
        if ($validator->isValid($data, $options)) {
            return true;
        }

        $this->setValidationErrors($validator->getErrors());
        return false;
    }

    /**
     * @param array $options
     * @param $group
     * @return bool
     */
    protected function setSortingOptions($options = [], $group = self::GROUP)
    {
        if (empty($options)) {
            $options = app('request')->request->all();
        }

        if (isset($options['column']) && isset($options['order'])) {
            if (is_null($options['column'])) {
                return true;
            }

            $column = strtolower($options['column']);

            // check if column is allowed to be sorted
            if ($this->getSortableColumns($column, $group)) {
                $order = strtolower($options['order']);
                $order = ($order === 'desc') ? $order : 'asc';
                $this->pushOrderBy($column, $order);
            }
        }

        return true;
    }

    /**
     * returns the list of sortable fields
     *
     * @param null $column
     * @param string $group
     * @return mixed
     */
    protected function getSortableColumns($column = null, $group = self::GROUP)
    {
        return $this->query->getModel()->getSortable($column, $group);
    }

    /**
     * @param array $data
     * @return array
     */
    protected function getRelationForSaveAssociated($data = [])
    {
        if(is_string($data) && trim($data)) {
            $data = [$data];
        }

        if(!empty($data)) {
            return ['associated' => $data];
        }

        return [];
    }

    /**
     * @param $column
     * @param string $cmp
     * @param null $value
     * @param string $type
     * @return $this
     */
    protected function where($column, $cmp = '=', $value = null, string $type = 'where')
    {
        $where = $column;
        if ( ! is_array($column) && ! is_callable($column)) {
            dd($column);
            if (is_null($value)) {
                $value = $cmp;
                $cmp = '=';
            }
            $where = [
                [$column, $cmp, $value]
            ];
        }

        $this->query->{$type}($where);
        return $this;
    }

    /**
     * @param $with
     * @return mixed
     */
    protected function fixRequiredRelations($with)
    {
        foreach ($this->query->getModel()->getRequiredWith() as $requiredRelations => $options) {
            if (is_numeric($requiredRelations)) {
                $requiredRelations = $options;
                $options = [];
            }
            if (in_array($requiredRelations, $with) || isset($with[$requiredRelations])) {
                // this relations already exists
                continue;
            }
            $with[$requiredRelations] = $options;
        }
        return $with;
    }

    /**
     * @param $relation
     * @param array $relOptions
     * @param string $type
     */
    protected function with($relation, $relOptions, string $type = 'with')
    {
        if (is_callable($relOptions)) {
            $this->query->{$type}([$relation => $relOptions]);
            return;
        }

        $relOptions = (array)$relOptions;
        $this->query->{$type}([$relation => function ($query) use ($relOptions) {
            foreach ($relOptions as $relOptionName => $relOption) {
                $method = $this->clearRelationOption($relOptionName);
                $option = [$relOption];

                if ($this->isMethodWhere($method)) {
                    if ( ! $this->isMultiArray($relOption)) {
                        $option = [[$relOption]];
                    }
                }

                $query->{$method}(...$option);
            }
        }]);
    }

    /**
     * Clear old query
     */
    protected function resetQuery(): void
    {
        $this->query = $this->baseModel->newQuery();
    }

    /**
     * Get model of query
     *
     * @return mixed
     */
    protected function getModel()
    {
        return $this->query->getModel();
    }

    /**
     * Get primary key
     *
     * @return mixed
     */
    protected function getKeyName()
    {
       return $this->getModel()->getKeyName();
    }

    /**
     * @param null $columns
     * @return array
     */
    protected function fixSelectedColumns($columns = null): array
    {
        if (is_null($columns)) {
            $columns = array_merge([$this->getKeyName()], $this->getFillableColumns());
        } elseif (!is_array($columns)) {
            $columns = (array) $columns;
        }

        return $columns;
    }

    /**
     * list of columns for showing on index page
     *
     * @param bool $full
     * @param bool $hidden
     * @param $group
     * @return mixed
     */
    protected function getIndexableColumns($full = false, $hidden = true, $group = self::GROUP)
    {
        return $this->getModel()->getIndexable($full, $hidden, $group);
    }

    /**
     * returns the list of fillable fields
     *
     * @return array
     */
    protected function getFillableColumns(): array
    {
        return $this->getModel()->getFillable();
    }

    /**
     * returns the list of showAble fields
     *
     * @return array
     */
    protected function getShowAbleColumns(): array
    {
        return $this->getModel()->getShowAble();
    }

    /**
     * columns used for model's find list
     *
     * @return mixed
     */
    public function getListableColumns()
    {
        return $this->getModel()->getListable();
    }

    /**
     * Remove "push" line from method names
     *
     * @param $relOptionName
     * @return string
     */
    protected function clearRelationOption($relOptionName): string
    {
        return lcfirst(ltrim($relOptionName, 'push'));
    }

    /**
     * Check whether the 'where' method is
     *
     * @param string $method
     * @return bool
     */
    protected function isMethodWhere(string $method): bool
    {
        return is_numeric(strpos(mb_strtolower($method), 'where'));
    }

    /**
     * @param array $where
     * @return bool
     */
    protected function isMultiArray(array $where): bool
    {
        return (count($where, COUNT_RECURSIVE) - count($where)) > 0;
    }

    /**
     * Universal method for whereBetween and whereIn
     *
     * @param string $column
     * @param array $values
     * @param string $type
     */
    protected function betweenAndIn(string $column, array $values, string $type = 'between')
    {
        $type = sprintf('where%s', ucfirst($type));
        $this->query->{$type}($column, $values);
    }

    /**
     * Universal method for manipulations date
     *
     * @param string $column
     * @param string $value
     * @param string $type
     */
    protected function whereDateManipulations(string $column, string $value, string $type = 'date')
    {
        $type = sprintf('where%s', ucfirst($type));
        $this->query->{$type}($column, $value);
    }

    /**
     * Checks if a given columns in the model
     *
     * @param $columns
     * @param null $table
     * @param null $prefix
     * @return array
     */
    protected function fixColumns($columns, $table = null, $prefix = null)
    {
        if (is_null($table)) {
            $table = $this->getModel();
        }

        return LaraDB::getFullColumns($columns, $table, $prefix);
    }
}

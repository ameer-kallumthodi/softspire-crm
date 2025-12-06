<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BaseModel extends Model
{
    /**
     * Insert a new record
     */
    protected function insertRecord(array $data)
    {
        return $this->create($data);
    }

    /**
     * Update a record by ID
     */
    protected function updateRecord(int $id, array $data)
    {
        return $this->where('id', $id)->update($data);
    }

    /**
     * Delete a record by ID
     */
    protected function deleteRecord(int $id)
    {
        return $this->where('id', $id)->delete();
    }

    /**
     * Find a record by ID
     */
    protected function findRecord(int $id)
    {
        return $this->find($id);
    }

    /**
     * List paginated records
     */
    protected function listPaginated(int $perPage = 20)
    {
        return $this->orderBy('id', 'desc')->paginate($perPage);
    }

    /**
     * Get all data with params
     * 
     * @param array|null $where
     * @param array|string|null $select
     * @param array|null $order_by [column, direction]
     * @param int|null $limit
     * @param string|array|null $group_by
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function get($where = null, $select = null, $order_by = null, $limit = null, $group_by = null)
    {
        $query = static::query();

        if ($select != null) {
            if (is_array($select)) {
                $query->select($select);
            } else {
                $query->selectRaw($select);
            }
        }

        if ($where != null) {
            if (isset($where['OR'])) {
                $orConditions = $where['OR'];
                unset($where['OR']);

                // Apply normal conditions first
                foreach ($where as $column => $value) {
                    if (is_array($value) && count($value) == 2 && $value[0] == 'like') {
                        $query->where($column, 'like', $value[1]);
                    } elseif (is_array($value)) {
                        $query->whereIn($column, $value);
                    } else {
                        $query->where($column, $value);
                    }
                }

                // Add OR conditions
                $query->where(function($q) use ($orConditions) {
                    foreach ($orConditions as $column => $value) {
                        if (is_array($value) && count($value) == 2 && $value[0] == 'like') {
                            $q->orWhere($column, 'like', $value[1]);
                        } elseif (is_array($value)) {
                            $q->orWhereIn($column, $value);
                        } else {
                            $q->orWhere($column, $value);
                        }
                    }
                });
            } else {
                // Normal conditions
                foreach ($where as $column => $value) {
                    if (is_array($value) && count($value) == 3 && $value[0] == 'operator') {
                        $query->where($column, $value[1], $value[2]);
                    } elseif (is_array($value) && count($value) == 2 && $value[0] == 'like') {
                        $query->where($column, 'like', $value[1]);
                    } elseif (is_array($value)) {
                        $query->whereIn($column, $value);
                    } else {
                        $query->where($column, $value);
                    }
                }
            }
        }

        // Global soft delete condition - handled automatically by SoftDeletes trait
        // No need to manually check as Laravel handles it

        if ($order_by != null && is_array($order_by) && count($order_by) >= 2) {
            $query->orderBy($order_by[0], $order_by[1]);
        }

        if ($group_by != null) {
            if (is_array($group_by)) {
                $query->groupBy($group_by);
            } else {
                $query->groupBy($group_by);
            }
        }

        if ($limit != null) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Build query with params (returns query builder for chaining)
     * 
     * @param array|null $where
     * @param array|string|null $select
     * @param array|null $order_by [column, direction]
     * @param string|array|null $group_by
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function buildQuery($where = null, $select = null, $order_by = null, $group_by = null)
    {
        $query = static::query();

        if ($select != null) {
            if (is_array($select)) {
                $query->select($select);
            } else {
                $query->selectRaw($select);
            }
        }

        if ($where != null) {
            if (isset($where['OR'])) {
                $orConditions = $where['OR'];
                unset($where['OR']);

                // Apply normal conditions first
                foreach ($where as $column => $value) {
                    if (is_array($value) && count($value) == 3 && $value[0] == 'operator') {
                        $query->where($column, $value[1], $value[2]);
                    } elseif (is_array($value) && count($value) == 2 && $value[0] == 'like') {
                        $query->where($column, 'like', $value[1]);
                    } elseif (is_array($value)) {
                        $query->whereIn($column, $value);
                    } else {
                        $query->where($column, $value);
                    }
                }

                // Add OR conditions
                $query->where(function($q) use ($orConditions) {
                    foreach ($orConditions as $column => $value) {
                        if (is_array($value) && count($value) == 3 && $value[0] == 'operator') {
                            $q->orWhere($column, $value[1], $value[2]);
                        } elseif (is_array($value) && count($value) == 2 && $value[0] == 'like') {
                            $q->orWhere($column, 'like', $value[1]);
                        } elseif (is_array($value)) {
                            $q->orWhereIn($column, $value);
                        } else {
                            $q->orWhere($column, $value);
                        }
                    }
                });
            } else {
                // Normal conditions
                foreach ($where as $column => $value) {
                    if (is_array($value) && count($value) == 3 && $value[0] == 'operator') {
                        $query->where($column, $value[1], $value[2]);
                    } elseif (is_array($value) && count($value) == 2 && $value[0] == 'like') {
                        $query->where($column, 'like', $value[1]);
                    } elseif (is_array($value)) {
                        $query->whereIn($column, $value);
                    } else {
                        $query->where($column, $value);
                    }
                }
            }
        }

        if ($order_by != null && is_array($order_by) && count($order_by) >= 2) {
            $query->orderBy($order_by[0], $order_by[1]);
        }

        if ($group_by != null) {
            if (is_array($group_by)) {
                $query->groupBy($group_by);
            } else {
                $query->groupBy($group_by);
            }
        }

        return $query;
    }

    /**
     * Get paginated data with params
     * 
     * @param array|null $where
     * @param array|string|null $select
     * @param array|null $order_by [column, direction]
     * @param int $perPage
     * @param string|array|null $group_by
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public static function paginate($where = null, $select = null, $order_by = null, $perPage = 20, $group_by = null)
    {
        return static::buildQuery($where, $select, $order_by, $group_by)->paginate($perPage);
    }

    /**
     * Get data with joins
     * 
     * @param array $joins [[table, condition, type], ...]
     * @param array $where
     * @param array|string $select
     * @param array|null $order_by ['key' => column, 'direction' => asc/desc]
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getJoin($joins, $where = [], $select = [], $order_by = null)
    {
        $query = static::query();

        if ($select != null && !empty($select)) {
            if (is_array($select)) {
                $query->select($select);
            } else {
                $query->selectRaw($select);
            }
        }

        foreach ($joins as $join) {
            $table = $join[0];
            $condition = $join[1];
            $type = $join[2] ?? 'left';

            switch (strtolower($type)) {
                case 'inner':
                    $query->join($table, $condition);
                    break;
                case 'left':
                    $query->leftJoin($table, $condition);
                    break;
                case 'right':
                    $query->rightJoin($table, $condition);
                    break;
                default:
                    $query->leftJoin($table, $condition);
            }
        }

        if ($where != null && !empty($where)) {
            if (isset($where['OR'])) {
                $orConditions = $where['OR'];
                unset($where['OR']);

                // Apply normal conditions first
                foreach ($where as $column => $value) {
                    if (is_array($value) && count($value) == 3 && $value[0] == 'operator') {
                        $query->where($column, $value[1], $value[2]);
                    } elseif (is_array($value) && count($value) == 2 && $value[0] == 'like') {
                        $query->where($column, 'like', $value[1]);
                    } elseif (is_array($value)) {
                        $query->whereIn($column, $value);
                    } else {
                        $query->where($column, $value);
                    }
                }

                // Add OR conditions
                $query->where(function($q) use ($orConditions) {
                    foreach ($orConditions as $column => $value) {
                        if (is_array($value) && count($value) == 3 && $value[0] == 'operator') {
                            $q->orWhere($column, $value[1], $value[2]);
                        } elseif (is_array($value) && count($value) == 2 && $value[0] == 'like') {
                            $q->orWhere($column, 'like', $value[1]);
                        } elseif (is_array($value)) {
                            $q->orWhereIn($column, $value);
                        } else {
                            $q->orWhere($column, $value);
                        }
                    }
                });
            } else {
                // Normal conditions
                foreach ($where as $column => $value) {
                    if (is_array($value) && count($value) == 3 && $value[0] == 'operator') {
                        $query->where($column, $value[1], $value[2]);
                    } elseif (is_array($value) && count($value) == 2 && $value[0] == 'like') {
                        $query->where($column, 'like', $value[1]);
                    } elseif (is_array($value)) {
                        $query->whereIn($column, $value);
                    } else {
                        $query->where($column, $value);
                    }
                }
            }
        }

        // Global soft delete condition - handled automatically by SoftDeletes trait

        if ($order_by != null && is_array($order_by) && isset($order_by['key'])) {
            $direction = $order_by['direction'] ?? 'asc';
            $query->orderBy($order_by['key'], $direction);
        }

        return $query->get();
    }
}


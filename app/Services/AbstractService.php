<?php

namespace App\Services;

use Schema;
use Carbon\Carbon;

abstract class AbstractService
{
    /**
     * The model to be used for this service.
     *
     * @var \App\Models\Model
     */
    protected $model;

    /**
     * Show the resource with all its relations
     *
     * @var bool
     */
    protected $showWithRelations = false;


    /**
     * Default pagination to use for item listings
     *
     * @var bool
     */
    protected $pagination = 20;


    /**
     * Default ordering
     *
     * @var bool
     */
    protected $ranking = 'DESC';

    /**
     * Get a listing of resource matching with query params appplied
     *
     * @param array $data Data coming in from request
     */
    public function all($data = [])
    {
        $params = $this->getQueryParams($data);

        if ($this->showWithRelations) {
            $relations = $this->model()->getRelations();
        } else {
            $relations = [];
        }

        if (count($relations)) {
            $query = $this->model()->with($relations);
        } else {
            $query = $this->model();
        }

        // Deal with keyword searches
        if (isset($data['keyword'])) {
            $query = $this->search($query, $data['keyword']);
        }

        if (isset($params['where'])) {
            $where = $params['where'];

            foreach ($where as $field => $value) {
                $query = $query->where($field, $value);
            }
        }

        // Pass query through functions for custom filtering
        if (isset($data['filter'])) {
            $func = $this->customFilters[$data['filter']];
            $query = $this->$func($query);
        }

        if (isset($params['order'])) {
            $order = $params['order'];

            foreach ($order as $field => $ranking) {
                if (Schema::hasColumn($this->model()->getTable(), $field)) {
                    $query = $query->orderBy($this->model()->getTable() . '.' . $field, $ranking);
                } else {
                    // For custom fields, we expect a custom order by function defined for it
                    $sortFunction = 'sortBy' . join(array_map('ucfirst', explode('_', $field)));

                    if (method_exists($this->model(), $sortFunction)) {
                        $query = $this->model()->$sortFunction($query, $ranking);
                    }
                }
            }
        }

        if (isset($params['paginate'])) {
            $results = $query->paginate($params['paginate']);
        } else {
            $results = $query->get();
        }

        return $results;
    }

    /**
     * Store a new resource with the provided data.
     *
     * @param array $data
     * @return \App\Models\Model|null
     */
    public function store($data = [])
    {
        $data = $this->getPreparedSaveData($data);

        if (count($data) < 1) {
            return null;
        }

        $resource = $this->model()->fill($data);
        $resource->save();

        return $resource;
    }

    /**
     * Show the specified resource. Load it with or without its relations
     * depending on the value of the showWithRelations variable.
     *
     * @param int $id
     * @return \App\Models\Model|null
     */
    public function show($id)
    {
        $resource = $this->find($id);

        if (!$resource) {
            return null;
        }

        return $this->showWithRelations() ? $resource->load($this->model()->getRelations()) : $resource;
    }

    /**
     * Update the specified resource with the specified data.
     * Returns null if the resource was not found or the data is not valid.
     *
     * @param int $id
     * @param array $data
     * @return \App\Models\Model|null
     */
    public function update($id, $data = [])
    {
        $resource = $this->find($id);
        $data = $this->getPreparedUpdateData($data, $resource);

        if (!$resource) {
            return null;
        }

        if (count($data)) {
            $resource->update($data);
        }

        return $resource;
    }

    /**
     * Delete the resource with the specified id.
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $resource = $this->find($id);

        if ($resource && $resource->delete()) {
            return true;
        }

        return false;
    }

    /**
     * Restore a deleted resource
     *
     * @param int $id Id of resource
     * @return mixed Resource
     */
    public function restore($id)
    {
        $resource = $this->find($id, 'id', true);

        if ($resource && $resource->restore()) {
            $resource->load($this->model()->getRelations());
            return $resource;
        }

        return null;
    }

    /**
     * Find a resource in the model using the specified
     * value and column for defining the constraints.
     *
     * @param mixed $value
     * @param string $column
     * @return \App\Models\Model|null
     */
    public function find($value, $column = 'id', $withTrashed = false)
    {
        if ($this->tableHasColumn($column)) {
            $query = $this->model();

            if ($withTrashed) {
                $query = $query->withTrashed();
            }

            return $query->where($column, $value)->first();
        }

        return null;
    }

    /**
     * Get a new instance of the model used by this service.
     *
     * @return \App\Models\Model|null
     */
    public function model()
    {
        return $this->model ? new $this->model : null;
    }

    /**
     * Get the valid data fields from the specified data array.
     * Do this by checking if the field exists in the table.
     *
     * @param array $data
     * @return array
     */
    protected function getValidData($data = [])
    {
        $validData = [];

        if (count($data)) {
            foreach ($data as $key => $value) {
                if ($this->tableHasColumn($key)) {
                    $validData[$key] = $value;
                }
            }
        }

        return $validData;
    }

    /**
     * Is the service set to load resources with their relations?
     *
     * @return bool
     */
    protected function showWithRelations()
    {
        return $this->showWithRelations;
    }

    /**
     * Get the ranking to be used when ordering resources.
     * Either ascending or descending order.
     *      *
     * @return string
     */
    protected function getRanking($ranking)
    {
        return strtolower($ranking) === 'desc' ? 'desc' : 'asc';
    }

    /**
     * Check if the resources should be paginated.
     *
     * @param bool|string $paginate
     * @return bool
     */
    protected function shouldPaginate($paginate = false)
    {
        return (is_string($paginate) && strtolower($paginate) === 'false') || !$paginate
                ? false : true;
    }

    /**
     * Get the per page value from the specified value.
     *
     * @param int|string $per_page
     * @return int
     */
    protected function getPerPage($per_page = 0)
    {
        $per_page = intval($per_page);

        return is_int($per_page) ? $per_page : 20;
    }

    /**
     * Check if the model has the specified column in its
     * list of columns (fields).
     *
     * @param string $field
     * @return bool
     */
    protected function tableHasColumn($column)
    {
        $table = $this->model()->getTable();

        return $column && Schema::hasColumn($table, $column);
    }

    /**
     * Get the final data that should be used in creating a new resource
     *
     * @param array $data The initial data from request
     */
    public function getPreparedSaveData($data)
    {
        return $this->getValidData($data);
    }

    /**
     * Get the final data that should be used in updating a resource
     *
     * @param array $data The initial data from request
     * @param array $resource The resource been updated
     */
    public function getPreparedUpdateData($data, $resource)
    {
        return $this->getValidData($data);
    }

    /**
     * Get base params for query purpose
     *
     * @param array $requestData Data from request
     * @return array $params Query params
     */
    protected function getQueryParams($requestData)
    {
        $params = [];
        $fields = Schema::getColumnListing($this->model()->getTable());

        // Set pagination options
        if (isset($requestData['paginate']) && ($requestData['paginate'] === true || $requestData['paginate'] === 'true')) {
            $pagination = (isset($requestData['per_page']) && $requestData['per_page']) ?
                $requestData['per_page'] : $this->pagination;
            $params['paginate'] = $pagination;
        }

        // Set ordering options
        if (isset($requestData['order_field']) && $requestData['order_field']) {
            $orderField = $requestData['order_field'];
            $ranking = (isset($requestData['ranking_order']) && $requestData['ranking_order']) ?
                $requestData['ranking_order'] : $this->ranking;

            $params['order'] = [$orderField => $ranking];
        }

        // Add default ordering fields
        $defaultOrderings = ['name' => 'ASC', 'created_at' => 'DESC'];

        foreach ($defaultOrderings as $field => $rank) {
            if (!isset($params['order'][$field])) {
                $params['order'][$field] = $rank;
            }
        }

        $params['where'] = [];

        // Set field-value pairs for use in an ANDed query for items
        foreach ($fields as $field) {
            if (isset($requestData[$field]) && $requestData[$field]) {
                $params['where'][$field] = $requestData[$field];
            }
        }

        // Instantiate extra filters option
        $params['filters'] = [];

        return $params;
    }

    /**
     * A filter for querying name with a search
     *
     * @param \Illuminate\Database\QueryBuilder $query Current query builder
     * @param $data Data from request
     * @return \Illuminate\Database\QueryBuilder $query Updated query
     */
    public function search($query, $keyword)
    {
        // Truncate contiguous spaces to only a single space for
        // explode to work desirably
        $keyword = preg_replace('/\s+/', ' ', trim($keyword));
        $searchFields = $this->model()->getSearchFields();

        $query = $query->where(function($query) use ($searchFields, $keyword) {
            $useOperator = false;

            foreach ($searchFields as $field) {
                if ($useOperator) {
                    $query->orWhere($field, 'LIKE', '%'.$keyword.'%');
                } else {
                    $query->where($field, 'LIKE', '%'.$keyword.'%');
                    $useOperator = true;
                }
            }
        });

        return $query;
    }

    /**
     * Filter resources by those created within given date range
     *
     * @param \Illuminate\Database\Query\Builder $query The current built query
     * @param string $start Start date to query from
     * @param string $end End date to query from
     * @return \Illuminate\Database\Query\Builder $query The updated query
     */
    public function filterByDate($query, $start, $end)
    {
        if ($start) {
            $start = Carbon::parse($start)->toDateString() . ' 00:00:00';
        } else {
            $start = null;
        }

        if ($end) {
            $end = Carbon::parse($end)->toDateString() . ' 23:59:00';
        } else {
            $end = null;
        }

        if ($start) {
            $query = $query->where('created_at', '>=', $start);
        }

        if ($end) {
            $query = $query->where('created_at', '<=', $end);
        }

        return $query;
    }

    /**
     * Do further querying on the current query object
     * Will be overriden by service classes with more complicated filtering requirements
     *
     * @param \Illuminate\Database\QueryBuilder $query
     * @param array $data Data for filtering query
     */
    public function applyFilters($query, $data)
    {
        return $query;
    }
}

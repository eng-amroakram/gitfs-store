<?php

namespace App\Services;

use App\Models\Supplier;

class SupplierService
{
    public $model = Supplier::class;

    public function __construct()
    {
        $this->model = new Supplier();
    }

    public function model($id)
    {
        return Supplier::find($id);
    }

    public function all($columns = ['*'])
    {
        return Supplier::all($columns);
    }

    public function data($filters, $sort_field, $sort_direction, $paginate = 10)
    {
        return Supplier::data()
            ->filters($filters)
            ->reorder($sort_field, $sort_direction)
            ->paginate($paginate);
    }

    public function changeAccountStatus($id)
    {
        return Supplier::changeAccountStatus($id);
    }

    public function delete($id)
    {
        return Supplier::deleteModel($id);
    }

    public function store($data)
    {
        return Supplier::store($data);
    }

    public function update($data, $id)
    {
        return Supplier::updateModel($data, $id);
    }
}

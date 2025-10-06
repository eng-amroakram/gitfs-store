<?php

namespace App\Services;

use App\Models\Customer;

class CustomerService
{
    public $model = Customer::class;

    public function __construct()
    {
        $this->model = new Customer();
    }

    public function model($id)
    {
        return Customer::find($id);
    }

    public function all($columns = ['*'])
    {
        return Customer::all($columns);
    }

    public function data($filters, $sort_field, $sort_direction, $paginate = 10)
    {
        return Customer::data()
            ->filters($filters)
            ->reorder($sort_field, $sort_direction)
            ->paginate($paginate);
    }

    public function changeAccountStatus($id)
    {
        return Customer::changeAccountStatus($id);
    }

    public function delete($id)
    {
        return Customer::deleteModel($id);
    }

    public function store($data)
    {
        return Customer::store($data);
    }

    public function update($data, $id)
    {
        return Customer::updateModel($data, $id);
    }
}

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
        return Customer::select($columns)->get();
    }

    public function allUnsynced($columns = ['*'])
    {
        // i need to sync only unsynced customers
        return Customer::whereNull('synced_at')->select($columns)->get();
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

    public function confirmSync($ids)
    {
        return Customer::whereIn('id', $ids)->update(['synced_at' => now()]);
    }
}

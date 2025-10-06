<?php

namespace App\Livewire\Panel\Users;

use App\Helpers\LivewireHelper;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use Livewire\Component;

class UsersList extends Component
{
    use WithPagination;
    use LivewireHelper;

    protected $paginationTheme = 'bootstrap';

    public $currentPage = 1;
    public $pagination = 10;
    public $sort_field = 'id';
    public $sort_direction = 'asc';

    public $selectedUsers = [];
    public $selectAll = false;

    public $search = "";
    public $status = "";
    public $filters = [];

    public function updatedSelectAll($value)
    {
        if ($value) {
            // حدد كل العناصر في الصفحة الحالية فقط
            $this->selectedUsers = $this->getCurrentPageUsersIds();
        } else {
            $this->selectedUsers = [];
        }
    }

    public function loadUsers()
    {
        $this->filters = [
            'search' => $this->search,
            'status' => $this->status,
        ];

        $service = $this->setService('UserService');
        return $service->data($this->filters, $this->sort_field, $this->sort_direction, $this->pagination);
    }

    public function getCurrentPageUsersIds()
    {
        $users = $this->loadUsers();
        return $users->slice(($this->currentPage - 1) * $this->pagination, $this->pagination)->pluck('id')->toArray();
    }

    #[Layout('layouts.admin.panel'), Title('المستخدمين')]
    public function render()
    {
        $users = $this->loadUsers();
        return view('livewire.panel.users.users-list', compact('users'));
    }

    public function create()
    {
        return redirect()->route('admin.panel.users.create');
    }

    public function edit($id)
    {
        session(['user_id' => $id]);
        return redirect()->route('admin.panel.users.edit');
    }

    public function managePermissions($id)
    {
        session(['user_id' => $id]);
        return redirect()->route('admin.panel.users.manage-permissions');
    }
}

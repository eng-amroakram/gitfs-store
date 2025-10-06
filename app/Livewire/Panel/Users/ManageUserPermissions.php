<?php

namespace App\Livewire\Panel\Users;

use App\Helpers\LivewireHelper;
use Livewire\Component;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ManageUserPermissions extends Component
{
    use LivewireHelper;

    public $userId;
    public $user;

    public $guard_name = 'web'; // guard web فقط
    public $roles = [];
    public $permissionsByGroup = [];
    public $selectedRoles = [];
    public $selectedPermissions = [];

    public function mount()
    {
        $this->userId = session('user_id');
        $this->user = User::findOrFail($this->userId);

        // كل الأدوار من الحارس web فقط
        $this->roles = Role::where('guard_name', 'web')->get();

        // الصلاحيات مجمعة حسب الحارس web فقط
        $this->permissionsByGroup = Permission::where('guard_name', 'web')->get()->groupBy('group')->toArray();

        // الأدوار الحالية للمستخدم
        $this->selectedRoles = $this->user->roles->pluck('name')->toArray();

        // الصلاحيات الحالية للمستخدم
        $this->selectedPermissions = $this->user->permissions->pluck('name')->toArray();
    }

    public function update()
    {
        $data = $this->validate([
            'selectedRoles' => 'array',
            'selectedPermissions' => 'array',
        ]);

        // تنظيف كل الأدوار والصلاحيات القديمة
        $this->user->roles()->detach();
        $this->user->permissions()->detach();

        // تعيين الأدوار من الحارس web فقط
        foreach ($data['selectedRoles'] as $roleName) {
            $role = Role::where('name', $roleName)->where('guard_name', 'web')->first();
            if ($role) {
                $this->user->assignRole($role);
            }
        }

        // تعيين الصلاحيات من الحارس web فقط
        foreach ($data['selectedPermissions'] as $permName) {
            $perm = Permission::where('name', $permName)->where('guard_name', 'web')->first();
            if ($perm) {
                $this->user->givePermissionTo($perm);
            }
        }

        $this->alertMessage(__('Roles & Permissions updated successfully.'), 'success');
    }

    #[Layout('layouts.admin.panel'), Title('المستخدمين')]
    public function render()
    {
        return view('livewire.panel.users.manage-user-permissions', [
            'roles' => $this->roles,
            'permissionsByGroup' => $this->permissionsByGroup,
        ]);
    }
}

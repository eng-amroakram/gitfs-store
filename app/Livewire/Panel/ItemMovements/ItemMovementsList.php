<?php

namespace App\Livewire\Panel\ItemMovements;

use App\Helpers\LivewireHelper;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use Livewire\Component;

class ItemMovementsList extends Component
{
    use WithPagination;
    use LivewireHelper;

    protected $paginationTheme = 'bootstrap';

    public $currentPage = 1;
    public $pagination = 10;
    public $sort_field = 'id';
    public $sort_direction = 'asc';

    public $selectedItemMovements = [];
    public $selectAll = false;

    public $search = "";
    public $status = "";
    public $reason = "";
    public $movement_type = "";
    public $filters = [];

    public function updatedSelectAll($value)
    {
        if ($value) {
            // حدد كل العناصر في الصفحة الحالية فقط
            $this->selectedItemMovements = $this->getCurrentPageItemMovementsIds();
        } else {
            $this->selectedItemMovements = [];
        }
    }

    public function loadItemMovements()
    {
        $this->filters = [
            'search' => $this->search,
            'status' => $this->status,
            'reason' => $this->reason,
            'movement_type' => $this->movement_type,
        ];

        $service = $this->setService('ItemMovementService');
        return $service->data($this->filters, $this->sort_field, $this->sort_direction, $this->pagination);
    }

    public function getCurrentPageItemMovementsIds()
    {
        $item_movements = $this->loadItemMovements();
        return $item_movements->slice(($this->currentPage - 1) * $this->pagination, $this->pagination)->pluck('id')->toArray();
    }

    #[Layout('layouts.admin.panel'), Title('Item Movements List')]
    public function render()
    {
        $item_movements = $this->loadItemMovements();
        return view('livewire.panel.item-movements.item-movements-list', compact('item_movements'));
    }

    public function create()
    {
        return redirect()->route('admin.panel.item-movements.create');
    }

    public function edit($id)
    {
        session(['item_movement_id' => $id]);
        return redirect()->route('admin.panel.item-movements.edit');
    }

    public function show($id)
    {
        session(['item_movement_id' => $id]);
        return redirect()->route('admin.panel.item-movements.show');
    }

}

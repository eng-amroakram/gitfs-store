<?php

namespace App\Livewire\Panel\Items;

use App\Helpers\LivewireHelper;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class ItemsList extends Component
{
    use WithPagination;
    use LivewireHelper;

    protected $paginationTheme = 'bootstrap';

    public $currentPage = 1;
    public $pagination = 10;
    public $sort_field = 'id';
    public $sort_direction = 'asc';

    public $selectedItems = [];
    public $selectAll = false;

    public $search = "";
    public $status = "";
    public $type = "";
    public $filters = [];

    public function updatedSelectAll($value)
    {
        if ($value) {
            // حدد كل العناصر في الصفحة الحالية فقط
            $this->selectedItems = $this->getCurrentPageItemsIds();
        } else {
            $this->selectedItems = [];
        }
    }

    public function loadItems()
    {
        $this->filters = [
            'search' => $this->search,
            'status' => $this->status,
            'type'   => $this->type
        ];

        $service = $this->setService('ItemService');
        return $service->data($this->filters, $this->sort_field, $this->sort_direction, $this->pagination);
    }

    public function getCurrentPageItemsIds()
    {
        $items = $this->loadItems();
        return $items->slice(($this->currentPage - 1) * $this->pagination, $this->pagination)->pluck('id')->toArray();
    }

    #[Layout('layouts.admin.panel'), Title('Items List')]
    public function render()
    {
        $items = $this->loadItems();
        return view('livewire.panel.items.items-list', compact('items'));
    }

    public function create()
    {
        return redirect()->route('admin.panel.items.create');
    }

    public function edit($id)
    {
        session(['item_id' => $id]);
        return redirect()->route('admin.panel.items.edit');
    }

    public function show($id)
    {
        session(['item_id' => $id]);
        return redirect()->route('admin.panel.items.show');
    }
}

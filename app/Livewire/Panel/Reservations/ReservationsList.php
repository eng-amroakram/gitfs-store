<?php

namespace App\Livewire\Panel\Reservations;

use App\Helpers\LivewireHelper;
use App\Models\Reservation;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class ReservationsList extends Component
{
    use WithPagination;
    use LivewireHelper;

    protected $paginationTheme = 'bootstrap';

    public $currentPage = 1;
    public $pagination = 10;
    public $sort_field = 'id';
    public $sort_direction = 'asc';

    public $selectedReservations = [];
    public $selectAll = false;

    public $search = "";
    public $status = "";
    public $filters = [];

    public $deleteId = null;

    public function updatedSelectAll($value)
    {
        if ($value) {
            // حدد كل العناصر في الصفحة الحالية فقط
            $this->selectedReservations = $this->getCurrentPageReservationsIds();
        } else {
            $this->selectedReservations = [];
        }
    }

    public function loadReservations()
    {
        $this->filters = [
            'search' => $this->search,
            'status' => $this->status,
        ];

        $service = $this->setService('ReservationService');
        return $service->data($this->filters, $this->sort_field, $this->sort_direction, $this->pagination);
    }

    public function getCurrentPageReservationsIds()
    {
        $reservations = $this->loadReservations();
        return $reservations->slice(($this->currentPage - 1) * $this->pagination, $this->pagination)->pluck('id')->toArray();
    }

    #[Layout('layouts.admin.panel'), Title('الحجوزات')]
    public function render()
    {
        $reservations = $this->loadReservations();
        return view('livewire.panel.reservations.reservations-list', compact('reservations'));
    }

    public function create()
    {
        return redirect()->route('admin.panel.reservations.create');
    }

    public function edit($id)
    {
        session(['reservation_id' => $id]);
        return redirect()->route('admin.panel.reservations.edit');
    }

    public function show($id)
    {
        session(['reservation_id' => $id]);
        return redirect()->route('admin.panel.reservations.show');
    }

    public function confirmDelete(Reservation $reservation)
    {
        $this->deleteId = $reservation->id;
        $this->alertConfirm('هل أنت متأكد أنك تريد حذف الحجز', 'delete');
    }

    public function delete()
    {
        try {
            $service = $this->setService('ReservationService');
            $reservation = $service->delete($this->deleteId);

            if ($reservation) {
                $this->alertMessage(__('Reservation deleted successfully.'), 'success');
            }
        } catch (ValidationException $e) {
            $this->setErrorBag($e->validator->getMessageBag());
            return false;
        } catch (\Exception $e) {
            $this->alertMessage(__('An error occurred while creating the sale: ') . $e->getMessage(), 'error');
            return false;
        }
    }
}

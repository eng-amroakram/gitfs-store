<?php

namespace App\Livewire\Panel\Reservations;

use App\Helpers\LivewireHelper;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class ShowReservation extends Component
{
    use LivewireHelper;

    public $reservation_id;
    public $reservation;

    public function mount()
    {
        $id = session('reservation_id');

        if (!$id) {
            return redirect()->route('admin.panel.reservations.list');
        }

        $this->reservation_id = $id;
        $service =  $this->setService('ReservationService');
        $this->reservation = $service->model($this->reservation_id);
    }

    #[Layout('layouts.admin.panel'), Title('Show Reservation')]
    public function render()
    {
        return view('livewire.panel.reservations.show-reservation', ['reservation' => $this->reservation]);
    }
}

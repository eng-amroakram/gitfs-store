<?php

namespace App\Livewire\Panel;

use App\Models\Customer;
use App\Models\Item;
use App\Models\ItemMovement;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\Sale;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Dashboard extends Component
{
    public $usersCount;
    public $itemsCount;
    public $reservationsCount;
    public $salesCount;
    public $totalSalesAmount;
    public $totalPaymentsAmount;
    public $totalCustomersSaleBalance;
    public $totalCustomersReservationBalance;
    public $totalCustomersTotalBalance;
    public $totalReservationsAmount;
    public $totalItemsQuantity;
    public $totalItemsAvailableQuantity;
    public $totalItemsReservedQuantity;
    public $totalItemsQuantityValue;
    public $totalItemsAvailableQuantityValue;
    public $totalItemsReservedQuantityValue;
    public $recentUsers;
    public $recentReservations;
    public $recentSales;
    public $recentItems;
    public $recentPayments;
    public $recentCustomers;
    public $topSellingItems;
    public $topCustomersBySales;
    // public $topCustomersByPayments;
    public $topCustomersBySaleBalance;
    public $topCustomersByReservationBalance;
    public $topCustomersByTotalBalance;

    public function mount()
    {
        $this->usersCount = User::count();
        $this->itemsCount = Item::count();
        $this->reservationsCount = Reservation::count();
        $this->salesCount = Sale::count();
        $this->totalSalesAmount = Sale::sum('grand_total');
        $this->totalPaymentsAmount = Payment::sum('amount');
        $this->totalCustomersSaleBalance = Customer::sum('sale_balance');
        $this->totalCustomersReservationBalance = Customer::sum('reservation_balance');
        $this->totalCustomersTotalBalance = Customer::sum('total_balance');
        $this->totalReservationsAmount = Reservation::sum('total');
        $this->totalItemsQuantity = Item::sum('quantity_total');
        $this->totalItemsAvailableQuantity = Item::sum('available_quantity');
        $this->totalItemsReservedQuantity = Item::sum('reserved_quantity');
        $this->totalItemsQuantityValue = Item::sum('quantity_total');
        $this->totalItemsAvailableQuantityValue = Item::sum('available_quantity');
        $this->totalItemsReservedQuantityValue = Item::sum('reserved_quantity');
        $this->recentUsers = User::latest()->take(5)->get();
        $this->recentReservations = Reservation::latest()->take(5)->get();
        $this->recentSales = Sale::latest()->take(5)->get();
        $this->recentItems = Item::latest()->take(5)->get();
        $this->recentPayments = Payment::latest()->take(5)->get();
        $this->recentCustomers = Customer::latest()->take(5)->get();
        $this->topSellingItems = Item::withSum('saleItems', 'quantity')
            ->orderByDesc('sale_items_sum_quantity')
            ->take(5)
            ->get();
        $this->topCustomersBySales = Customer::withSum('sales', 'grand_total')
            ->orderByDesc('sales_sum_grand_total')
            ->take(5)
            ->get();
        // $this->topCustomersByPayments = Customer::withSum('payments', 'amount')
        //     ->orderByDesc('payments_sum_amount')
        //     ->take(5)
        //     ->get();
        $this->topCustomersBySaleBalance = Customer::orderByDesc('sale_balance')
            ->take(5)
            ->get();
        $this->topCustomersByReservationBalance = Customer::orderByDesc('reservation_balance')
            ->take(5)
            ->get();
        $this->topCustomersByTotalBalance = Customer::orderByDesc('total_balance')
            ->take(5)
            ->get();
    }

    #[Layout('layouts.admin.panel'), Title('لوحة التحكم')]
    public function render()
    {
        return view('livewire.panel.dashboard', [
            'usersCount' => $this->usersCount,
            'itemsCount' => $this->itemsCount,
            'reservationsCount' => $this->reservationsCount,
            'salesCount' => $this->salesCount,
            'totalSalesAmount' => $this->totalSalesAmount,
            'totalPaymentsAmount' => $this->totalPaymentsAmount,
            'totalCustomersSaleBalance' => $this->totalCustomersSaleBalance,
            'totalCustomersReservationBalance' => $this->totalCustomersReservationBalance,
            'totalCustomersTotalBalance' => $this->totalCustomersTotalBalance,
            'totalReservationsAmount' => $this->totalReservationsAmount,
            'totalItemsQuantity' => $this->totalItemsQuantity,
            'totalItemsAvailableQuantity' => $this->totalItemsAvailableQuantity,
            'totalItemsReservedQuantity' => $this->totalItemsReservedQuantity,
            'totalItemsQuantityValue' => $this->totalItemsQuantityValue,
            'totalItemsAvailableQuantityValue' => $this->totalItemsAvailableQuantityValue,
            'totalItemsReservedQuantityValue' => $this->totalItemsReservedQuantityValue,
            'recentUsers' => $this->recentUsers,
            'recentReservations' => $this->recentReservations,
            'recentSales' => $this->recentSales,
            'recentItems' => $this->recentItems,
            'recentPayments' => $this->recentPayments,
            'recentCustomers' => $this->recentCustomers,
            'topSellingItems' => $this->topSellingItems,
            'topCustomersBySales' => $this->topCustomersBySales,
            // 'topCustomersByPayments' => $this->topCustomersByPayments,
            'topCustomersBySaleBalance' => $this->topCustomersBySaleBalance,
            'topCustomersByReservationBalance' => $this->topCustomersByReservationBalance,
            'topCustomersByTotalBalance' => $this->topCustomersByTotalBalance,
        ]);
    }
}

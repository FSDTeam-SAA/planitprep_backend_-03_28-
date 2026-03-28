@extends('admin.layouts.admin')
@section('content')

    <div class="pc-container">
        <div class="pc-content">
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="javascript: void(0)">Dashboard</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body pb-1">
                            <div class="row">
                                <div class="col-9">
                                    <h6 class="mb-2 f-w-600 text-muted">Total Users</h6>
                                    <h4 class="mb-3">{{ $totalUsers }}</h4>
                                </div>
                                <div class="col-3 text-end text-green-400 f-36">
                                    <div class="t-order-icon bg-color1 color0"><i class="fa fa-users"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body pb-1">
                            <div class="row">
                                <div class="col-9">
                                    <h6 class="mb-2 f-w-600 text-muted">Total Packages</h6>
                                    <h4 class="mb-3">{{ $totalPackages }}</h4>
                                </div>
                                <div class="col-3 text-end text-orange-400 f-36">
                                    <div class="t-order-icon bg-color1 color0"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body pb-1">
                            <div class="row">
                                <div class="col-9">
                                    <h6 class="mb-2 f-w-600 text-muted">Active Memberships</h6>
                                    <h4 class="mb-3">{{ $activeMemberships }}</h4>
                                </div>
                                <div class="col-3 text-end text-teal-400 f-36">
                                    <div class="t-order-icon bg-color1 color0"><i class="fa fa-user"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body pb-1">
                            <div class="row">
                                <div class="col-9">
                                    <h6 class="mb-2 f-w-600 text-muted">Total Payments</h6>
                                    <h4 class="mb-3">{{ $totalPayments }}</h4>
                                </div>
                                <div class="col-3 text-end text-blue-400 f-36">
                                    <div class="t-order-icon bg-color1 color0"><i class="fa fa-dollar-sign"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card-main mb-3 card p-3">
                        <div class="recent-orders-table">
                            <div class="row">
                                <div class="col-6">
                                    <h4 class="map-heading border-0 pt-2 mb-2">Latest Users</h4>
                                </div>
                                <div class="col-6">
                                    <div class="float-end">
                                        <a class="text-decoration-none" href="{{ route('admin.users') }}">View All</a>
                                    </div>
                                </div>
                            </div>
                            <div class="recent-orders-table-wht table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">Name</th>
                                            <th scope="col" class="text-end">Age</th>
                                            <th scope="col" class="text-end">Phone</th>
                                            <th scope="col" class="text-end">Joined On</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (count($latestUsers))
                                            @foreach ($latestUsers as $item)
                                                <tr>
                                                    <td>
                                                        <a class="text-dark" href="{{ route('admin.users.edit', $item->id) }}">{{ $item->full_name }}</a>
                                                    </td>
                                                    <td class="text-end">{{ $item->age }}</td>
                                                    <td class="text-end">${{ $item->phone }}</td>
                                                    <td class="text-end">{{ $item->created_at->diffForHumans() }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card-main mb-3 card p-3">
                        <div class="recent-orders-table">
                            <div class="row">
                                <div class="col-6">
                                    <h4 class="map-heading border-0 pt-2 mb-2">Latest Packages</h4>
                                </div>
                                <div class="col-6">
                                    <div class="float-end">
                                        <a class="text-decoration-none" href="{{ route('admin.packages') }}">View All</a>

                                    </div>
                                </div>
                            </div>
                            <div class="recent-orders-table-wht table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">Title</th>
                                            <th scope="col" class="text-end">Price</th>
                                            <th scope="col" class="text-end">Discounted Price</th>
                                            <th scope="col" class="text-end">Created On</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (count($latestPackages))
                                            @foreach ($latestPackages as $item)
                                                <tr>
                                                    <td>
                                                        <a class="text-dark" href="{{ route('admin.packages.edit',$item->id) }}">{{ $item->title }}</a>
                                                    </td>
                                                    <td class="text-end">&#x20B9;{{ number_format($item->price, 2) }}</td>
                                                    <td class="text-end">&#x20B9;{{ number_format($item->discounted_price, 2) }}
                                                    </td>
                                                    <td class="text-end">{{ $item->created_at->diffForHumans() }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- <div class="row">
                <div class="col-md-12">
                    <div class="card-main mb-3 card p-3">
                        <div class="recent-orders-table">
                            <div class="row">
                                <div class="col-6">
                                    <h4 class="map-heading border-0 pt-2 mb-2">Latest Orders</h4>
                                </div>
                                <div class="col-6">
                                    <div class="float-end">
                                        <a class="text-decoration-none" href="">View All</a>
                                    </div>
                                </div>
                            </div>
                            <div class="recent-orders-table-wht table-responsive">
                                <table class="table">
                                    <thead>

                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}
            <div class="row">
                <div class="col-md-12">
                    <div class="card-main mb-3 card p-3">
                        <div class="recent-orders-table">
                            <div class="row">
                                <div class="col-6">
                                    <h4 class="map-heading border-0 pt-2 mb-2">Latest Payments</h4>
                                </div>
                                <div class="col-6">
                                    <div class="float-end">
                                        <a class="text-decoration-none" href="">View All</a>

                                    </div>
                                </div>
                            </div>
                            <div class="recent-orders-table-wht table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Transaction Id</th>
                                            <th>Method</th>
                                            <th>Price</th>
                                            <th>Status</th>
                                            <th>User</th>
                                            <th>Membership</th>
                                            <th>Date & Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($latestPayments as $item)
                                            <tr>
                                                <td>{{ $item->transaction_id }}</td>
                                                <td>{{ $item->payment_method ? $item->payment_method : '' }}</td>
                                                <td>&#x20B9;{{ number_format($item->price, 2) }}</td>
                                                <td>
                                                    @switch($item->status)
                                                        @case('P')
                                                            Pending
                                                            @break
                                                        @case('S')
                                                            Success
                                                            @break
                                                        @case('C')
                                                            Cancel
                                                            @break
                                                        @default
                                                    @endswitch
                                                </td>                                                
                                                <td>
                                                    <a class="text-dark" href="{{ route('admin.users.edit', $item->user_id) }}">{{ $item->user ? $item->user->full_name : '' }}</a>
                                                </td>
                                                <td>{{ $item->user_membership ? $item->user_membership->membership->title : '' }}</td>
                                                <td>{{ $item->created_at->diffForHumans() }}</td>
                                            </tr>
                                        @empty
                                            {{--  --}}
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="card-main mb-3 card p-3">
                        <div>
                            <h4 class="map-heading border-0 pt-2 mb-2">Memberships</h4>
                            <canvas id="orders-chart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card-main mb-3 card p-3">
                        <div>
                            <h4 class="map-heading border-0 pt-2 mb-2">Payments</h4>
                            <canvas id="payments-chart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@push('scripts')
    <script>
        const ctx = document.getElementById('payments-chart');
        const paymentMonths = JSON.parse(@json($paymentMonths));
        const payments = JSON.parse(@json($paymentCounts));

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: paymentMonths,
                datasets: [{
                    label: 'Payments',
                    data: payments,
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    x: {
                        barPercentage: 0.3,
                        categoryPercentage: 0.5
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            callback: function(value) {
                                return Number.isInteger(value) ? value : '';
                            }
                        }
                    }
                }
            }
        });
    </script>

    <script>
        const ctx2 = document.getElementById('orders-chart');
        const membershipMonths = JSON.parse(@json($membershipMonths));
        const memberships = JSON.parse(@json($membershipCounts));

        new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: membershipMonths,
                datasets: [{
                    label: 'Memberships',
                    data: memberships,
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    x: {
                        barPercentage: 0.3,
                        categoryPercentage: 0.5
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            callback: function(value) {
                                return Number.isInteger(value) ? value : '';
                            }
                        }
                    }
                }
            }
        });
    </script>

@endpush

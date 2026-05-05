@extends('layouts.app')

@section('title', 'Dashboard – BrewPOS')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
<main class="main-content">

    <div class="page-header">
        <div>
            <h1 class="page-title">Dashboard</h1>
            <p class="page-subtitle">Overview of today's business</p>
        </div>
        <div class="header-date" id="headerDate"></div>
    </div>

    @include('dashboard.partials.stats')

    <div class="chart-card">
        <div class="chart-header">
            <h2 class="chart-title">Recent Sales Activity</h2>
            <div class="chart-tabs">
                <button class="chart-tab active" data-period="week">This Week</button>
                <button class="chart-tab" data-period="month">This Month</button>
            </div>
        </div>
        <div class="chart-body">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    @include('dashboard.partials.bottom-grid')

</main>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
@endpush

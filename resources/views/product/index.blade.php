@extends('layouts.app')

@section('title', 'Products – BrewPOS')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/product.css') }}">
@endpush

@section('content')
<main class="main-content">

    <div class="page-header">
        <div>
            <h1 class="page-title">Products Catalog</h1>
            <p class="page-subtitle">Manage your menu items, pricing, and availability</p>
        </div>
        <button class="btn-add" id="btnAddProduct">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add New Product
        </button>
    </div>

    <div class="stat-grid">
        <div class="stat-card" style="--delay:0s">
            <div class="stat-top">
                <span class="stat-label">Total Products</span>
                <div class="stat-icon-wrap" style="background:#f0ebe4">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#7a6555" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8h1a4 4 0 0 1 0 8h-1"/><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/><line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/></svg>
                </div>
            </div>
            <div class="stat-value">7</div>
            <div class="stat-sub">Active items on menu</div>
        </div>
        <div class="stat-card" style="--delay:0.07s">
            <div class="stat-top">
                <span class="stat-label">Average Margin</span>
                <div class="stat-icon-wrap" style="background:#edf7f1">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2e7d50" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                </div>
            </div>
            <div class="stat-value">72%</div>
            <div class="stat-change positive">+2.4% from last month</div>
        </div>
        <div class="stat-card" style="--delay:0.14s">
            <div class="stat-top">
                <span class="stat-label">Low Stock Items</span>
                <div class="stat-icon-wrap" style="background:#fff8ec">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#b36b00" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                </div>
            </div>
            <div class="stat-value">2</div>
            <div class="stat-change warning">Needs attention</div>
        </div>
        <div class="stat-card" style="--delay:0.21s">
            <div class="stat-top">
                <span class="stat-label">Total Value</span>
                <div class="stat-icon-wrap" style="background:#eaf1fd">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
            </div>
            <div class="stat-value">₱142.5K</div>
            <div class="stat-sub">Estimated stock value</div>
        </div>
    </div>

    <div class="table-card">
        <div class="table-toolbar">
            <div class="search-wrap">
                <svg class="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" id="searchInput" class="search-input" placeholder="Search products by name or category...">
            </div>
            <div class="filter-group">
                <button class="btn-filter" id="btnFilter">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                    Filter
                </button>
                <div class="filter-dropdown hidden" id="filterDropdown">
                    <p class="filter-label">Category</p>
                    <label class="filter-check"><input type="checkbox" value="Coffee" checked> Coffee</label>
                    <label class="filter-check"><input type="checkbox" value="Non-Coffee" checked> Non-Coffee</label>
                    <label class="filter-check"><input type="checkbox" value="Pastry" checked> Pastry</label>
                    <label class="filter-check"><input type="checkbox" value="Snacks" checked> Snacks</label>
                    <p class="filter-label" style="margin-top:12px">Status</p>
                    <label class="filter-check"><input type="checkbox" value="Active" checked> Active</label>
                    <label class="filter-check"><input type="checkbox" value="Inactive" checked> Inactive</label>
                    <button class="btn-apply-filter" id="btnApplyFilter">Apply</button>
                </div>
            </div>
        </div>

        <div class="table-wrap">
            <table class="product-table" id="productTable">
                <thead>
                    <tr>
                        <th style="width:64px">Image</th>
                        <th class="sortable" data-col="name">Product Name <span class="sort-arrow">↕</span></th>
                        <th class="sortable" data-col="category">Category <span class="sort-arrow">↕</span></th>
                        <th class="sortable" data-col="price">Price (₱) <span class="sort-arrow">↕</span></th>
                        <th class="sortable" data-col="cost">Cost (₱) <span class="sort-arrow">↕</span></th>
                        <th class="sortable" data-col="stock">Stock <span class="sort-arrow">↕</span></th>
                        <th>Status</th>
                        <th style="width:60px">Action</th>
                    </tr>
                </thead>
                <tbody id="productTableBody"></tbody>
            </table>
            <div class="empty-state hidden" id="emptyState">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#c8b8a8" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <p>No products found</p>
            </div>
        </div>

        <div class="table-footer">
            <span class="table-count" id="tableCount">Showing 7 of 7 products</span>
            <div class="pagination" id="pagination"></div>
        </div>
    </div>

</main>

@include('product.modal.product-modal')
@endsection

@push('scripts')
    <script src="{{ asset('js/product.js') }}"></script>
@endpush

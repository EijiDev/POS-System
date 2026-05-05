@extends('layouts.app')

@section('title', 'Expenses – BrewPOS')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/expenses.css') }}">
@endpush

@section('content')
<main class="main-content">

    <div class="page-header">
        <div>
            <h1 class="page-title">Expenses</h1>
            <p class="page-subtitle">Track business costs and daily spending</p>
        </div>
        <button class="btn-add" id="btnAddExpense">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Expense
        </button>
    </div>

    {{-- Summary Cards --}}
    <div class="stat-grid" id="statGrid">
        <div class="stat-card" style="--delay:0s">
            <div class="stat-top">
                <span class="stat-label">Total Expenses</span>
                <div class="stat-icon-wrap" style="background:#fef2f0">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#c0392b" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
            </div>
            <div class="stat-value" id="statTotal">₱0.00</div>
            <div class="stat-sub">This month so far</div>
        </div>
        <div class="stat-card" style="--delay:0.07s">
            <div class="stat-top">
                <span class="stat-label">Stock</span>
                <div class="stat-icon-wrap" style="background:#fff8ec">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#b36b00" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                </div>
            </div>
            <div class="stat-value" id="statStock">₱0.00</div>
            <div class="stat-sub">This month</div>
        </div>
        <div class="stat-card" style="--delay:0.14s">
            <div class="stat-top">
                <span class="stat-label">Utilities</span>
                <div class="stat-icon-wrap" style="background:#eaf1fd">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                </div>
            </div>
            <div class="stat-value" id="statUtilities">₱0.00</div>
            <div class="stat-sub">This month</div>
        </div>
        <div class="stat-card" style="--delay:0.21s">
            <div class="stat-top">
                <span class="stat-label">Rent</span>
                <div class="stat-icon-wrap" style="background:#edf7f1">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2e7d50" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                </div>
            </div>
            <div class="stat-value" id="statRent">₱0.00</div>
            <div class="stat-sub">This month</div>
        </div>
    </div>

    {{-- Category Breakdown --}}
    <div class="breakdown-card" id="breakdownCard">
        <div class="breakdown-list" id="breakdownList"></div>
    </div>

    {{-- Table --}}
    <div class="table-card">
        <div class="table-toolbar">
            <div class="search-wrap">
                <svg class="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" id="searchInput" class="search-input" placeholder="Search by category or description...">
            </div>
            <div class="filter-group">
                <button class="btn-filter" id="btnFilter">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                    Filter
                </button>
                <div class="filter-dropdown hidden" id="filterDropdown">
                    <p class="filter-label">Category</p>
                    <label class="filter-check"><input type="checkbox" value="Stock" checked> Stock</label>
                    <label class="filter-check"><input type="checkbox" value="Utilities" checked> Utilities</label>
                    <label class="filter-check"><input type="checkbox" value="Rent" checked> Rent</label>
                    <label class="filter-check"><input type="checkbox" value="Staff" checked> Staff</label>
                    <label class="filter-check"><input type="checkbox" value="Maintenance" checked> Maintenance</label>
                    <label class="filter-check"><input type="checkbox" value="Other" checked> Other</label>
                    <p class="filter-label" style="margin-top:12px">Status</p>
                    <label class="filter-check"><input type="checkbox" value="Paid" checked> Paid</label>
                    <label class="filter-check"><input type="checkbox" value="Pending" checked> Pending</label>
                    <button class="btn-apply-filter" id="btnApplyFilter">Apply</button>
                </div>
            </div>
        </div>

        <div class="table-wrap">
            <table class="expense-table">
                <thead>
                    <tr>
                        <th class="sortable" data-col="category">Category <span class="sort-arrow">↕</span></th>
                        <th class="sortable" data-col="description">Description <span class="sort-arrow">↕</span></th>
                        <th class="sortable" data-col="date">Date <span class="sort-arrow">↕</span></th>
                        <th>Status</th>
                        <th class="sortable" data-col="amount">Amount <span class="sort-arrow">↕</span></th>
                        <th style="width:60px"></th>
                    </tr>
                </thead>
                <tbody id="expenseTableBody"></tbody>
            </table>
            <div class="empty-state hidden" id="emptyState">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#c8b8a8" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <p>No expenses found</p>
            </div>
        </div>

        <div class="table-footer">
            <span class="table-count" id="tableCount">Showing 0 expenses</span>
            <div class="pagination" id="pagination"></div>
        </div>
    </div>

</main>

{{-- Add / Edit Modal --}}
<div class="modal-backdrop hidden" id="modalBackdrop">
    <div class="modal">
        <div class="modal-header">
            <div>
                <h3 class="modal-title" id="modalTitle">Record New Expense</h3>
                <p class="modal-subtitle">Track business outgoings and bills.</p>
            </div>
            <button class="modal-close" id="modalClose">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label>Description</label>
                <input type="text" id="fieldDescription" placeholder="e.g. Weekly Coffee Bean Supply">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Category</label>
                    <select id="fieldCategory">
                        <option value="Stock">Stock</option>
                        <option value="Utilities">Utilities</option>
                        <option value="Rent">Rent</option>
                        <option value="Staff">Staff</option>
                        <option value="Maintenance">Maintenance</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select id="fieldStatus">
                        <option value="Paid">Paid</option>
                        <option value="Pending">Pending</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Amount (₱)</label>
                    <input type="number" id="fieldAmount" placeholder="0.00" min="0" step="0.01">
                </div>
                <div class="form-group">
                    <label>Date</label>
                    <input type="date" id="fieldDate">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" id="btnCancel">Cancel</button>
            <button class="btn-save" id="btnSave">Save Expense</button>
        </div>
    </div>
</div>

{{-- Action Menu --}}
<div class="action-menu hidden" id="actionMenu">
    <button class="action-item" id="actionEdit">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
        Edit
    </button>
    <div class="action-divider"></div>
    <button class="action-item danger" id="actionDelete">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
        Delete
    </button>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/expenses.js') }}"></script>
@endpush

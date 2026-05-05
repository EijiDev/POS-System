@extends('layouts.app')

@section('title', 'Customers – BrewPOS')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customers.css') }}">
@endpush

@section('content')
<main class="main-content">

    <div class="page-header">
        <div>
            <h1 class="page-title">Customers</h1>
            <p class="page-subtitle">Loyalty program — track visits, points, and rewards</p>
        </div>
        <button class="btn-add" id="btnAddCustomer">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Customer
        </button>
    </div>

    {{-- Stat Cards --}}
    <div class="stat-grid">
        <div class="stat-card" style="--delay:0s">
            <div class="stat-top">
                <span class="stat-label">Total Customers</span>
                <div class="stat-icon-wrap" style="background:#eaf1fd">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
            </div>
            <div class="stat-value" id="statTotal">0</div>
            <div class="stat-sub">Registered members</div>
        </div>
        <div class="stat-card" style="--delay:0.07s">
            <div class="stat-top">
                <span class="stat-label">Gold Members</span>
                <div class="stat-icon-wrap" style="background:#fef9ec">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#b36b00" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                </div>
            </div>
            <div class="stat-value" id="statGold">0</div>
            <div class="stat-sub">500+ points • 10% discount</div>
        </div>
        <div class="stat-card" style="--delay:0.14s">
            <div class="stat-top">
                <span class="stat-label">Silver Members</span>
                <div class="stat-icon-wrap" style="background:#f5f5f5">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                </div>
            </div>
            <div class="stat-value" id="statSilver">0</div>
            <div class="stat-sub">100–499 points • 5% discount</div>
        </div>
        <div class="stat-card" style="--delay:0.21s">
            <div class="stat-top">
                <span class="stat-label">Total Points Given</span>
                <div class="stat-icon-wrap" style="background:#edf7f1">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2e7d50" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                </div>
            </div>
            <div class="stat-value" id="statPoints">0</div>
            <div class="stat-sub">Across all members</div>
        </div>
    </div>

    {{-- Tier Legend --}}
    <div class="tier-legend">
        <div class="tier-item bronze">
            <span class="tier-dot"></span>
            <div>
                <span class="tier-name">Bronze</span>
                <span class="tier-desc">0–99 pts • No discount</span>
            </div>
        </div>
        <div class="tier-item silver">
            <span class="tier-dot"></span>
            <div>
                <span class="tier-name">Silver</span>
                <span class="tier-desc">100–499 pts • 5% off</span>
            </div>
        </div>
        <div class="tier-item gold">
            <span class="tier-dot"></span>
            <div>
                <span class="tier-name">Gold</span>
                <span class="tier-desc">500+ pts • 10% off</span>
            </div>
        </div>
        <div class="tier-earn">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            1 point earned per ₱10 spent
        </div>
    </div>

    {{-- Table --}}
    <div class="table-card">
        <div class="table-toolbar">
            <div class="search-wrap">
                <svg class="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" id="searchInput" class="search-input" placeholder="Search by name or phone...">
            </div>
            <div class="filter-group">
                <button class="btn-filter" id="btnFilter">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                    Filter
                </button>
                <div class="filter-dropdown hidden" id="filterDropdown">
                    <p class="filter-label">Tier</p>
                    <label class="filter-check"><input type="checkbox" value="Bronze" checked> Bronze</label>
                    <label class="filter-check"><input type="checkbox" value="Silver" checked> Silver</label>
                    <label class="filter-check"><input type="checkbox" value="Gold" checked> Gold</label>
                    <button class="btn-apply-filter" id="btnApplyFilter">Apply</button>
                </div>
            </div>
        </div>

        <div class="table-wrap">
            <table class="customer-table">
                <thead>
                    <tr>
                        <th class="sortable" data-col="name">Customer <span class="sort-arrow">↕</span></th>
                        <th>Contact</th>
                        <th class="sortable" data-col="tier">Tier <span class="sort-arrow">↕</span></th>
                        <th class="sortable" data-col="points">Points <span class="sort-arrow">↕</span></th>
                        <th class="sortable" data-col="total_visits">Visits <span class="sort-arrow">↕</span></th>
                        <th class="sortable" data-col="total_spent">Total Spent <span class="sort-arrow">↕</span></th>
                        <th>Discount</th>
                        <th style="width:60px"></th>
                    </tr>
                </thead>
                <tbody id="customerTableBody"></tbody>
            </table>
            <div class="empty-state hidden" id="emptyState">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#c8b8a8" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                <p>No customers found</p>
            </div>
        </div>

        <div class="table-footer">
            <span class="table-count" id="tableCount">Showing 0 customers</span>
            <div class="pagination" id="pagination"></div>
        </div>
    </div>

</main>

{{-- Add / Edit Modal --}}
<div class="modal-backdrop hidden" id="modalBackdrop">
    <div class="modal">
        <div class="modal-header">
            <div>
                <h3 class="modal-title" id="modalTitle">Add New Customer</h3>
                <p class="modal-subtitle">Register a new loyalty member.</p>
            </div>
            <button class="modal-close" id="modalClose">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" id="fieldName" placeholder="e.g. Juan dela Cruz">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" id="fieldPhone" placeholder="e.g. 09171234567">
                </div>
                <div class="form-group">
                    <label>Email <span style="color:var(--text-muted);font-weight:400">(optional)</span></label>
                    <input type="email" id="fieldEmail" placeholder="e.g. juan@email.com">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" id="btnCancel">Cancel</button>
            <button class="btn-save" id="btnSave">Save Customer</button>
        </div>
    </div>
</div>

{{-- Points Modal --}}
<div class="modal-backdrop hidden" id="pointsBackdrop">
    <div class="modal">
        <div class="modal-header">
            <div>
                <h3 class="modal-title">Add Points</h3>
                <p class="modal-subtitle" id="pointsCustomerName">Customer</p>
            </div>
            <button class="modal-close" id="pointsClose">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <div class="modal-body">
            <div class="points-info" id="pointsInfo"></div>
            <div class="form-group">
                <label>Amount Spent (₱)</label>
                <input type="number" id="fieldSpent" placeholder="0.00" min="0" step="0.01">
            </div>
            <div class="points-preview hidden" id="pointsPreview">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                Will earn <strong id="previewEarned">0</strong> points
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" id="pointsCancel">Cancel</button>
            <button class="btn-save" id="btnAddPoints">Add Points</button>
        </div>
    </div>
</div>

{{-- Action Menu --}}
<div class="action-menu hidden" id="actionMenu">
    <button class="action-item" id="actionEdit">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
        Edit
    </button>
    <button class="action-item" id="actionPoints">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        Add Points
    </button>
    <div class="action-divider"></div>
    <button class="action-item danger" id="actionDelete">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
        Delete
    </button>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/customers.js') }}"></script>
@endpush

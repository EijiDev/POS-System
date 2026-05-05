@extends('layouts.app')

@section('title', 'Point of Sale – BrewPOS')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pos.css') }}">
@endpush

@section('content')
<div class="pos-layout">

    <!-- Left: Product Browser -->
    <section class="pos-left">
        <div class="pos-header">
            <div class="pos-title-wrap">
                <h1 class="pos-title">New Order</h1>
                <button class="btn-refresh" id="btnRefresh" title="Refresh products">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
                </button>
            </div>
            <div class="search-wrap">
                <svg class="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" id="searchInput" class="search-input" placeholder="Search products...">
            </div>
        </div>
        <div class="category-tabs" id="categoryTabs"></div>
        <div class="product-grid" id="productGrid"></div>
        <div class="grid-empty hidden" id="gridEmpty">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#c8b8a8" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <p>No products found</p>
        </div>
    </section>

    <!-- Right: Order Panel -->
    <aside class="pos-right">
        <div class="order-panel">
            <div class="order-header">
                <div>
                    <h2 class="order-title">Current Order</h2>
                    <p class="order-meta">Order #<span id="orderNum">2429</span> • Table <span id="orderTable">05</span></p>
                </div>
                <button class="btn-clear-order" id="btnClearOrder" title="Clear order">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                </button>
            </div>

            <div class="order-items" id="orderItems"></div>

            <div class="order-empty hidden" id="orderEmpty">
                <svg width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="#d4c4b4" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                <p>No items yet.<br>Add products to start an order.</p>
            </div>

            <div class="order-totals">
                <div class="total-row"><span>Subtotal</span><span id="subtotal">₱0.00</span></div>
                <div class="total-row"><span>Tax (10%)</span><span id="tax">₱0.00</span></div>
                <div class="total-row grand"><span>Total</span><span id="grandTotal">₱0.00</span></div>
            </div>

            <div class="order-actions">
                <button class="btn-receipt" id="btnReceipt">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                    Receipt
                </button>
                <button class="btn-cashpay" id="btnCashPay">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                    Cash Pay
                </button>
            </div>
        </div>
    </aside>

</div>

@include('pos.modal.pos-modal')
@endsection

@push('scripts')
    <script src="{{ asset('js/pos.js') }}"></script>
@endpush

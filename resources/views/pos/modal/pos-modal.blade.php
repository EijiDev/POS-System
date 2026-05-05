<!-- Receipt Modal -->
<div class="modal-backdrop hidden" id="receiptBackdrop">
    <div class="modal receipt-modal">
        <div class="modal-header">
            <div>
                <h3 class="modal-title">Order Receipt</h3>
                <p class="modal-sub">Order #<span id="receiptOrderNum">0000</span></p>
            </div>
            <button class="modal-close" id="receiptClose">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <div class="modal-body">
            <div class="receipt-paper">
                <div class="receipt-shop-name">BrewPOS Cafe</div>
                <div class="receipt-shop-addr">123 Coffee Street, Manila</div>
                <div class="receipt-divider dashed"></div>
                <div class="receipt-items" id="receiptItems"></div>
                <div class="receipt-divider dashed"></div>
                <div class="receipt-row"><span>Subtotal</span><span id="receiptSubtotal">₱0.00</span></div>
                <div class="receipt-row"><span>Tax (10%)</span><span id="receiptTax">₱0.00</span></div>
                <div class="receipt-divider"></div>
                <div class="receipt-row total"><span>Total</span><span id="receiptTotal">₱0.00</span></div>
                <div class="receipt-divider dashed"></div>
                <p class="receipt-thanks">Thank you for visiting!</p>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" id="receiptCancel">Cancel</button>
            <button class="btn-save" id="btnPrintReceipt">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                Print Receipt
            </button>
        </div>
    </div>
</div>

<!-- Cash Payment Modal -->
<div class="modal-backdrop hidden" id="cashBackdrop">
    <div class="modal cash-modal">
        <div class="modal-header">
            <div>
                <h3 class="modal-title">Cash Payment</h3>
                <p class="modal-sub">Complete the payment for this order</p>
            </div>
            <button class="modal-close" id="cashClose">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <div class="modal-body">
            <div class="amount-due-box">
                <p class="amount-due-label">AMOUNT DUE</p>
                <p class="amount-due-value" id="cashAmountDue">₱0.00</p>
            </div>
            <div class="cash-field-wrap">
                <label class="cash-label">Amount Received</label>
                <div class="cash-input-wrap">
                    <span class="cash-peso">₱</span>
                    <input type="number" id="cashReceived" class="cash-input" placeholder="0.00" min="0" step="0.01">
                </div>
            </div>
            <div class="quick-amounts" id="quickAmounts">
                <button class="quick-btn" data-val="100">₱100</button>
                <button class="quick-btn" data-val="200">₱200</button>
                <button class="quick-btn" data-val="500">₱500</button>
                <button class="quick-btn" data-val="1000">₱1000</button>
            </div>
            <div class="change-row hidden" id="changeRow">
                <span class="change-label">Change</span>
                <span class="change-value" id="changeValue">₱0.00</span>
            </div>
        </div>
        <div class="modal-footer cash-footer">
            <button class="btn-cancel" id="cashCancel">Cancel</button>
            <button class="btn-confirm-pay" id="btnConfirmPay" disabled>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                Confirm Payment
            </button>
        </div>
    </div>
</div>

<!-- Success Toast -->
<div class="toast hidden" id="toast">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
    <span id="toastMsg">Payment confirmed!</span>
</div>

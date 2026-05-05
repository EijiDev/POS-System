/* ===================================================
   POS.JS – Point of Sale Logic
   BrewPOS Cafe System
   =================================================== */

'use strict';

/* ── Sample Product Data (replace with PHP/AJAX fetch) ── */
let PRODUCTS = [];

const TAX_RATE = 0.10;

/* ── State ── */
let activeCategory = 'All';
let searchQuery    = '';
let orderItems     = [];   // [{ product, qty }]
let orderNumber    = Math.floor(1000 + Math.random() * 9000);
let tableNumber    = '05';

/* ── DOM References ── */
const categoryTabsEl = document.getElementById('categoryTabs');
const productGridEl  = document.getElementById('productGrid');
const gridEmptyEl    = document.getElementById('gridEmpty');
const searchInputEl  = document.getElementById('searchInput');

const orderItemsEl   = document.getElementById('orderItems');
const orderEmptyEl   = document.getElementById('orderEmpty');
const orderNumEl     = document.getElementById('orderNum');
const orderTableEl   = document.getElementById('orderTable');

const subtotalEl     = document.getElementById('subtotal');
const taxEl          = document.getElementById('tax');
const grandTotalEl   = document.getElementById('grandTotal');

const btnClearOrder  = document.getElementById('btnClearOrder');
const btnReceipt     = document.getElementById('btnReceipt');
const btnCashPay     = document.getElementById('btnCashPay');
const btnRefresh     = document.getElementById('btnRefresh');

// Receipt Modal
const receiptBackdrop  = document.getElementById('receiptBackdrop');
const receiptOrderNum  = document.getElementById('receiptOrderNum');
const receiptItemsEl   = document.getElementById('receiptItems');
const receiptSubtotal  = document.getElementById('receiptSubtotal');
const receiptTax       = document.getElementById('receiptTax');
const receiptTotal     = document.getElementById('receiptTotal');
const receiptClose     = document.getElementById('receiptClose');
const receiptCancel    = document.getElementById('receiptCancel');
const btnPrintReceipt  = document.getElementById('btnPrintReceipt');

// Cash Modal
const cashBackdrop     = document.getElementById('cashBackdrop');
const cashAmountDue    = document.getElementById('cashAmountDue');
const cashReceivedEl   = document.getElementById('cashReceived');
const changeRowEl      = document.getElementById('changeRow');
const changeValueEl    = document.getElementById('changeValue');
const cashClose        = document.getElementById('cashClose');
const cashCancel       = document.getElementById('cashCancel');
const btnConfirmPay    = document.getElementById('btnConfirmPay');

// Toast
const toastEl  = document.getElementById('toast');
const toastMsg = document.getElementById('toastMsg');

/* ══════════════════════════════════════════
   INIT
══════════════════════════════════════════ */
let autoRefreshInterval = null;

async function init() {
  orderNumEl.textContent   = orderNumber;
  orderTableEl.textContent = tableNumber;
  await loadProducts();
  buildCategoryTabs();
  renderProducts();
  renderOrder();
  bindEvents();
  startAutoRefresh();
}

async function loadProducts() {
  try {
    const response = await fetch('/api/pos/products');
    PRODUCTS = await response.json();
  } catch (error) {
    console.error('Failed to load products:', error);
    showToast('Failed to load products');
  }
}

async function refreshProducts() {
  btnRefresh.classList.add('spinning');
  await loadProducts();
  buildCategoryTabs();
  renderProducts();
  setTimeout(() => btnRefresh.classList.remove('spinning'), 600);
}

function startAutoRefresh() {
  // Auto-refresh products every 30 seconds
  autoRefreshInterval = setInterval(async () => {
    await loadProducts();
    buildCategoryTabs();
    renderProducts();
  }, 30000);
}

/* ══════════════════════════════════════════
   CATEGORY TABS
══════════════════════════════════════════ */
function getCategories() {
  return [...new Set(PRODUCTS.map(p => p.category))];
}

function buildCategoryTabs() {
  const cats = getCategories();
  categoryTabsEl.innerHTML = '';
  cats.forEach((cat, i) => {
    const btn = document.createElement('button');
    btn.className = 'tab-btn' + (i === 0 ? ' active' : '');
    btn.textContent = cat;
    btn.addEventListener('click', () => {
      activeCategory = cat;
      document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      renderProducts();
    });
    categoryTabsEl.appendChild(btn);
  });
  activeCategory = cats[0];
}

/* ══════════════════════════════════════════
   PRODUCT GRID
══════════════════════════════════════════ */
function getFilteredProducts() {
  return PRODUCTS.filter(p => {
    const matchCat   = p.category === activeCategory;
    const matchQuery = p.name.toLowerCase().includes(searchQuery.toLowerCase());
    return matchCat && matchQuery;
  });
}

function renderProducts() {
  const filtered = getFilteredProducts();
  productGridEl.innerHTML = '';

  if (filtered.length === 0) {
    gridEmptyEl.classList.remove('hidden');
    return;
  }
  gridEmptyEl.classList.add('hidden');

  filtered.forEach(product => {
    const card = document.createElement('div');
    card.className = 'product-card';
    card.innerHTML = `
      <img class="product-card-img" src="${product.img}" alt="${product.name}"
           onerror="this.src='https://placehold.co/200x200/f5f5f5/b0a090?text=${encodeURIComponent(product.name)}'">
      <button class="product-card-add" title="Add to order">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <line x1="12" y1="5" x2="12" y2="19"/>
          <line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
      </button>
      <div class="product-card-info">
        <div class="product-card-category">${product.category}</div>
        <div class="product-card-name">${product.name}</div>
        <div class="product-card-price">₱${product.price.toFixed(2)}</div>
      </div>
    `;
    
    const addBtn = card.querySelector('.product-card-add');
    addBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      addToOrder(product);
    });
    
    card.addEventListener('click', () => addToOrder(product));
    productGridEl.appendChild(card);
  });
}

/* ══════════════════════════════════════════
   ORDER MANAGEMENT
══════════════════════════════════════════ */
function addToOrder(product) {
  const existing = orderItems.find(i => i.product.id === product.id);
  if (existing) {
    existing.qty++;
  } else {
    orderItems.push({ product, qty: 1 });
  }
  renderOrder();
}

function removeFromOrder(productId) {
  orderItems = orderItems.filter(i => i.product.id !== productId);
  renderOrder();
}

function changeQty(productId, delta) {
  const item = orderItems.find(i => i.product.id === productId);
  if (!item) return;
  item.qty += delta;
  if (item.qty <= 0) {
    removeFromOrder(productId);
  } else {
    renderOrder();
  }
}

function clearOrder() {
  if (orderItems.length === 0) return;
  orderItems = [];
  orderNumber = Math.floor(1000 + Math.random() * 9000);
  orderNumEl.textContent = orderNumber;
  renderOrder();
}

function getTotals() {
  const subtotal = orderItems.reduce((sum, i) => sum + i.product.price * i.qty, 0);
  const tax      = subtotal * TAX_RATE;
  const total    = subtotal + tax;
  return { subtotal, tax, total };
}

function renderOrder() {
  const { subtotal, tax, total } = getTotals();

  // Show/hide empty state
  if (orderItems.length === 0) {
    orderItemsEl.innerHTML = '';
    orderEmptyEl.classList.remove('hidden');
  } else {
    orderEmptyEl.classList.add('hidden');
    orderItemsEl.innerHTML = '';
    orderItems.forEach(item => {
      const lineTotal = item.product.price * item.qty;
      const row = document.createElement('div');
      row.className = 'order-item';
      row.dataset.id = item.product.id;
      row.innerHTML = `
        <div class="order-item-top">
          <span class="oi-name">${item.product.name}</span>
          <span class="oi-price">₱${lineTotal.toFixed(2)}</span>
        </div>
        <div class="order-item-bottom">
          <div class="qty-ctrl">
            <button class="qty-btn btn-minus" data-id="${item.product.id}">−</button>
            <span class="qty-num">${item.qty}</span>
            <button class="qty-btn btn-plus" data-id="${item.product.id}">+</button>
          </div>
          <button class="btn-delete-item" data-id="${item.product.id}" title="Remove">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                 stroke-linecap="round" stroke-linejoin="round">
              <polyline points="3 6 5 6 21 6"/>
              <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
              <path d="M10 11v6"/><path d="M14 11v6"/>
              <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
            </svg>
          </button>
        </div>
      `;
      orderItemsEl.appendChild(row);
    });

    // Bind qty & delete buttons
    orderItemsEl.querySelectorAll('.btn-minus').forEach(btn => {
      btn.addEventListener('click', () => changeQty(+btn.dataset.id, -1));
    });
    orderItemsEl.querySelectorAll('.btn-plus').forEach(btn => {
      btn.addEventListener('click', () => changeQty(+btn.dataset.id, +1));
    });
    orderItemsEl.querySelectorAll('.btn-delete-item').forEach(btn => {
      btn.addEventListener('click', () => removeFromOrder(+btn.dataset.id));
    });
  }

  // Update totals
  subtotalEl.textContent  = `₱${subtotal.toFixed(2)}`;
  taxEl.textContent       = `₱${tax.toFixed(2)}`;
  grandTotalEl.textContent= `₱${total.toFixed(2)}`;
}

/* ══════════════════════════════════════════
   RECEIPT MODAL
══════════════════════════════════════════ */
function openReceiptModal() {
  const { subtotal, tax, total } = getTotals();
  receiptOrderNum.textContent = orderNumber;

  // Build receipt item rows
  receiptItemsEl.innerHTML = '';
  if (orderItems.length === 0) {
    receiptItemsEl.innerHTML = '<p style="color:#b0a090;font-size:13px;text-align:center;">No items in order.</p>';
  } else {
    orderItems.forEach(item => {
      const row = document.createElement('div');
      row.className = 'receipt-item-row';
      row.innerHTML = `
        <span class="ri-name">${item.product.name}</span>
        <span class="ri-qty">x${item.qty}</span>
        <span class="ri-amt">₱${(item.product.price * item.qty).toFixed(2)}</span>
      `;
      receiptItemsEl.appendChild(row);
    });
  }

  receiptSubtotal.textContent = `₱${subtotal.toFixed(2)}`;
  receiptTax.textContent      = `₱${tax.toFixed(2)}`;
  receiptTotal.textContent    = `₱${total.toFixed(2)}`;

  receiptBackdrop.classList.remove('hidden');
}

function closeReceiptModal() {
  receiptBackdrop.classList.add('hidden');
}

function printReceipt() {
  window.print();
}

/* ══════════════════════════════════════════
   CASH PAYMENT MODAL
══════════════════════════════════════════ */
function openCashModal() {
  const { total } = getTotals();
  cashAmountDue.textContent = `₱${total.toFixed(2)}`;
  cashReceivedEl.value      = '';
  changeRowEl.classList.add('hidden');
  btnConfirmPay.disabled    = true;
  cashBackdrop.classList.remove('hidden');
  setTimeout(() => cashReceivedEl.focus(), 100);
}

function closeCashModal() {
  cashBackdrop.classList.add('hidden');
}

function handleCashInput() {
  const { total } = getTotals();
  const received  = parseFloat(cashReceivedEl.value) || 0;
  const change    = received - total;

  if (received >= total) {
    changeRowEl.classList.remove('hidden');
    changeValueEl.textContent = `₱${change.toFixed(2)}`;
    btnConfirmPay.disabled = false;
  } else {
    changeRowEl.classList.add('hidden');
    btnConfirmPay.disabled = true;
  }
}

function confirmPayment() {
  const { subtotal, tax, total } = getTotals();
  const received = parseFloat(cashReceivedEl.value) || 0;
  const change = received - total;

  // Prepare order data
  const orderData = {
    orderNumber: orderNumber,
    tableNumber: tableNumber,
    subtotal: subtotal,
    tax: tax,
    total: total,
    paymentMethod: 'Cash',
    amountReceived: received,
    changeGiven: change,
    items: orderItems.map(item => ({
      productId: item.product.id,
      productName: item.product.name,
      price: item.product.price,
      quantity: item.qty
    }))
  };

  // Save to database
  fetch('/api/pos/save-order', {
    method: 'POST',
    headers: { 
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify(orderData)
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      closeCashModal();
      showToast('Payment confirmed! Order #' + orderNumber);
      setTimeout(() => {
        clearOrder();
        orderNumber = Math.floor(1000 + Math.random() * 9000);
        orderNumEl.textContent = orderNumber;
        renderOrder();
      }, 600);
    } else {
      showToast('Error: ' + data.message);
    }
  })
  .catch(err => {
    console.error('Save order error:', err);
    showToast('Failed to save order');
  });
}

/* ══════════════════════════════════════════
   TOAST
══════════════════════════════════════════ */
let toastTimer = null;

function showToast(message, duration = 3000) {
  toastMsg.textContent = message;
  toastEl.classList.remove('hidden');
  // Force reflow
  toastEl.getBoundingClientRect();
  toastEl.classList.add('show');
  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => {
    toastEl.classList.remove('show');
  }, duration);
}

/* ══════════════════════════════════════════
   EVENT BINDING
══════════════════════════════════════════ */
function bindEvents() {
  // Refresh button
  btnRefresh.addEventListener('click', refreshProducts);

  // Search
  searchInputEl.addEventListener('input', (e) => {
    searchQuery = e.target.value.trim();
    renderProducts();
  });

  // Clear order
  btnClearOrder.addEventListener('click', () => {
    if (orderItems.length === 0) return;
    if (confirm('Clear the current order?')) clearOrder();
  });

  // Receipt button
  btnReceipt.addEventListener('click', openReceiptModal);
  receiptClose.addEventListener('click', closeReceiptModal);
  receiptCancel.addEventListener('click', closeReceiptModal);
  receiptBackdrop.addEventListener('click', (e) => {
    if (e.target === receiptBackdrop) closeReceiptModal();
  });
  btnPrintReceipt.addEventListener('click', printReceipt);

  // Cash Pay button
  btnCashPay.addEventListener('click', () => {
    if (orderItems.length === 0) {
      showToast('Add items to the order first.');
      return;
    }
    openCashModal();
  });

  cashReceivedEl.addEventListener('input', handleCashInput);

  // Quick amount buttons
  document.querySelectorAll('.quick-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const val = parseFloat(btn.dataset.val);
      cashReceivedEl.value = val.toFixed(2);
      handleCashInput();
    });
  });

  cashClose.addEventListener('click', closeCashModal);
  cashCancel.addEventListener('click', closeCashModal);
  cashBackdrop.addEventListener('click', (e) => {
    if (e.target === cashBackdrop) closeCashModal();
  });
  btnConfirmPay.addEventListener('click', confirmPayment);

  // Keyboard: Escape closes modals
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      closeReceiptModal();
      closeCashModal();
    }
  });
}

/* ── Run ── */
document.addEventListener('DOMContentLoaded', init);

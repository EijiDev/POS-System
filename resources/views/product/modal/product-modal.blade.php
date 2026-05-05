<!-- Add / Edit Modal -->
<div class="modal-backdrop hidden" id="modalBackdrop">
    <div class="modal" id="productModal">
        <div class="modal-header">
            <h3 class="modal-title" id="modalTitle">Add New Product</h3>
            <button class="modal-close" id="modalClose">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" id="fieldName" placeholder="e.g. Iced Latte">
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <select id="fieldCategory">
                        <option value="Coffee">Coffee</option>
                        <option value="Non-Coffee">Non-Coffee</option>
                        <option value="Pastry">Pastry</option>
                        <option value="Snacks">Snacks</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Price (₱)</label>
                    <input type="number" id="fieldPrice" placeholder="0.00" min="0" step="0.01">
                </div>
                <div class="form-group">
                    <label>Cost (₱)</label>
                    <input type="number" id="fieldCost" placeholder="0.00" min="0" step="0.01">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Stock</label>
                    <input type="number" id="fieldStock" placeholder="0" min="0">
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select id="fieldStatus">
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>Product Image</label>
                <input type="file" id="fieldImage" accept="image/*">
                <input type="hidden" id="fieldImageUrl">
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" id="btnCancel">Cancel</button>
            <button class="btn-save" id="btnSave">Save Product</button>
        </div>
    </div>
</div>

<!-- Action Context Menu -->
<div class="action-menu hidden" id="actionMenu">
    <button class="action-item" id="actionEdit">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
        Edit
    </button>
    <button class="action-item" id="actionToggle">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18.36 6.64A9 9 0 1 1 5.64 5.64"/><line x1="12" y1="2" x2="12" y2="12"/></svg>
        Toggle Status
    </button>
    <div class="action-divider"></div>
    <button class="action-item danger" id="actionDelete">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
        Delete
    </button>
</div>

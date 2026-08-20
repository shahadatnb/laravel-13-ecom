<div id="section-variants">
<div id="variable-fields" class="row" style="display: none;">
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Dynamic Variants</h3>
                <span class="badge badge-success ml-2">Auto-Save</span>
            </div>
            <div class="card-body">
                <div id="attributes-container">
                    @foreach($attributes as $attribute)
                        @if($attribute->type == 'select' || $attribute->type == 'color')
                            <div class="form-group attr-group" data-attr-type="{{ $attribute->type }}" data-attr-name="{{ $attribute->name }}">
                                <label class="attr-group-label">
                                    <i class="fas fa-{{ $attribute->type == 'color' ? 'palette' : 'ruler' }}"></i>
                                    {{ $attribute->name }}
                                    <span class="attr-selected-count text-muted small ml-2">0 selected</span>
                                </label>
                                <div class="attr-options">
                                    @foreach($attribute->values as $value)
                                        @if($attribute->type == 'color')
                                            <button type="button"
                                                class="attr-option attr-swatch"
                                                data-attr-name="{{ $attribute->name }}"
                                                data-value="{{ $value->value }}"
                                                style="background-color: {{ $value->value }};"
                                                title="{{ $value->value }}">
                                                <span class="swatch-label">{{ $value->value }}</span>
                                            </button>
                                        @else
                                            <button type="button"
                                                class="attr-option attr-pill"
                                                data-attr-name="{{ $attribute->name }}"
                                                data-value="{{ $value->value }}">
                                                {{ $value->value }}
                                            </button>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                <div id="combination-preview" class="combination-preview" style="display: none;">
                    <div class="preview-header">
                        <strong><i class="fas fa-list-check text-success"></i> Select Combinations</strong>
                        <span class="text-muted small ml-2" id="preview-count">0 combinations</span>
                        <label class="preview-toggle-all ml-auto mb-0">
                            <input type="checkbox" id="preview-select-all" checked />
                            <span class="small">All</span>
                        </label>
                    </div>
                    <div class="preview-body" id="preview-body"></div>
                </div>

                <button type="button" class="btn btn-primary btn-sm mt-2" id="generate-variants">
                    <i class="fas fa-plus"></i> Generate Selected
                </button>

                <div class="table-responsive mt-3" id="variants-container" style="display: none;">
                    <div id="bulk-update-bar" class="bulk-update-bar">
                        <div class="bulk-update-header" id="bulk-update-toggle">
                            <i class="fas fa-layer-group text-info"></i>
                            <strong>Bulk Update</strong>
                            <span class="text-muted small ml-2">Set same value for all variants</span>
                            <i class="fas fa-chevron-down ml-auto bulk-chevron"></i>
                        </div>
                        <div class="bulk-update-body" id="bulk-update-body" style="display: none;">
                            <div class="row align-items-end">
                                <div class="col-md-3">
                                    <div class="form-group mb-0">
                                        <label class="small text-muted mb-1">SKU Prefix</label>
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                            </div>
                                            <input type="text" id="bulk-sku" class="form-control" placeholder="e.g. PROD-" />
                                        </div>
                                        <small class="text-muted" style="font-size: 10px;">Appended with attribute values</small>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group mb-0">
                                        <label class="small text-muted mb-1">Price <span class="text-danger">*</span></label>
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                            </div>
                                            <input type="number" step="0.01" id="bulk-price" class="form-control" placeholder="0.00" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group mb-0">
                                        <label class="small text-muted mb-1">Stock <span class="text-danger">*</span></label>
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-box"></i></span>
                                            </div>
                                            <input type="number" id="bulk-stock" class="form-control" placeholder="0" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-0">
                                        <label class="small text-muted mb-1">SKU Pattern</label>
                                        <select id="bulk-sku-pattern" class="form-control form-control-sm">
                                            <option value="prefix-attr">PREFIX-VALUE1-VALUE2</option>
                                            <option value="attr-prefix">VALUE1-VALUE2-PREFIX</option>
                                            <option value="prefix-only">PREFIX-1, PREFIX-2…</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" id="bulk-apply-btn" class="btn btn-info btn-block btn-sm">
                                        <i class="fas fa-check-double"></i> Apply to All
                                    </button>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-12">
                                    <small class="text-warning">
                                        <i class="fas fa-info-circle"></i>
                                        Fields left empty will be skipped (not overwritten).
                                        Stock badges update live after apply.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="variant-summary-bar" id="variant-summary-bar" style="display: none;">
                        <div class="summary-stat">
                            <span class="summary-icon summary-icon-total"><i class="fas fa-layer-group"></i></span>
                            <span class="summary-value" id="summary-total">0</span>
                            <span class="summary-label">Total Variants</span>
                        </div>
                        <div class="summary-divider"></div>
                        <div class="summary-stat">
                            <span class="summary-icon summary-icon-stock"><i class="fas fa-boxes"></i></span>
                            <span class="summary-value" id="summary-total-stock">0</span>
                            <span class="summary-label">Total Stock</span>
                        </div>
                        <div class="summary-divider"></div>
                        <div class="summary-stat">
                            <span class="summary-badge summary-badge-instock" id="summary-instock-count">0</span>
                            <span class="summary-label">In Stock</span>
                        </div>
                        <div class="summary-stat">
                            <span class="summary-badge summary-badge-low" id="summary-low-count">0</span>
                            <span class="summary-label">Low</span>
                        </div>
                        <div class="summary-stat">
                            <span class="summary-badge summary-badge-out" id="summary-out-count">0</span>
                            <span class="summary-label">Out</span>
                        </div>
                        <div class="summary-divider"></div>
                        <a href="{{ route('admin.stock.stock-in-form', $product) }}" class="btn btn-sm btn-outline-success" title="Manage stock for all variants">
                            <i class="fas fa-plus-circle"></i> Add Stock
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-secondary export-csv-btn" id="export-csv-btn" title="Download all variant data as CSV">
                            <i class="fas fa-download"></i> Export CSV
                        </button>
                    </div>

                    <table class="table table-bordered" id="variants-table">
                        <thead>
                            <tr>
                                @foreach($attributes as $attribute)
                                    @if($attribute->type == 'select' || $attribute->type == 'color')
                                        <th data-attr-name="{{ $attribute->name }}">{{ $attribute->name }}</th>
                                    @endif
                                @endforeach
                                <th>
                                    Images
                                    <small class="d-block text-muted" style="font-size: 11px;">Auto-save on upload</small>
                                </th>
                                <th>SKU</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="variants-table-body"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
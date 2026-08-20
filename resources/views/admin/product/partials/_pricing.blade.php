<div id="section-pricing">
<div class="card card-primary">
    <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
        <h3 class="card-title"><i class="fas fa-dollar-sign"></i> Pricing & Stock</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
        </div>
    </div>
    <div class="card-body" id="pricingCardBody">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="regular_price">Regular Price</label>
                    <input id="regular_price" name="regular_price" type="number" step="0.01" value="{{ old('regular_price', $product->regular_price ?? '') }}" class="form-control" placeholder="0.00" />
                    @error('regular_price')<span class="text-danger">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="sale_price">Sale Price</label>
                    <input id="sale_price" name="sale_price" type="number" step="0.01" value="{{ old('sale_price', $product->sale_price ?? '') }}" class="form-control" placeholder="0.00" />
                    @error('sale_price')<span class="text-danger">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="cost_price">Cost Price</label>
                    <input id="cost_price" name="cost_price" type="number" step="0.01" value="{{ old('cost_price', $product->cost_price ?? '') }}" class="form-control" placeholder="0.00" />
                    @error('cost_price')<span class="text-danger">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="stock">Stock</label>
                    <input id="stock" name="stock" type="number" value="{{ old('stock', $product->stock ?? 0) }}" class="form-control" />
                    @error('stock')<span class="text-danger">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="minimum_stock">Minimum Stock</label>
                    <input id="minimum_stock" name="minimum_stock" type="number" value="{{ old('minimum_stock', $product->minimum_stock ?? 0) }}" class="form-control" />
                    @error('minimum_stock')<span class="text-danger">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="maximum_order">Maximum Order</label>
                    <input id="maximum_order" name="maximum_order" type="number" value="{{ old('maximum_order', $product->maximum_order ?? '') }}" class="form-control" />
                    @error('maximum_order')<span class="text-danger">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>
    </div>
</div>
</div>
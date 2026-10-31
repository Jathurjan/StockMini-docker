<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f5f7fb">
    <title>{{ $product->exists ? 'Edit product' : 'Add product' }} | StockMini</title>
    <style>
        :root {
            color-scheme: light;
            --navy: #17243b;
            --muted: #78859a;
            --blue: #356fe5;
            --line: #e8edf4;
            --surface: #fff;
            --canvas: #f5f7fb;
            --shadow: 0 8px 26px rgba(27, 46, 79, .055);
        }
        * { box-sizing: border-box; }
        body { margin: 0; background: var(--canvas); color: var(--navy); font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; font-size: 14px; }
        a { color: inherit; text-decoration: none; }
        button, input { font: inherit; }
        .app-shell { min-height: 100vh; }
        .sidebar { position: fixed; inset: 0 auto 0 0; z-index: 2; display: flex; width: 248px; flex-direction: column; border-right: 1px solid var(--line); background: var(--surface); padding: 28px 18px; }
        .brand { display: flex; align-items: center; gap: 11px; padding: 0 12px; font-size: 19px; font-weight: 750; letter-spacing: -.5px; }
        .brand-mark { display: grid; width: 34px; height: 34px; place-items: center; border-radius: 11px; background: #eaf1ff; color: var(--blue); }
        .brand-mark svg { width: 19px; height: 19px; }
        .nav-label { margin: 43px 12px 12px; color: #a0a9b8; font-size: 10px; font-weight: 750; letter-spacing: 1.15px; text-transform: uppercase; }
        .nav-link { display: flex; align-items: center; gap: 12px; border-radius: 9px; color: #68768b; padding: 11px 12px; font-weight: 600; }
        .nav-link.active { background: #edf3ff; color: var(--blue); }
        .nav-link svg { width: 18px; height: 18px; }
        .sidebar-note { margin-top: auto; border: 1px solid var(--line); border-radius: 12px; background: #fbfcfe; padding: 15px; color: var(--muted); font-size: 12px; line-height: 1.6; }
        .sidebar-note strong { display: block; margin-bottom: 4px; color: var(--navy); font-size: 13px; }
        .main { min-height: 100vh; margin-left: 248px; }
        .topbar { display: flex; min-height: 72px; align-items: center; justify-content: flex-end; border-bottom: 1px solid var(--line); background: var(--surface); padding: 0 42px; }
        .workspace-label { display: flex; align-items: center; gap: 9px; color: #617087; font-size: 13px; font-weight: 600; }
        .workspace-dot { width: 8px; height: 8px; border-radius: 50%; background: #42bd8b; box-shadow: 0 0 0 3px #e6f8f0; }
        .content { width: min(910px, 100%); margin: 0 auto; padding: 39px 42px 56px; }
        .breadcrumb { display: inline-flex; align-items: center; gap: 8px; margin-bottom: 22px; color: #738097; font-size: 12px; font-weight: 550; }
        .breadcrumb:hover, .breadcrumb svg { color: var(--blue); }
        .breadcrumb svg { width: 15px; height: 15px; transform: rotate(180deg); }
        .page-heading { margin-bottom: 24px; }
        .eyebrow { margin: 0 0 8px; color: var(--blue); font-size: 11px; font-weight: 750; letter-spacing: 1.15px; text-transform: uppercase; }
        h1 { margin: 0; font-size: clamp(25px, 3vw, 31px); font-weight: 730; letter-spacing: -1.05px; }
        .subtitle { margin: 8px 0 0; color: var(--muted); font-size: 14px; }
        .panel { overflow: hidden; border: 1px solid var(--line); border-radius: 13px; background: var(--surface); box-shadow: var(--shadow); }
        .panel-heading { border-bottom: 1px solid var(--line); padding: 20px 24px; }
        .panel-heading h2 { margin: 0; font-size: 15px; font-weight: 700; }
        .panel-heading p { margin: 5px 0 0; color: var(--muted); font-size: 12px; }
        .form-body { padding: 24px; }
        .form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 21px 18px; }
        .field-group { min-width: 0; }
        .field-group.full { grid-column: 1 / -1; }
        .field-label { display: flex; align-items: center; gap: 4px; margin-bottom: 8px; color: #34425a; font-size: 12px; font-weight: 650; }
        .required { color: #d15c57; }
        .field { display: block; width: 100%; height: 43px; border: 1px solid #dfe5ee; border-radius: 8px; outline: none; background: #fff; color: var(--navy); padding: 0 12px; transition: border-color .15s, box-shadow .15s; }
        .field::placeholder { color: #a5aebb; }
        .field:focus { border-color: #8eb0f5; box-shadow: 0 0 0 3px rgba(53, 111, 229, .1); }
        .field.is-invalid { border-color: #df8d88; }
        .field-hint { margin: 7px 0 0; color: #929eaf; font-size: 11px; }
        .field-error { margin: 6px 0 0; color: #b74640; font-size: 11px; }
        .form-footer { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-top: 25px; border-top: 1px solid #edf0f5; padding-top: 19px; }
        .footer-note { color: #8b97a8; font-size: 11px; }
        .button-group { display: flex; align-items: center; gap: 9px; }
        .button { display: inline-flex; min-height: 41px; align-items: center; justify-content: center; gap: 8px; border: 1px solid transparent; border-radius: 9px; cursor: pointer; font-weight: 650; padding: 0 15px; transition: background .15s, box-shadow .15s, transform .15s; }
        .button:hover { transform: translateY(-1px); }
        .button-primary { background: var(--blue); box-shadow: 0 5px 12px rgba(53, 111, 229, .18); color: #fff; }
        .button-primary:hover { background: #285fcf; }
        .button-secondary { border-color: #e2e7ef; background: #fff; color: #526178; }
        .button-secondary:hover { background: #f7f9fc; }
        .button svg { width: 16px; height: 16px; }
        .notice-error { margin: 0 24px 20px; border: 1px solid #f1c9c6; border-radius: 9px; background: #fff5f4; color: #a8443e; padding: 12px 15px; font-size: 13px; }
        .notice-error ul { margin: 7px 0 0; padding-left: 20px; }
        @media (max-width: 1050px) {
            .sidebar { width: 210px; }
            .main { margin-left: 210px; }
            .content { padding-right: 25px; padding-left: 25px; }
            .topbar { padding-right: 25px; }
        }
        @media (max-width: 700px) {
            .sidebar { position: static; width: 100%; flex-direction: row; align-items: center; justify-content: space-between; border-right: 0; border-bottom: 1px solid var(--line); padding: 13px 18px; }
            .brand { padding: 0; }
            .nav-label, .sidebar-note { display: none; }
            .nav-link { padding: 9px 10px; font-size: 12px; }
            .main { margin-left: 0; }
            .topbar { min-height: 48px; padding: 0 18px; }
            .content { padding: 27px 17px 38px; }
            .panel-heading, .form-body { padding-right: 17px; padding-left: 17px; }
            .notice-error { margin-right: 17px; margin-left: 17px; }
            .form-grid { gap: 18px 13px; }
        }
        @media (max-width: 480px) {
            .brand { font-size: 17px; }
            .nav-link { gap: 6px; font-size: 11px; padding: 8px; }
            .form-grid { grid-template-columns: 1fr; }
            .field-group.full { grid-column: auto; }
            .form-footer { align-items: flex-start; flex-direction: column; }
            .button-group { width: 100%; }
            .button-group .button { flex: 1; }
        }
    </style>
</head>
<body>
    <div class="app-shell">
        <aside class="sidebar">
            <a class="brand" href="{{ route('products.index') }}" aria-label="StockMini dashboard">
                <span class="brand-mark" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 7 9-4 9 4-9 4-9-4Z"/><path d="M3 7v10l9 4 9-4V7"/><path d="M12 11v10"/></svg>
                </span>
                StockMini
            </a>
            <p class="nav-label">Workspace</p>
            <a class="nav-link active" href="{{ route('products.index') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>
                Inventory
            </a>
            <div class="sidebar-note">
                <strong>Inventory overview</strong>
                Keep your products organized and your stock levels in view.
            </div>
        </aside>

        <main class="main">
            <header class="topbar">
                <div class="workspace-label"><span class="workspace-dot" aria-hidden="true"></span> Stock workspace</div>
            </header>

            <section class="content">
                <a class="breadcrumb" href="{{ route('products.index') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
                    Back to inventory
                </a>

                <div class="page-heading">
                    <p class="eyebrow">{{ $product->exists ? 'Product details' : 'Inventory' }}</p>
                    <h1>{{ $product->exists ? 'Edit product' : 'Add a product' }}</h1>
                    <p class="subtitle">{{ $product->exists ? 'Update product information and stock levels.' : 'Add a new item to your inventory catalog.' }}</p>
                </div>

                <section class="panel" aria-labelledby="form-heading">
                    <div class="panel-heading">
                        <h2 id="form-heading">Product information</h2>
                        <p>Enter the product details below. Fields marked with <span class="required">*</span> are required.</p>
                    </div>

                    @if ($errors->any())
                        <div class="notice-error" role="alert">
                            <strong>Please correct the highlighted fields.</strong>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ $product->exists ? route('products.update', $product) : route('products.store') }}" method="POST">
                        @csrf
                        @if ($product->exists)
                            @method('PUT')
                        @endif

                        <div class="form-body">
                            <div class="form-grid">
                                <div class="field-group full">
                                    <label class="field-label" for="name">Product name <span class="required">*</span></label>
                                    <input class="field @error('name') is-invalid @enderror" id="name" name="name" type="text" value="{{ old('name', $product->name) }}" maxlength="255" placeholder="e.g. Wireless keyboard" autocomplete="off" required aria-describedby="name-error">
                                    @error('name')<p class="field-error" id="name-error">{{ $message }}</p>@enderror
                                </div>

                                <div class="field-group">
                                    <label class="field-label" for="sku">Product SKU <span class="required">*</span></label>
                                    <input class="field @error('sku') is-invalid @enderror" id="sku" name="sku" type="text" value="{{ old('sku', $product->sku) }}" maxlength="255" placeholder="e.g. ACC-1042" autocomplete="off" required aria-describedby="sku-hint sku-error">
                                    <p class="field-hint" id="sku-hint">Must be unique for each product.</p>
                                    @error('sku')<p class="field-error" id="sku-error">{{ $message }}</p>@enderror
                                </div>

                                <div class="field-group">
                                    <label class="field-label" for="category">Category</label>
                                    <input class="field @error('category') is-invalid @enderror" id="category" name="category" type="text" value="{{ old('category', $product->category) }}" maxlength="255" placeholder="e.g. Accessories" aria-describedby="category-error">
                                    @error('category')<p class="field-error" id="category-error">{{ $message }}</p>@enderror
                                </div>

                                <div class="field-group">
                                    <label class="field-label" for="quantity">Quantity <span class="required">*</span></label>
                                    <input class="field @error('quantity') is-invalid @enderror" id="quantity" name="quantity" type="number" value="{{ old('quantity', $product->quantity) }}" min="0" step="1" placeholder="0" required aria-describedby="quantity-error">
                                    @error('quantity')<p class="field-error" id="quantity-error">{{ $message }}</p>@enderror
                                </div>

                                <div class="field-group">
                                    <label class="field-label" for="price">Unit price <span class="required">*</span></label>
                                    <input class="field @error('price') is-invalid @enderror" id="price" name="price" type="number" value="{{ old('price', $product->price) }}" min="0" max="99999999.99" step="0.01" inputmode="decimal" placeholder="0.00" required aria-describedby="price-error">
                                    @error('price')<p class="field-error" id="price-error">{{ $message }}</p>@enderror
                                </div>

                                <div class="field-group">
                                    <label class="field-label" for="min_stock">Minimum stock level <span class="required">*</span></label>
                                    <input class="field @error('min_stock') is-invalid @enderror" id="min_stock" name="min_stock" type="number" value="{{ old('min_stock', $product->min_stock) }}" min="0" step="1" placeholder="e.g. 10" required aria-describedby="min-stock-hint min_stock-error">
                                    <p class="field-hint" id="min-stock-hint">Products at or below this quantity are marked low stock.</p>
                                    @error('min_stock')<p class="field-error" id="min_stock-error">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <div class="form-footer">
                                <span class="footer-note"><span class="required">*</span> Required fields</span>
                                <div class="button-group">
                                    <a class="button button-secondary" href="{{ route('products.index') }}">Cancel</a>
                                    <button class="button button-primary" type="submit">
                                        @if ($product->exists)
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 7v10a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7"/><path d="M4 7V5a2 2 0 0 1 2-2h10l4 4"/><path d="M8 19v-6h8v6M8 3v5h7"/></svg>
                                            Update product
                                        @else
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                                            Save product
                                        @endif
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </section>
            </section>
        </main>
    </div>
</body>
</html>

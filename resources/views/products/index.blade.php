<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f5f7fb">
    <title>Inventory Dashboard | StockMini</title>
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
        .content { width: min(1240px, 100%); margin: 0 auto; padding: 39px 42px 56px; }
        .page-heading { display: flex; align-items: flex-end; justify-content: space-between; gap: 20px; margin-bottom: 27px; }
        .eyebrow { margin: 0 0 8px; color: var(--blue); font-size: 11px; font-weight: 750; letter-spacing: 1.15px; text-transform: uppercase; }
        h1 { margin: 0; font-size: clamp(25px, 3vw, 31px); font-weight: 730; letter-spacing: -1.05px; }
        .subtitle { margin: 8px 0 0; color: var(--muted); font-size: 14px; }
        .button { display: inline-flex; min-height: 42px; align-items: center; justify-content: center; gap: 9px; border: 1px solid transparent; border-radius: 9px; cursor: pointer; font-weight: 650; padding: 0 15px; transition: background .15s, box-shadow .15s, transform .15s; }
        .button:hover { transform: translateY(-1px); }
        .button-primary { background: var(--blue); box-shadow: 0 5px 12px rgba(53, 111, 229, .18); color: #fff; }
        .button-primary:hover { background: #285fcf; }
        .button svg { width: 16px; height: 16px; }
        .stats-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; margin-bottom: 25px; }
        .stat-card { min-height: 134px; border: 1px solid var(--line); border-radius: 13px; background: var(--surface); box-shadow: var(--shadow); padding: 19px 20px; }
        .stat-top { display: flex; align-items: center; justify-content: space-between; gap: 12px; color: #748198; font-size: 12px; font-weight: 600; }
        .stat-icon { display: grid; width: 34px; height: 34px; place-items: center; border-radius: 10px; background: #edf3ff; color: var(--blue); }
        .stat-icon svg { width: 17px; height: 17px; }
        .stat-icon.amber { background: #fff6e8; color: #d89022; }
        .stat-icon.red { background: #fff0ef; color: #dd625a; }
        .stat-value { margin-top: 15px; font-size: 27px; font-weight: 740; letter-spacing: -.8px; line-height: 1; }
        .stat-hint { margin-top: 8px; color: #9aa4b3; font-size: 11px; }
        .panel { overflow: hidden; border: 1px solid var(--line); border-radius: 13px; background: var(--surface); box-shadow: var(--shadow); }
        .panel-header { display: flex; align-items: center; justify-content: space-between; gap: 18px; border-bottom: 1px solid var(--line); padding: 20px 22px; }
        .panel-title { margin: 0; font-size: 15px; font-weight: 700; }
        .panel-caption { margin: 5px 0 0; color: var(--muted); font-size: 12px; }
        .search-form { display: flex; width: min(310px, 100%); gap: 8px; }
        .search-wrap { position: relative; flex: 1; }
        .search-wrap svg { position: absolute; top: 50%; left: 11px; width: 16px; height: 16px; color: #9aa5b5; transform: translateY(-50%); pointer-events: none; }
        .search-input { width: 100%; height: 38px; border: 1px solid #e4e9f1; border-radius: 8px; outline: none; background: #fff; color: var(--navy); padding: 0 11px 0 35px; }
        .search-input::placeholder { color: #a3acba; }
        .search-input:focus, .field:focus { border-color: #8eb0f5; box-shadow: 0 0 0 3px rgba(53, 111, 229, .1); }
        .button-secondary { min-height: 38px; border-color: #e4e9f1; background: #fff; color: #526178; padding: 0 12px; }
        .button-secondary:hover { background: #f7f9fc; }
        .table-scroll { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; text-align: left; white-space: nowrap; }
        thead { background: #fafbfd; }
        th { height: 43px; color: #8792a3; font-size: 10px; font-weight: 700; letter-spacing: .75px; text-transform: uppercase; }
        th, td { padding: 0 21px; }
        td { height: 69px; border-top: 1px solid #eef1f5; color: #526178; font-size: 13px; }
        .product-name { display: block; color: var(--navy); font-size: 13px; font-weight: 650; }
        .product-category { display: block; margin-top: 4px; color: #9aa4b3; font-size: 11px; }
        .sku { color: #7d899b; font-size: 12px; }
        .status { display: inline-flex; align-items: center; gap: 6px; border-radius: 999px; padding: 6px 9px; font-size: 11px; font-weight: 650; }
        .status::before { width: 6px; height: 6px; border-radius: 50%; background: currentColor; content: ""; }
        .status-in { background: #eaf8f1; color: #23956a; }
        .status-low { background: #fff5e5; color: #bd7c16; }
        .status-out { background: #fff0ef; color: #cf514a; }
        .actions { display: flex; align-items: center; gap: 7px; }
        .action-link, .action-button { display: inline-flex; min-height: 31px; align-items: center; justify-content: center; gap: 5px; border: 1px solid #e7ebf2; border-radius: 7px; background: #fff; color: #526178; cursor: pointer; font-size: 11px; font-weight: 600; padding: 0 9px; }
        .action-link:hover { border-color: #cbd9f4; background: #f7f9ff; color: var(--blue); }
        .action-button { color: #c14f4c; }
        .action-button:hover { border-color: #f2cecc; background: #fff7f6; }
        .delete-form { display: inline; }
        .empty-state { padding: 55px 20px; text-align: center; }
        .empty-icon { display: grid; width: 48px; height: 48px; place-items: center; margin: 0 auto 13px; border-radius: 14px; background: #edf3ff; color: var(--blue); }
        .empty-icon svg { width: 23px; height: 23px; }
        .empty-state h2 { margin: 0; font-size: 15px; }
        .empty-state p { margin: 7px 0 18px; color: var(--muted); font-size: 13px; }
        .notice { margin-bottom: 18px; border: 1px solid; border-radius: 9px; padding: 12px 15px; font-size: 13px; }
        .notice-success { border-color: #bee9d4; background: #effaf4; color: #20754f; }
        .notice-error { border-color: #f1c9c6; background: #fff5f4; color: #a8443e; }
        .notice-error ul { margin: 7px 0 0; padding-left: 20px; }
        @media (max-width: 1050px) {
            .sidebar { width: 210px; }
            .main { margin-left: 210px; }
            .content { padding-right: 25px; padding-left: 25px; }
            .topbar { padding-right: 25px; }
            .stats-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 700px) {
            .sidebar { position: static; width: 100%; flex-direction: row; align-items: center; justify-content: space-between; border-right: 0; border-bottom: 1px solid var(--line); padding: 13px 18px; }
            .brand { padding: 0; }
            .nav-label, .sidebar-note { display: none; }
            .nav-link { padding: 9px 10px; font-size: 12px; }
            .main { margin-left: 0; }
            .topbar { min-height: 48px; padding: 0 18px; }
            .content { padding: 27px 17px 38px; }
            .page-heading { align-items: flex-start; }
            .page-heading .button { flex-shrink: 0; }
            .stats-grid { gap: 11px; }
            .stat-card { min-height: 122px; padding: 15px; }
            .stat-top { align-items: flex-start; font-size: 11px; }
            .stat-value { font-size: 24px; }
            .panel-header { align-items: flex-start; flex-direction: column; padding: 17px; }
            .search-form { width: 100%; }
            th, td { padding-right: 14px; padding-left: 14px; }
        }
        @media (max-width: 390px) {
            .brand { font-size: 17px; }
            .nav-link { gap: 6px; font-size: 11px; padding: 8px; }
            .page-heading { flex-direction: column; }
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
                <div class="page-heading">
                    <div>
                        <p class="eyebrow">Overview</p>
                        <h1>Inventory dashboard</h1>
                        <p class="subtitle">A clear view of your products and stock health.</p>
                    </div>
                    <a class="button button-primary" href="{{ route('products.create') }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                        Add product
                    </a>
                </div>

                @if (session('success'))
                    <div class="notice notice-success" role="status">{{ session('success') }}</div>
                @endif

                @if ($errors->any())
                    <div class="notice notice-error" role="alert">
                        <strong>Please check the following:</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="stats-grid">
                    <article class="stat-card">
                        <div class="stat-top"><span>Total products</span><span class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12 3 9 5-9 5-9-5 9-5Z"/><path d="m3 12 9 5 9-5M3 16l9 5 9-5"/></svg></span></div>
                        <div class="stat-value">{{ number_format($totalProducts) }}</div>
                        <div class="stat-hint">Products in your catalog</div>
                    </article>
                    <article class="stat-card">
                        <div class="stat-top"><span>Total stock quantity</span><span class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 19V5m0 14h16"/><path d="m7 14 4-4 3 3 5-6"/></svg></span></div>
                        <div class="stat-value">{{ number_format($totalStockQuantity) }}</div>
                        <div class="stat-hint">Units currently on hand</div>
                    </article>
                    <article class="stat-card">
                        <div class="stat-top"><span>Low stock products</span><span class="stat-icon amber"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3 2.8 19h18.4L12 3Z"/><path d="M12 9v4m0 3h.01"/></svg></span></div>
                        <div class="stat-value">{{ number_format($lowStockProducts) }}</div>
                        <div class="stat-hint">At or below minimum level</div>
                    </article>
                    <article class="stat-card">
                        <div class="stat-top"><span>Out of stock</span><span class="stat-icon red"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="m9 9 6 6m0-6-6 6"/></svg></span></div>
                        <div class="stat-value">{{ number_format($outOfStockProducts) }}</div>
                        <div class="stat-hint">Products that need replenishing</div>
                    </article>
                </div>

                <section class="panel" aria-labelledby="products-heading">
                    <div class="panel-header">
                        <div>
                            <h2 class="panel-title" id="products-heading">Product inventory</h2>
                            <p class="panel-caption">Search and manage your catalog.</p>
                        </div>
                        <form class="search-form" action="{{ route('products.index') }}" method="GET" role="search">
                            <label class="search-wrap">
                                <span class="sr-only" style="position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0;">Search products</span>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg>
                                <input class="search-input" type="search" name="search" value="{{ $search }}" placeholder="Search products..." maxlength="255">
                            </label>
                            <button class="button button-secondary" type="submit">Search</button>
                        </form>
                    </div>

                    @if ($products->isNotEmpty())
                        <div class="table-scroll">
                            <table>
                                <thead>
                                    <tr>
                                        <th scope="col">Product</th>
                                        <th scope="col">SKU</th>
                                        <th scope="col">Quantity</th>
                                        <th scope="col">Unit price</th>
                                        <th scope="col">Stock status</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($products as $product)
                                        @php
                                            $stockStatus = $product->quantity === 0
                                                ? 'Out of Stock'
                                                : ($product->quantity <= $product->min_stock ? 'Low Stock' : 'In Stock');
                                            $statusClass = match ($stockStatus) {
                                                'Out of Stock' => 'status-out',
                                                'Low Stock' => 'status-low',
                                                default => 'status-in',
                                            };
                                        @endphp
                                        <tr>
                                            <td>
                                                <span class="product-name">{{ $product->name }}</span>
                                                <span class="product-category">{{ $product->category ?: 'Uncategorized' }}</span>
                                            </td>
                                            <td class="sku">{{ $product->sku }}</td>
                                            <td>{{ number_format($product->quantity) }} <span style="color:#9aa4b3;">units</span></td>
                                            <td>${{ number_format((float) $product->price, 2) }}</td>
                                            <td><span class="status {{ $statusClass }}">{{ $stockStatus }}</span></td>
                                            <td>
                                                <div class="actions">
                                                    <a class="action-link" href="{{ route('products.edit', $product) }}">Edit</a>
                                                    <form class="delete-form" action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Delete this product? This action cannot be undone.');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="action-button" type="submit">Delete</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <span class="empty-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m3 7 9-4 9 4-9 4-9-4Z"/><path d="M3 7v10l9 4 9-4V7"/></svg></span>
                            <h2>{{ filled($search) ? 'No matching products' : 'Your inventory is empty' }}</h2>
                            <p>{{ filled($search) ? 'Try another search term or clear your search.' : 'Add your first product to start managing your inventory.' }}</p>
                            @if (filled($search))
                                <a class="button button-secondary" href="{{ route('products.index') }}">Clear search</a>
                            @else
                                <a class="button button-primary" href="{{ route('products.create') }}">Add your first product</a>
                            @endif
                        </div>
                    @endif
                </section>
            </section>
        </main>
    </div>
</body>
</html>

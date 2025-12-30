@extends('adminlte::page')

@section('title', 'Gestión de Envíos | Sistema de Logística')

@section('content_header')
@include('shippings.create')
@include('shippings.edit')

<div class="dashboard-header">
    <div class="header-content">
        <div class="header-title">
            <div class="title-icon">
                <i class="fas fa-shipping-fast"></i>
            </div>
            <div>
                <h1>Gestión de Envíos</h1>
                <p class="subtitle">Sistema completo de administración y seguimiento</p>
            </div>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#crearEnvioModal">
            <i class="fas fa-plus"></i>
            <span>Nuevo Envío</span>
        </button>
    </div>
</div>
@stop

@section('content')
<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card primary">
        <div class="stat-icon">
            <div class="icon-bg">
                <i class="fas fa-boxes"></i>
            </div>
        </div>
        <div class="stat-content">
            <h3>{{ $stats['total'] ?? 0 }}</h3>
            <p>Total Envíos</p>
            <div class="stat-trend">
                <i class="fas fa-chart-line"></i>
                <span>Estadística general</span>
            </div>
        </div>
    </div>

    <div class="stat-card warning">
        <div class="stat-icon">
            <div class="icon-bg">
                <i class="fas fa-clock"></i>
            </div>
        </div>
        <div class="stat-content">
            <h3>{{ $stats['solicitados'] ?? 0 }}</h3>
            <p>Solicitados</p>
            <div class="stat-trend">
                <i class="fas fa-exclamation-circle"></i>
                <span>Por procesar</span>
            </div>
        </div>
    </div>

    <div class="stat-card info">
        <div class="stat-icon">
            <div class="icon-bg">
                <i class="fas fa-truck"></i>
            </div>
        </div>
        <div class="stat-content">
            <h3>{{ $stats['en_camino'] ?? 0 }}</h3>
            <p>En Camino</p>
            <div class="stat-trend">
                <i class="fas fa-route"></i>
                <span>En movimiento</span>
            </div>
        </div>
    </div>

    <div class="stat-card success">
        <div class="stat-icon">
            <div class="icon-bg">
                <i class="fas fa-check-double"></i>
            </div>
        </div>
        <div class="stat-content">
            <h3>{{ $stats['pagados'] ?? 0 }}</h3>
            <p>Pagados</p>
            <div class="stat-trend">
                <i class="fas fa-check-circle"></i>
                <span>Procesados</span>
            </div>
        </div>
    </div>
</div>

<!-- Control Panel -->
<div class="control-panel">
    <div class="panel-header">
        <div class="panel-title">
            <i class="fas fa-table"></i>
            <h3>Tabla de Envíos</h3>
        </div>
        <div class="panel-actions">
            <!-- Acciones en Lote (oculto inicialmente) -->
            <div class="batch-actions-container" style="display: none;" id="batchActionsContainer">
                <div class="batch-selection-info">
                    <span id="selectedCount">0</span> seleccionados
                </div>
                <select class="select-custom batch-action-select" id="batchActionSelect">
                    <option value="" selected disabled>Acciones en lote</option>
                    <option value="Solicitado">Marcar como Solicitado</option>
                    <option value="Pagado">Marcar como Pagado</option>
                    <option value="En camino">Marcar como En Camino</option>
                    <option value="En terminal">Marcar como En Terminal</option>
                    <option value="Cancelado">Marcar como Cancelado</option>
                    <option value="Expirado">Marcar como Expirado</option>
                    <option value="delete" class="text-danger">Eliminar seleccionados</option>
                </select>
                <button class="btn-clear-selection" id="clearSelection">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Buscar envíos, clientes...">
            </div>
            <div class="action-buttons">
                <button class="btn-select-all" id="selectAllBtn" title="Seleccionar todos">
                    <i class="far fa-square"></i>
                </button>
                <button class="btn-filter" id="filterToggle">
                    <i class="fas fa-filter"></i>
                    Filtros
                </button>
                <button class="btn-refresh" id="refreshBtn">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="filters-panel" id="filtersPanel">
        <div class="filter-row">
            <div class="filter-group">
                <label>Estado del Envío</label>
                <select class="select-custom" id="statusFilter">
                    <option value="">Todos los estados</option>
                    <option value="Solicitado">Solicitado</option>
                    <option value="Pagado">Pagado</option>
                    <option value="En camino">En Camino</option>
                    <option value="En terminal">En Terminal</option>
                    <option value="Cancelado">Cancelado</option>
                    <option value="Expirado">Expirado</option>
                </select>
            </div>

            <div class="filter-group">
                <label>Unidad</label>
                <select class="select-custom" id="unitFilter">
                    <option value="">Todas las unidades</option>
                    @foreach($unitIds as $unitId)
                        <option value="{{ $unitId }}">Unidad #{{ $unitId }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label>Ordenar por</label>
                <select class="select-custom" id="sortSelect">
                    <option value="newest">Más recientes primero</option>
                    <option value="oldest">Más antiguos primero</option>
                    <option value="amount_high">Mayor monto</option>
                    <option value="amount_low">Menor monto</option>
                    <option value="status">Estado</option>
                </select>
            </div>
        </div>

        <div class="filter-row">
            <div class="filter-group">
                <label>Rango de Monto</label>
                <div class="range-inputs">
                    <input type="number" class="range-input" id="minAmount" placeholder="Mínimo">
                    <span>a</span>
                    <input type="number" class="range-input" id="maxAmount" placeholder="Máximo">
                </div>
            </div>

            <div class="filter-group">
                <label>Filas por página</label>
                <select class="select-custom" id="perPageSelect">
                    <option value="10">10</option>
                    <option value="15" selected>15</option>
                    <option value="20">20</option>
                    <option value="30">30</option>
                    <option value="50">50</option>
                </select>
            </div>

            <div class="filter-group">
                <label>&nbsp;</label>
                <button class="btn-clear-filters" id="clearFilters">
                    <i class="fas fa-times"></i>
                    Limpiar Filtros
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Main Table -->
<div class="table-container">
    <div class="table-responsive">
        <table class="crud-table" id="shippingsTable">
            <thead>
                <tr>
                    <th class="col-checkbox">
                        <div class="th-content">
                            <input type="checkbox" id="selectAllHeader">
                        </div>
                    </th>
                    <th class="col-folio" data-sort="folio">
                        <div class="th-content">
                            <span>Folio</span>
                            <i class="fas fa-sort"></i>
                        </div>
                    </th>
                    <th class="col-cliente" data-sort="cliente">
                        <div class="th-content">
                            <span>Cliente</span>
                            <i class="fas fa-sort"></i>
                        </div>
                    </th>
                    <th class="col-sitio" data-sort="sitio">
                        <div class="th-content">
                            <span>Sitio</span>
                            <i class="fas fa-sort"></i>
                        </div>
                    </th>
                    <th class="col-destinatario" data-sort="destinatario">
                        <div class="th-content">
                            <span>Destinatario</span>
                            <i class="fas fa-sort"></i>
                        </div>
                    </th>
                    <th class="col-status" data-sort="status">
                        <div class="th-content">
                            <span>Estado</span>
                            <i class="fas fa-sort"></i>
                        </div>
                    </th>
                    <th class="col-unidad" data-sort="unidad">
                        <div class="th-content">
                            <span>Unidad/Ruta</span>
                            <i class="fas fa-sort"></i>
                        </div>
                    </th>
                    <th class="col-dimensiones" data-sort="dimensiones">
                        <div class="th-content">
                            <span>Dimensiones</span>
                            <i class="fas fa-sort"></i>
                        </div>
                    </th>
                    <th class="col-peso" data-sort="peso">
                        <div class="th-content">
                            <span>Peso</span>
                            <i class="fas fa-sort"></i>
                        </div>
                    </th>
                    <th class="col-monto" data-sort="monto">
                        <div class="th-content">
                            <span>Monto</span>
                            <i class="fas fa-sort"></i>
                        </div>
                    </th>
                    <th class="col-imagen">
                        <div class="th-content">
                            <span>Imagen</span>
                        </div>
                    </th>
                    <th class="col-fecha" data-sort="fecha">
                        <div class="th-content">
                            <span>Fecha</span>
                            <i class="fas fa-sort"></i>
                        </div>
                    </th>
                    <th class="col-actions">
                        <div class="th-content">
                            <span>Acciones</span>
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($shippings as $shipping)
                <tr class="envio-row"
                    data-id="{{ $shipping->id }}"
                    data-status="{{ $shipping->status }}"
                    data-unit="{{ $shipping->routeUnitSchedule ? $shipping->routeUnitSchedule->unit_id : '' }}"
                    data-amount="{{ $shipping->amount }}"
                    data-created="{{ $shipping->created_at->timestamp }}">

                    <!-- CHECKBOX -->
                    <td class="col-checkbox">
                        <input type="checkbox" class="row-checkbox" value="{{ $shipping->id }}">
                    </td>

                    <!-- FOLIO -->
                    <td class="col-folio">
                        <div class="envio-id">{{ $shipping->folio ?? 'N/A' }}</div>
                    </td>

                    <!-- CLIENTE -->
                    <td class="col-cliente">
                        <div class="user-info">
                            <div class="user-avatar sender">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="user-details">
                                <span class="user-name">{{ $shipping->user->name ?? 'No asignado' }}</span>
                                <small class="text-muted">{{ $shipping->user->email ?? '' }}</small>
                            </div>
                        </div>
                    </td>

                    <!-- SITIO -->
                    <td class="col-sitio">
                        <div class="site-info">
                            <span class="site-name">{{ $shipping->site->name ?? 'No especificado' }}</span>
                        </div>
                    </td>

                    <!-- DESTINATARIO -->
                    <td class="col-destinatario">
                        <div class="receiver-info">
                            <span class="receiver-name">{{ $shipping->receiver_name }}</span>
                            <small class="text-muted">{{ Str::limit($shipping->receiver_description, 30) }}</small>
                        </div>
                    </td>

                    <!-- ESTADO -->
                    <td class="col-status">
                        <div class="status-indicator status-{{ strtolower(str_replace(' ', '-', $shipping->status)) }}">
                            <span class="status-dot"></span>
                            <span class="status-text">{{ $shipping->status }}</span>
                        </div>
                    </td>

                    <!-- UNIDAD/RUTA -->
                    <td class="col-unidad">
                        @if($shipping->routeUnitSchedule)
                        <div class="unit-info">
                            <small>
                                Unidad #{{ $shipping->routeUnitSchedule->unit_id ?? 'N/A' }}
                            </small>
                            <br>
                            <small class="text-muted">Ruta asignada</small>
                        </div>
                        @else
                        <span class="no-assignment">No asignado</span>
                        @endif
                    </td>

                    <!-- DIMENSIONES -->
                    <td class="col-dimensiones">
                        <div class="dimensions">
                            <small>{{ $shipping->length_cm }}×{{ $shipping->width_cm }}×{{ $shipping->height_cm }} cm</small>
                            @if($shipping->fragile)
                            <br><span class="fragile-badge"><i class="fas fa-exclamation-triangle"></i> Frágil</span>
                            @endif
                        </div>
                    </td>

                    <!-- PESO -->
                    <td class="col-peso">
                        <div class="weight">
                            <span>{{ $shipping->weight_kg }} kg</span>
                        </div>
                    </td>

                    <!-- MONTO -->
                    <td class="col-monto">
                        <div class="amount-cell">
                            <span class="amount">${{ number_format($shipping->amount, 2) }}</span>
                        </div>
                    </td>

                    <!-- IMAGEN -->
                    <td class="col-imagen">
                        <div class="image-cell">
                            @if ($shipping->package_image)
                                <div class="image-thumbnail" onclick="showImageModal('{{ asset('storage/' . $shipping->package_image) }}', {{ $shipping->id }})">
                                    <img src="{{ asset('storage/' . $shipping->package_image) }}"
                                        alt="Imagen del paquete" loading="lazy">
                                    <div class="image-overlay">
                                        <i class="fas fa-search-plus"></i>
                                    </div>
                                </div>
                            @else
                                <span class="no-image">-</span>
                            @endif
                        </div>
                    </td>

                    <!-- FECHA -->
                    <td class="col-fecha">
                        <div class="date-cell">
                            <small>{{ $shipping->created_at->format('d/m/Y') }}</small>
                            <br>
                            <small class="text-muted">{{ $shipping->created_at->format('H:i') }}</small>
                        </div>
                    </td>

                    <!-- ACCIONES -->
                    <td class="col-actions">
                        <div class="action-buttons">
                            <a href="javascript:void(0)"
                            class="btn-action btn-edit"
                            data-bs-toggle="modal"
                            data-bs-target="#editarEnvioModal"
                            data-shipping-id="{{ $shipping->id }}">
                                <i class="fas fa-edit"></i>
                            </a>

                            <button class="btn-action btn-delete"
                                    data-shipping-id="{{ $shipping->id }}"
                                    data-shipping-folio="{{ $shipping->folio }}"
                                    onclick="confirmDelete(this)"
                                    title="Eliminar envío">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="13" class="text-center py-4">
                        <div class="no-data">
                            <i class="fas fa-box-open fa-2x text-muted mb-3"></i>
                            <p class="mb-0">No hay envíos registrados</p>
                            <button class="btn btn-sm btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#crearEnvioModal">
                                <i class="fas fa-plus"></i> Crear primer envío
                            </button>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Table Footer -->
    <div class="table-footer">
        <div class="footer-info">
            <div class="total-count">
                Mostrando <span id="visibleCount">{{ $shippings->count() }}</span> de <span id="totalCount">{{ $stats['total'] ?? 0 }}</span> envíos
            </div>
        </div>

        <!-- Paginación -->
        @if($shippings->hasPages())
        <div class="pagination-wrapper">
            {{ $shippings->links('pagination::bootstrap-4') }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Imagen -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content image-modal">
            <div class="modal-header">
                <h5 class="modal-title">Imagen del Paquete - Envío #<span id="modalShippingId"></span></h5>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="image-container">
                    <img id="modalImage" src="" alt="Imagen del paquete" class="modal-image">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cerrar
                </button>
                <a href="#" id="downloadImage" class="btn btn-primary" download>
                    <i class="fas fa-download"></i> Descargar
                </a>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<style>
    :root {
        --primary: #f59e0b;
        --primary-dark: #f59e0b;
        --success: #10b981;
        --warning: #f59e0b;
        --info: #3b82f6;
        --danger: #ef4444;
        --dark: #1f2937;
        --light: #f8fafc;
        --gray-100: #f3f4f6;
        --gray-200: #e5e7eb;
        --gray-300: #d1d5db;
        --gray-400: #9ca3af;
        --gray-500: #6b7280;
        --gray-600: #4b5563;
        --gray-700: #374151;
        --gray-800: #1f2937;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        --radius: 8px;
        --radius-lg: 12px;
    }

    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    /* Header Styles */
    .dashboard-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        padding: 2rem 0;
        margin: -1rem -1rem 2rem -1rem;
        box-shadow: var(--shadow-lg);
    }

    .header-content {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .header-title {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .title-icon {
        width: 60px;
        height: 60px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(10px);
    }

    .title-icon i {
        font-size: 1.8rem;
    }

    .header-title h1 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .subtitle {
        opacity: 0.9;
        font-weight: 400;
    }

    .btn-create {
        background: rgba(255, 255, 255, 0.2);
        border: 2px solid rgba(255, 255, 255, 0.3);
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: var(--radius);
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
        text-decoration: none;
    }

    .btn-create:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        border-radius: var(--radius-lg);
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        box-shadow: var(--shadow);
        transition: all 0.3s ease;
        border: 1px solid var(--gray-200);
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
    }

    .stat-card.primary::before { background: var(--primary); }
    .stat-card.warning::before { background: var(--warning); }
    .stat-card.info::before { background: var(--info); }
    .stat-card.success::before { background: var(--success); }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-xl);
    }

    .stat-icon {
        flex-shrink: 0;
    }

    .icon-bg {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .primary .icon-bg { background: rgba(99, 102, 241, 0.1); color: var(--primary); }
    .warning .icon-bg { background: rgba(245, 158, 11, 0.1); color: var(--warning); }
    .info .icon-bg { background: rgba(59, 130, 246, 0.1); color: var(--info); }
    .success .icon-bg { background: rgba(16, 185, 129, 0.1); color: var(--success); }

    .stat-content h3 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .stat-content p {
        color: var(--gray-600);
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .stat-trend {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        color: var(--gray-500);
    }

    /* Control Panel */
    .control-panel {
        background: white;
        border-radius: var(--radius-lg);
        margin-bottom: 2rem;
        box-shadow: var(--shadow);
        border: 1px solid var(--gray-200);
    }

    .panel-header {
        padding: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid var(--gray-200);
    }

    .panel-title {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-weight: 600;
        color: var(--gray-800);
    }

    .panel-title i {
        color: var(--primary);
    }

    .panel-actions {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    /* Acciones en Lote */
    .batch-actions-container {
        display: flex;
        align-items: center;
        gap: 1rem;
        background: rgba(99, 102, 241, 0.1);
        padding: 0.5rem 1rem;
        border-radius: 8px;
        border: 1px solid rgba(99, 102, 241, 0.2);
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .batch-selection-info {
        font-weight: 600;
        color: var(--primary);
        font-size: 0.9rem;
        white-space: nowrap;
    }

    .batch-action-select {
        min-width: 200px;
        background: white;
        border-color: var(--primary);
    }

    .btn-clear-selection {
        background: none;
        border: none;
        color: var(--gray-500);
        cursor: pointer;
        padding: 0.25rem;
        border-radius: 4px;
        transition: all 0.2s ease;
    }

    .btn-clear-selection:hover {
        color: var(--danger);
        background: rgba(239, 68, 68, 0.1);
    }

    .btn-select-all {
        padding: 0.5rem;
        border: 1px solid var(--gray-300);
        background: white;
        border-radius: 8px;
        color: var(--gray-700);
        cursor: pointer;
        transition: all 0.2s ease;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-select-all:hover {
        background: var(--gray-100);
        border-color: var(--gray-400);
    }

    .btn-select-all.active {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    .search-box {
        position: relative;
        background: var(--gray-100);
        border-radius: 8px;
        padding: 0.5rem 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        min-width: 300px;
    }

    .search-box i {
        color: var(--gray-500);
    }

    .search-box input {
        border: none;
        background: none;
        outline: none;
        width: 100%;
        font-size: 0.9rem;
    }

    .action-buttons {
        display: flex;
        gap: 0.75rem;
    }

    .btn-filter, .btn-export, .btn-refresh {
        padding: 0.5rem 1rem;
        border: 1px solid var(--gray-300);
        background: white;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }

    .btn-refresh {
        padding: 0.5rem;
    }

    .btn-filter:hover, .btn-export:hover, .btn-refresh:hover {
        background: var(--gray-100);
        border-color: var(--gray-400);
    }

    /* Filters Panel */
    .filters-panel {
        padding: 1.5rem;
        border-top: 1px solid var(--gray-200);
        display: none;
    }

    .filters-panel.show {
        display: block;
        animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .filter-row {
        display: flex;
        gap: 2rem;
        margin-bottom: 1.5rem;
    }

    .filter-row:last-child {
        margin-bottom: 0;
    }

    .filter-group {
        flex: 1;
    }

    .filter-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: var(--gray-700);
        font-size: 0.875rem;
    }

    .select-custom {
        padding: 0.5rem 1rem;
        border: 1px solid var(--gray-300);
        border-radius: 8px;
        background: white;
        font-size: 0.9rem;
        width: 100%;
    }

    .range-inputs {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .range-input {
        padding: 0.5rem;
        border: 1px solid var(--gray-300);
        border-radius: 6px;
        font-size: 0.9rem;
        flex: 1;
    }

    .btn-clear-filters {
        padding: 0.5rem 1rem;
        background: var(--gray-100);
        border: 1px solid var(--gray-300);
        border-radius: 8px;
        color: var(--gray-700);
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-clear-filters:hover {
        background: var(--gray-200);
    }

    /* Table Container */
    .table-container {
        background: white;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow);
        border: 1px solid var(--gray-200);
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .table-responsive {
        overflow-x: auto;
    }

    /* CRUD Table */
    .crud-table {
        width: 100%;
        border-collapse: collapse;
        background: white;
    }

    .crud-table thead {
        background: linear-gradient(135deg, var(--gray-50) 0%, var(--gray-100) 100%);
        border-bottom: 2px solid var(--gray-200);
    }

    .crud-table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: var(--gray-700);
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid var(--gray-200);
        white-space: nowrap;
        vertical-align: middle;
    }

    .th-content {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .th-content i {
        color: var(--gray-400);
        font-size: 0.75rem;
        opacity: 0.6;
    }

    .crud-table td {
        padding: 1rem;
        border-bottom: 1px solid var(--gray-200);
        vertical-align: middle;
    }

    .envio-row:hover {
        background: var(--gray-50);
    }

    .envio-row.selected {
        background: rgba(99, 102, 241, 0.05) !important;
    }

    /* Column Widths */
    .col-checkbox { width: 50px; }
    .col-folio { width: 140px; }
    .col-cliente { width: 220px; }
    .col-sitio { width: 150px; }
    .col-destinatario { width: 200px; }
    .col-status { width: 140px; }
    .col-unidad { width: 150px; }
    .col-dimensiones { width: 140px; }
    .col-peso { width: 100px; }
    .col-monto { width: 120px; }
    .col-imagen { width: 100px; }
    .col-fecha { width: 120px; }
    .col-actions { width: 100px; }

    /* Checkbox Styles */
    .row-checkbox {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: var(--primary);
    }

    #selectAllHeader {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: var(--primary);
    }

    /* Table Content Styles */
    .envio-id {
        font-weight: 700;
        color: var(--gray-800);
        font-size: 0.875rem;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        flex-shrink: 0;
    }

    .user-avatar.sender { background: rgba(99, 102, 241, 0.1); color: var(--primary); }

    .user-details {
        display: flex;
        flex-direction: column;
    }

    .user-name {
        font-weight: 600;
        color: var(--gray-800);
        font-size: 0.875rem;
    }

    .user-details small {
        color: var(--gray-500);
        font-size: 0.75rem;
    }

    .site-info,
    .receiver-info {
        display: flex;
        flex-direction: column;
    }

    .site-name {
        font-weight: 600;
        color: var(--gray-800);
        font-size: 0.875rem;
    }

    .receiver-name {
        font-weight: 600;
        color: var(--gray-800);
        font-size: 0.875rem;
    }

    .receiver-info small {
        color: var(--gray-500);
        font-size: 0.75rem;
        margin-top: 0.125rem;
    }

    .status-indicator {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-solicitado { background: #fef3c7; color: #92400e; }
    .status-pagado { background: #d1fae5; color: #065f46; }
    .status-en-camino { background: #dbeafe; color: #1e40af; }
    .status-en-terminal { background: #e0e7ff; color: #3730a3; }
    .status-cancelado { background: #fee2e2; color: #991b1b; }
    .status-expirado { background: #f3f4f6; color: #6b7280; }

    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    .status-solicitado .status-dot { background: var(--warning); }
    .status-pagado .status-dot { background: var(--success); }
    .status-en-camino .status-dot { background: var(--info); }
    .status-en-terminal .status-dot { background: var(--info); }
    .status-cancelado .status-dot { background: var(--danger); }
    .status-expirado .status-dot { background: var(--gray-400); }

    .unit-info {
        display: flex;
        flex-direction: column;
    }

    .unit-info small {
        font-size: 0.875rem;
        color: var(--gray-800);
    }

    .unit-info small:last-child {
        color: var(--gray-500);
        font-size: 0.75rem;
        margin-top: 0.125rem;
    }

    .no-assignment {
        color: var(--gray-400);
        font-style: italic;
        font-size: 0.875rem;
    }

    .dimensions {
        display: flex;
        flex-direction: column;
    }

    .dimensions small {
        font-size: 0.875rem;
        color: var(--gray-800);
    }

    .fragile-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        font-size: 0.75rem;
        color: #d97706;
        background: rgba(245, 158, 11, 0.1);
        padding: 0.125rem 0.5rem;
        border-radius: 12px;
        margin-top: 0.25rem;
        width: fit-content;
    }

    .weight {
        font-weight: 600;
        color: var(--gray-800);
        font-size: 0.875rem;
    }

    .amount-cell .amount {
        font-weight: 700;
        color: var(--gray-800);
        font-size: 0.875rem;
    }

    .image-cell {
        display: flex;
        justify-content: center;
    }

    .image-thumbnail {
        position: relative;
        width: 48px;
        height: 48px;
        border-radius: 6px;
        overflow: hidden;
        cursor: pointer;
        transition: transform 0.2s ease;
    }

    .image-thumbnail:hover {
        transform: scale(1.1);
    }

    .image-thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .image-thumbnail:hover .image-overlay {
        opacity: 1;
    }

    .image-overlay i {
        color: white;
        font-size: 0.875rem;
    }

    .no-image {
        color: var(--gray-400);
        font-style: italic;
        font-size: 0.875rem;
    }

    .date-cell {
        display: flex;
        flex-direction: column;
    }

    .date-cell small {
        font-size: 0.875rem;
        color: var(--gray-800);
    }

    .date-cell small:last-child {
        color: var(--gray-500);
        font-size: 0.75rem;
        margin-top: 0.125rem;
    }

    .action-buttons {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-action {
        width: 32px;
        height: 32px;
        border: none;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        cursor: pointer;
        font-size: 0.875rem;
    }

    .btn-edit {
        background: rgba(59, 130, 246, 0.1);
        color: var(--info);
    }

    .btn-edit:hover {
        background: var(--info);
        color: white;
    }

    .btn-delete {
        background: rgba(239, 68, 68, 0.1);
        color: var(--danger);
    }

    .btn-delete:hover {
        background: var(--danger);
        color: white;
    }

    /* Table Footer */
    .table-footer {
        padding: 1rem 1.5rem;
        background: var(--gray-50);
        border-top: 1px solid var(--gray-200);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .footer-info {
        display: flex;
        align-items: center;
        gap: 2rem;
    }

    .total-count {
        font-size: 0.875rem;
        color: var(--gray-600);
    }

    .total-count span {
        font-weight: 700;
        color: var(--gray-800);
    }

    .pagination-wrapper {
        display: flex;
        justify-content: center;
    }

    .pagination-wrapper .pagination {
        margin-bottom: 0;
    }

    .pagination-wrapper .page-item .page-link {
        border-color: var(--gray-300);
        color: var(--gray-700);
        font-size: 0.875rem;
        padding: 0.375rem 0.75rem;
    }

    .pagination-wrapper .page-item.active .page-link {
        background: var(--primary);
        border-color: var(--primary);
    }

    .pagination-wrapper .page-item.disabled .page-link {
        color: var(--gray-400);
    }

    .no-data {
        padding: 2rem;
        text-align: center;
    }

    .no-data i {
        color: var(--gray-400);
        margin-bottom: 1rem;
    }

    .no-data p {
        color: var(--gray-600);
        margin-bottom: 1rem;
    }

    .no-data .btn {
        background: var(--primary);
        border-color: var(--primary);
        color: white;
    }

    .no-data .btn:hover {
        background: var(--primary-dark);
        border-color: var(--primary-dark);
    }

    /* Image Modal Styles */
    .image-modal .modal-content {
        border: none;
        border-radius: var(--radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-xl);
    }

    .image-modal .modal-header {
        background: var(--gray-50);
        border-bottom: 1px solid var(--gray-200);
        padding: 1.25rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .image-modal .modal-body {
        padding: 0;
        background: transparent;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 400px;
    }

    .image-container {
        max-width: 100%;
        max-height: 70vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .modal-image {
        max-width: 100%;
        max-height: 70vh;
        width: auto;
        height: auto;
        border-radius: 8px;
    }

    .image-modal .modal-footer {
        background: var(--gray-50);
        border-top: 1px solid var(--gray-200);
        padding: 1rem 1.5rem;
    }

    /* X negra sin fondo */
    .btn-close-custom {
        background: none !important;
        border: none !important;
        font-size: 1.5rem;
        color: #000 !important;
        opacity: 0.7;
        transition: opacity 0.3s ease;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .btn-close-custom:hover {
        opacity: 1;
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .filter-row {
            flex-direction: column;
            gap: 1rem;
        }

        .panel-actions {
            flex-direction: column;
            align-items: stretch;
            gap: 1rem;
        }

        .search-box {
            min-width: auto;
        }

        .batch-actions-container {
            order: -1;
        }
    }

    @media (max-width: 768px) {
        .header-content {
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .table-footer {
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start;
        }

        .image-modal .modal-body {
            padding: 1rem;
            min-height: 300px;
        }

        .col-checkbox { width: 40px; }
        .col-folio { width: 120px; }
        .col-cliente { width: 180px; }
        .col-destinatario { width: 160px; }
        .col-dimensiones { width: 120px; }

        .batch-action-select {
            min-width: 150px;
        }
    }

    @media (max-width: 576px) {
        .dashboard-header {
            padding: 1.5rem 0;
            margin: -1rem -0.5rem 1.5rem -0.5rem;
        }

        .header-content {
            padding: 0 1rem;
        }

        .stats-grid,
        .control-panel,
        .table-container {
            margin-left: 0.5rem;
            margin-right: 0.5rem;
        }

        .panel-header {
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start;
        }

        .panel-actions {
            width: 100%;
            flex-wrap: wrap;
        }

        .batch-actions-container {
            width: 100%;
            justify-content: space-between;
        }

        .search-box {
            width: 100%;
            min-width: auto;
        }

        .action-buttons {
            width: 100%;
            justify-content: flex-end;
        }

        .crud-table th,
        .crud-table td {
            padding: 0.75rem 0.5rem;
            font-size: 0.8125rem;
        }

        .user-avatar {
            width: 28px;
            height: 28px;
            font-size: 0.75rem;
        }

        .image-thumbnail {
            width: 40px;
            height: 40px;
        }

        .btn-action {
            width: 28px;
            height: 28px;
            font-size: 0.75rem;
        }

        .batch-selection-info {
            font-size: 0.8rem;
        }
    }
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        // CSRF Token para todas las peticiones AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Variables para selección
        let selectedIds = new Set();
        let allSelected = false;

        // Inicializar eventos de selección
        initializeSelectionEvents();

        // Toggle filters panel
        $('#filterToggle').on('click', function() {
            $('#filtersPanel').toggleClass('show');
            $(this).toggleClass('active');
            $(this).html($(this).hasClass('active') ?
                '<i class="fas fa-filter"></i> Ocultar Filtros' :
                '<i class="fas fa-filter"></i> Filtros');
        });

        // Search functionality
        $('#searchInput').on('input', function() {
            filterTable();
        });

        // Filter functionality
        $('#statusFilter, #unitFilter, #sortSelect, #minAmount, #maxAmount').on('change', function() {
            filterTable();
        });

        // Clear filters
        $('#clearFilters').on('click', function() {
            $('#statusFilter, #unitFilter, #sortSelect').val('');
            $('#minAmount, #maxAmount').val('');
            $('#searchInput').val('');
            filterTable();
        });

        // Filter table function
        function filterTable() {
            const status = $('#statusFilter').val();
            const unit = $('#unitFilter').val();
            const search = $('#searchInput').val().toLowerCase();
            const minAmount = $('#minAmount').val();
            const maxAmount = $('#maxAmount').val();
            const sortBy = $('#sortSelect').val();

            let visibleCount = 0;

            $('.envio-row').each(function() {
                const row = $(this);
                const rowStatus = row.data('status');
                const rowUnit = row.data('unit').toString();
                const rowAmount = parseFloat(row.data('amount')) || 0;
                const rowText = row.text().toLowerCase();

                const statusMatch = !status || rowStatus === status;
                const unitMatch = !unit || rowUnit === unit;
                const searchMatch = !search || rowText.includes(search);
                const minAmountMatch = !minAmount || rowAmount >= parseFloat(minAmount);
                const maxAmountMatch = !maxAmount || rowAmount <= parseFloat(maxAmount);

                if (statusMatch && unitMatch && searchMatch && minAmountMatch && maxAmountMatch) {
                    row.show();
                    visibleCount++;
                } else {
                    row.hide();
                }
            });

            $('#visibleCount').text(visibleCount);
            applySorting(sortBy);
            updateSelectionCount();
        }

        // Apply sorting
        function applySorting(sortBy) {
            const $tbody = $('#shippingsTable tbody');
            const $rows = $('.envio-row:visible').get();

            $rows.sort(function(a, b) {
                const aRow = $(a);
                const bRow = $(b);

                switch(sortBy) {
                    case 'newest':
                        return parseInt(bRow.data('id')) - parseInt(aRow.data('id'));
                    case 'oldest':
                        return parseInt(aRow.data('id')) - parseInt(bRow.data('id'));
                    case 'amount_high':
                        return (parseFloat(bRow.data('amount')) || 0) - (parseFloat(aRow.data('amount')) || 0);
                    case 'amount_low':
                        return (parseFloat(aRow.data('amount')) || 0) - (parseFloat(bRow.data('amount')) || 0);
                    case 'status':
                        return aRow.data('status').localeCompare(bRow.data('status'));
                    default:
                        return 0;
                }
            });

            $.each($rows, function(index, row) {
                $tbody.append(row);
            });
        }

        // Refresh button
        $('#refreshBtn').on('click', function() {
            const $btn = $(this);
            $btn.addClass('loading');
            $btn.find('i').addClass('fa-spin');

            // Recargar la página para obtener datos actualizados
            setTimeout(() => {
                location.reload();
            }, 500);
        });

        // Efecto hover en filas de la tabla
        $(document).on('mouseenter', '.envio-row', function() {
            if (!$(this).hasClass('selected')) {
                $(this).css('background', 'var(--gray-50)');
            }
        }).on('mouseleave', '.envio-row', function() {
            if (!$(this).hasClass('selected')) {
                $(this).css('background', 'white');
            }
        });

        // Cambiar número de filas por página
        $('#perPageSelect').on('change', function() {
            const perPage = $(this).val();
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('per_page', perPage);
            window.location.href = currentUrl.toString();
        });

        // =============== FUNCIONES DE SELECCIÓN Y ACCIONES EN LOTE ===============

        function initializeSelectionEvents() {
            // Seleccionar fila individual
            $(document).on('change', '.row-checkbox', function() {
                const row = $(this).closest('.envio-row');
                const id = $(this).val();

                if ($(this).is(':checked')) {
                    selectedIds.add(id);
                    row.addClass('selected');
                } else {
                    selectedIds.delete(id);
                    row.removeClass('selected');
                }

                updateSelectionCount();
            });

            // Seleccionar/deseleccionar todos (checkbox header)
            $('#selectAllHeader').on('change', function() {
                allSelected = $(this).is(':checked');

                $('.envio-row:visible').each(function() {
                    const checkbox = $(this).find('.row-checkbox');
                    const id = checkbox.val();

                    if (allSelected) {
                        checkbox.prop('checked', true);
                        selectedIds.add(id);
                        $(this).addClass('selected');
                    } else {
                        checkbox.prop('checked', false);
                        selectedIds.delete(id);
                        $(this).removeClass('selected');
                    }
                });

                updateSelectionCount();
            });

            // Botón de seleccionar todos
            $('#selectAllBtn').on('click', function() {
                const btn = $(this);
                const icon = btn.find('i');

                if (btn.hasClass('active')) {
                    // Deseleccionar todos
                    $('.row-checkbox:visible').prop('checked', false).trigger('change');
                    btn.removeClass('active');
                    icon.removeClass('fa-check-square').addClass('fa-square');
                } else {
                    // Seleccionar todos visibles
                    $('.envio-row:visible').each(function() {
                        const checkbox = $(this).find('.row-checkbox');
                        const id = checkbox.val();

                        checkbox.prop('checked', true);
                        selectedIds.add(id);
                        $(this).addClass('selected');
                    });

                    btn.addClass('active');
                    icon.removeClass('fa-square').addClass('fa-check-square');
                }

                updateSelectionCount();
            });

            // Limpiar selección
            $('#clearSelection').on('click', function() {
                clearSelection();
            });

            // Acción en lote
            $('#batchActionSelect').on('change', function() {
                const action = $(this).val();
                if (!action) return;

                if (selectedIds.size === 0) {
                    showNotification('Por favor selecciona al menos un envío', 'warning');
                    $(this).val('');
                    return;
                }

                executeBatchAction(action);
                $(this).val('');
            });
        }

        function updateSelectionCount() {
            const count = selectedIds.size;
            $('#selectedCount').text(count);

            if (count > 0) {
                $('#batchActionsContainer').fadeIn(300);
                $('#selectAllBtn').addClass('active');
                $('#selectAllBtn').find('i').removeClass('fa-square').addClass('fa-check-square');
            } else {
                $('#batchActionsContainer').fadeOut(300);
                $('#selectAllBtn').removeClass('active');
                $('#selectAllBtn').find('i').removeClass('fa-check-square').addClass('fa-square');
            }

            // Actualizar checkbox header
            const visibleRows = $('.envio-row:visible').length;
            const checkedVisibleRows = $('.envio-row:visible .row-checkbox:checked').length;

            if (checkedVisibleRows === visibleRows && visibleRows > 0) {
                $('#selectAllHeader').prop('checked', true);
            } else {
                $('#selectAllHeader').prop('checked', false);
            }
        }

        function clearSelection() {
            selectedIds.clear();
            $('.row-checkbox').prop('checked', false);
            $('.envio-row').removeClass('selected');
            $('#selectAllHeader').prop('checked', false);
            $('#selectAllBtn').removeClass('active');
            $('#selectAllBtn').find('i').removeClass('fa-check-square').addClass('fa-square');
            updateSelectionCount();
        }

        function executeBatchAction(action) {
            const ids = Array.from(selectedIds);

            if (action === 'delete') {
                // Confirmación para eliminar
                Swal.fire({
                    title: '¿Eliminar envíos seleccionados?',
                    html: `Estás a punto de eliminar <strong>${ids.length}</strong> envío(s)<br>Esta acción no se puede deshacer.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    reverseButtons: true,
                    focusCancel: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        performBulkAction('delete', ids);
                    } else {
                        $('#batchActionSelect').val('');
                    }
                });
            } else {
                // Confirmación para cambiar estado
                Swal.fire({
                    title: '¿Cambiar estado de envíos?',
                    html: `¿Deseas cambiar el estado de <strong>${ids.length}</strong> envío(s) a <strong>${action}</strong>?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, cambiar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#6b7280',
                    reverseButtons: true,
                    focusCancel: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        performBulkAction(action, ids);
                    } else {
                        $('#batchActionSelect').val('');
                    }
                });
            }
        }

        function performBulkAction(action, ids) {
            Swal.fire({
                title: 'Procesando...',
                text: 'Por favor espere',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '/shippings/bulk-action',
                method: 'POST',
                data: {
                    action: action,
                    ids: ids
                },
                success: function(response) {
                    Swal.close();

                    if (response.success) {
                        // Mostrar mensaje de éxito
                        showNotification(response.message, 'success');

                        // Actualizar estadísticas si están disponibles
                        if (response.stats) {
                            updateStats(response.stats);
                        }

                        // Si fue eliminación, remover filas
                        if (action === 'delete') {
                            ids.forEach(id => {
                                $(`.envio-row[data-id="${id}"]`).remove();
                            });
                        } else {
                            // Si fue cambio de estado, actualizar filas
                            ids.forEach(id => {
                                const row = $(`.envio-row[data-id="${id}"]`);
                                const statusCell = row.find('.status-indicator');

                                // Actualizar datos de la fila
                                row.attr('data-status', action);

                                // Actualizar visual del estado
                                statusCell.removeClass().addClass(`status-indicator status-${action.toLowerCase().replace(' ', '-')}`);
                                statusCell.find('.status-text').text(action);

                                // Actualizar punto de estado
                                const dot = statusCell.find('.status-dot');
                                dot.css('background', getStatusColor(action));
                            });
                        }

                        // Limpiar selección
                        clearSelection();

                        // Actualizar contadores
                        const visibleCount = $('.envio-row:visible').length;
                        $('#visibleCount').text(visibleCount);

                    } else {
                        showNotification('Error al procesar la acción', 'error');
                    }
                },
                error: function(xhr) {
                    Swal.close();
                    let errorMessage = 'Error al procesar la acción';

                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }

                    showNotification(errorMessage, 'error');
                    $('#batchActionSelect').val('');
                }
            });
        }

        function getStatusColor(status) {
            const colors = {
                'Solicitado': '#f59e0b',
                'Pagado': '#10b981',
                'En camino': '#3b82f6',
                'En terminal': '#3b82f6',
                'Cancelado': '#ef4444',
                'Expirado': '#6b7280'
            };
            return colors[status] || '#6b7280';
        }

        function updateStats(stats) {
            // Actualizar las tarjetas de estadísticas
            $('.stat-card.primary h3').text(stats.total || 0);
            $('.stat-card.warning h3').text(stats.solicitados || 0);
            $('.stat-card.info h3').text(stats.en_camino || 0);
            $('.stat-card.success h3').text(stats.pagados || 0);
        }

        // Initial filter application
        filterTable();
    });

    // Función para mostrar imagen en modal
    function showImageModal(imageSrc, shippingId) {
        // Actualizar contenido del modal
        $('#modalImage').attr('src', imageSrc);
        $('#modalShippingId').text(shippingId);

        // Configurar enlace de descarga
        $('#downloadImage').attr('href', imageSrc);
        $('#downloadImage').attr('download', `envio-${shippingId}-imagen.jpg`);

        // Mostrar modal
        const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
        imageModal.show();
    }

    // Función para eliminar envío (individual)
    function confirmDelete(button) {
        const shippingId = $(button).data('shipping-id');
        const shippingFolio = $(button).data('shipping-folio');

        Swal.fire({
            title: '¿Eliminar envío?',
            html: `Estás a punto de eliminar el envío <strong>${shippingFolio || 'N/A'}</strong><br>Esta acción no se puede deshacer.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            reverseButtons: true,
            focusCancel: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Mostrar loading
                Swal.fire({
                    title: 'Eliminando...',
                    text: 'Por favor espere',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Enviar petición AJAX al servidor
                $.ajax({
                    url: '/shippings/' + shippingId,
                    method: 'DELETE',
                    success: function(response) {
                        Swal.close();

                        if (response.success) {
                            // Eliminar fila de la tabla
                            $(button).closest('tr').remove();

                            // Actualizar contador visible
                            const visibleCount = $('.envio-row:visible').length;
                            $('#visibleCount').text(visibleCount);

                            // Mostrar notificación
                            showNotification(response.message, 'success');

                            // Recargar la página después de 1.5 segundos para actualizar estadísticas
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        } else {
                            showNotification('Error al eliminar el envío', 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.close();
                        let errorMessage = 'Error al eliminar el envío';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        showNotification(errorMessage, 'error');
                    }
                });
            }
        });
    }

    // Función para mostrar notificaciones
    function showNotification(message, type) {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        Toast.fire({
            icon: type,
            title: message
        });
    }

    // Manejar mensajes de sesión
    @if(session('success'))
        showNotification('{{ session('success') }}', 'success');
    @endif

    @if(session('error'))
        showNotification('{{ session('error') }}', 'error');
    @endif

    // Manejar errores de carga de imagen
    document.addEventListener('DOMContentLoaded', function() {
        const modalImage = document.getElementById('modalImage');
        if (modalImage) {
            modalImage.addEventListener('error', function() {
                console.error('Error al cargar la imagen');
                this.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgZmlsbD0iI2YzZjRmNiIvPjx0ZXh0IHg9IjEwMCIgeT0iMTAwIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIGZpbGw9IiM2YjcyODAiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIwLjM1ZW0iPkltYWdlbiBubyBlbmNvbnRyYWRhPC90ZXh0Pjwvc3ZnPg==';
            });
        }
    });
</script>
@stop

@extends('adminlte::page')

@section('title', 'Dashboard de Envíos | Sistema de Logística')

@section('content_header')
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
        <button type="button" class="btn-create" data-bs-toggle="modal" data-bs-target="#createEnvioModal">
            <i class="fas fa-plus"></i>
            <span>Nuevo Envío</span>
        </button>
    </div>
</div>
@endsection
<script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
@section('content')
<!-- Dashboard Stats -->
<div class="stats-grid">
    <div class="stat-card primary">
        <div class="stat-icon">
            <div class="icon-bg">
                <i class="fas fa-boxes"></i>
            </div>
        </div>
        <div class="stat-content">
            <h3>{{ $envios->count() }}</h3>
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
            <h3>{{ $envios->where('status', 'Pendiente')->count() }}</h3>
            <p>Pendientes</p>
            <div class="stat-trend">
                <i class="fas fa-exclamation-circle"></i>
                <span>Estado actual</span>
            </div>
        </div>
    </div>

    <div class="stat-card info">
        <div class="stat-icon">
            <div class="icon-bg">
                <i class="fas fa-truck-loading"></i>
            </div>
        </div>
        <div class="stat-content">
            <h3>{{ $envios->where('status', 'En camino')->count() }}</h3>
            <p>En Tránsito</p>
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
            <h3>{{ $envios->where('status', 'Entregado')->count() }}</h3>
            <p>Completados</p>
            <div class="stat-trend">
                <i class="fas fa-check-circle"></i>
                <span>Finalizados</span>
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
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Buscar envíos, clientes...">
            </div>
            <div class="action-buttons">
                <button class="btn-filter" id="filterToggle">
                    <i class="fas fa-filter"></i>
                    Filtros
                </button>
                <button class="btn-export" id="exportBtn">
                    <i class="fas fa-file-export"></i>
                    Exportar
                </button>
                <button class="btn-refresh" id="refreshBtn">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Filtros Avanzados -->
    <div class="filters-panel" id="filtersPanel">
        <div class="filter-row">
            <div class="filter-group">
                <label>Estado del Envío</label>
                <select class="select-custom" id="statusFilter">
                    <option value="">Todos los estados</option>
                    <option value="Pendiente">Pendiente</option>
                    <option value="En camino">En Camino</option>
                    <option value="Entregado">Entregado</option>
                    <option value="Cancelado">Cancelado</option>
                </select>
            </div>

            <div class="filter-group">
                <label>Unidad de Ruta</label>
                <select class="select-custom" id="unitFilter">
                    <option value="">Todas las unidades</option>
                    @foreach($envios->pluck('route_unit_id')->unique() as $unit)
                        <option value="{{ $unit }}">Unidad #{{ $unit }}</option>
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
                <label>Fecha de Creación</label>
                <div class="date-inputs">
                    <input type="date" class="date-input" id="startDate">
                    <span>a</span>
                    <input type="date" class="date-input" id="endDate">
                </div>
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
        <table class="crud-table" id="enviosTable">
            <thead>
                <tr>
                    <th class="col-checkbox">
                        <div class="checkbox-wrapper">
                            <input type="checkbox" id="selectAll">
                            <label for="selectAll"></label>
                        </div>
                    </th>
                    <th class="col-id" data-sort="id">
                        <div class="th-content">
                            <span>ID</span>
                            <i class="fas fa-sort"></i>
                        </div>
                    </th>
                    <th class="col-sender" data-sort="sender">
                        <div class="th-content">
                            <span>Remitente</span>
                            <i class="fas fa-sort"></i>
                        </div>
                    </th>
                    <th class="col-receiver" data-sort="receiver">
                        <div class="th-content">
                            <span>Destinatario</span>
                            <i class="fas fa-sort"></i>
                        </div>
                    </th>
                    <th class="col-amount" data-sort="amount">
                        <div class="th-content">
                            <span>Monto</span>
                            <i class="fas fa-sort"></i>
                        </div>
                    </th>
                    <th class="col-status" data-sort="status">
                        <div class="th-content">
                            <span>Estado</span>
                            <i class="fas fa-sort"></i>
                        </div>
                    </th>
                    <th class="col-description">
                        <div class="th-content">
                            <span>Descripción</span>
                        </div>
                    </th>
                    <th class="col-description">
                        <div class="th-content">
                            <span>Servicio</span>
                        </div>
                    </th>
                    <th class="col-image">
                        <div class="th-content">
                            <span>Imagen</span>
                        </div>
                    </th>
                    <th class="col-unit" data-sort="unit">
                        <div class="th-content">
                            <span>Unidad</span>
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
                @foreach ($envios as $envio)
                <tr class="envio-row"
                    data-id="{{ $envio->id }}"
                    data-status="{{ $envio->status }}"
                    data-unit="{{ $envio->route_unit_id }}"
                    data-amount="{{ $envio->amount }}"
                    data-created="{{ $envio->created_at }}">
                    <td class="col-checkbox">
                        <div class="checkbox-wrapper">
                            <input type="checkbox" class="row-checkbox" id="row{{ $envio->id }}" value="{{ $envio->id }}">
                            <label for="row{{ $envio->id }}"></label>
                        </div>
                    </td>
                    <td class="col-id">
                        <div class="envio-id">#{{ $envio->id }}</div>
                    </td>
                    <td class="col-sender">
                        <div class="user-info">
                            <div class="user-avatar sender">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="user-details">
                                <span class="user-name">{{ e($envio->sender_name) }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="col-receiver">
                        <div class="user-info">
                            <div class="user-avatar receiver">
                                <i class="fas fa-user-check"></i>
                            </div>
                            <div class="user-details">
                                <span class="user-name">{{ e($envio->receiver_name) }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="col-amount">
                        <div class="amount-cell">
                            <span class="amount">${{ number_format($envio->amount ?? 0, 2) }}</span>
                        </div>
                    </td>
                    <td class="col-status">
                        <div class="status-indicator status-{{ strtolower(str_replace(' ', '-', $envio->status)) }}">
                            <span class="status-dot"></span>
                            <span class="status-text">{{ $envio->status }}</span>
                        </div>
                    </td>
                    <td class="col-description">
                        <div class="description-cell" title="{{ e($envio->package_description) ?: 'Sin descripción' }}">
                            {{ e($envio->package_description) ?: 'Sin descripción' }}
                        </div>
                    </td>
                    <td class="col-description">
                        <div class="description-cell" title="{{ $envio->service->name ?? 'Sin descripción' }}">
                            {{ $envio->service->name ?? 'Sin descripción' }}
                        </div>
                    </td>
                    <td class="col-image">
                        <div class="image-cell">
                            @if ($envio->package_image)
                                <div class="image-thumbnail" onclick="showImageModal('{{ asset('storage/' . $envio->package_image) }}', {{ $envio->id }})">
                                    <img src="{{ asset('storage/' . $envio->package_image) }}"
                                        alt="Imagen del paquete">
                                    <div class="image-overlay">
                                        <i class="fas fa-search-plus"></i>
                                    </div>
                                </div>
                            @else
                                <span class="no-image">-</span>
                            @endif
                        </div>
                    </td>
                    <td class="col-unit">
                        <div class="unit-badge">
                            <i class="fas fa-truck"></i>
                            <span>{{ $envio->route_unit_id }}</span>
                        </div>
                    </td>
                    <td class="col-actions">
                        <div class="action-buttons">
                            <button class="btn-action btn-edit"
                                    data-bs-toggle="modal" data-bs-target="#editEnvioModal"
                                    data-envio-id="{{ $envio->id }}"
                                    data-envio-sender="{{ e($envio->sender_name) }}"
                                    data-envio-receiver="{{ e($envio->receiver_name) }}"
                                    data-envio-amount="{{ $envio->amount }}"
                                    data-envio-description="{{ e($envio->package_description) }}"
                                    data-envio-route-unit="{{ $envio->route_unit_id }}"
                                    data-envio-status="{{ $envio->status }}"
                                    data-envio-service="{{ $envio->service_id }}"
                                    title="Editar envío">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn-action btn-delete"
                                    data-envio-id="{{ $envio->id }}"
                                    onclick="confirmDelete(this)"
                                    title="Eliminar envío">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Table Footer -->
    <div class="table-footer">
        <div class="footer-info">
            <div class="selected-count">
                <span id="selectedRows">0</span> envíos seleccionados
            </div>
            <div class="total-count">
                Mostrando <span id="visibleCount">{{ $envios->count() }}</span> de <span id="totalCount">{{ $envios->count() }}</span> envíos
            </div>
        </div>
        <div class="footer-actions">
            <div class="bulk-actions">
                <select class="select-custom bulk-select" id="bulkAction">
                    <option value="">Acciones en lote</option>
                    <option value="delete">Eliminar seleccionados</option>
                    <option value="Pendiente">Marcar como Pendiente</option>
                    <option value="En camino">Marcar como En Camino</option>
                    <option value="Entregado">Marcar como Entregado</option>
                    <option value="Cancelado">Marcar como Cancelado</option>
                </select>
                <button class="btn-bulk-action" id="applyBulkAction">Aplicar</button>
            </div>

            <div class="pagination-info">
                <span>Filas por página:</span>
                <select class="select-custom page-size" id="pageSize">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Modal Imagen -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content image-modal">
            <div class="modal-header">
                <h5 class="modal-title">Imagen del Paquete - Envío #<span id="modalEnvioId"></span></h5>
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
<!-- Incluir otros modales -->
@include('envios.create')
@include('envios.edit')
@endsection

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
}

.btn-close-custom:hover {
    opacity: 1;
    background: none !important;
    color: #000 !important;
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

.range-inputs, .date-inputs {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.range-input, .date-input {
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
    padding: 1rem 0.75rem;
    text-align: left;
    font-weight: 600;
    color: var(--gray-700);
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 1px solid var(--gray-200);
    white-space: nowrap;
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
    padding: 1rem 0.75rem;
    border-bottom: 1px solid var(--gray-200);
    vertical-align: middle;
}

.envio-row:hover {
    background: var(--gray-50);
}

.envio-row.selected {
    background: rgba(99, 102, 241, 0.05);
    border-left: 3px solid var(--primary);
}

/* Column Styles */
.col-checkbox {
    width: 40px;
    text-align: center;
}

.checkbox-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
}

.checkbox-wrapper input[type="checkbox"] {
    display: none;
}

.checkbox-wrapper label {
    width: 18px;
    height: 18px;
    border: 2px solid var(--gray-400);
    border-radius: 4px;
    cursor: pointer;
    position: relative;
    transition: all 0.2s ease;
}

.checkbox-wrapper input[type="checkbox"]:checked + label {
    background: var(--primary);
    border-color: var(--primary);
}

.checkbox-wrapper input[type="checkbox"]:checked + label::after {
    content: '✓';
    position: absolute;
    color: white;
    font-size: 12px;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

.col-id { width: 80px; }
.col-sender { width: 200px; }
.col-receiver { width: 200px; }
.col-amount { width: 120px; }
.col-status { width: 140px; }
.col-description { width: 250px; min-width: 200px; }
.col-image { width: 80px; }
.col-unit { width: 100px; }
.col-actions { width: 160px; }

/* Table Content Styles */
.envio-id {
    font-weight: 700;
    color: var(--gray-700);
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
.user-avatar.receiver { background: rgba(16, 185, 129, 0.1); color: var(--success); }

.user-details {
    display: flex;
    flex-direction: column;
}

.user-name {
    font-weight: 600;
    color: var(--gray-800);
    font-size: 0.875rem;
}

.amount-cell .amount {
    font-weight: 700;
    color: var(--gray-800);
    font-size: 0.875rem;
}

.status-indicator {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    width: fit-content;
}

.status-pendiente { background: #fef3c7; color: #92400e; }
.status-en-camino { background: #dbeafe; color: #1e40af; }
.status-entregado { background: #d1fae5; color: #065f46; }
.status-cancelado { background: #fee2e2; color: #991b1b; }

.status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
}

.status-pendiente .status-dot { background: var(--warning); }
.status-en-camino .status-dot { background: var(--info); }
.status-entregado .status-dot { background: var(--success); }
.status-cancelado .status-dot { background: var(--danger); }

.description-cell {
    font-size: 0.875rem;
    color: var(--gray-600);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 200px;
}

.image-cell {
    display: flex;
    justify-content: center;
}

.image-thumbnail {
    position: relative;
    width: 40px;
    height: 40px;
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
}

.unit-badge {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0.75rem;
    background: var(--gray-100);
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--gray-700);
    width: fit-content;
}

.unit-badge i {
    color: var(--gray-500);
}

/* Action Buttons */
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

.btn-view {
    background: rgba(107, 114, 128, 0.1);
    color: var(--gray-600);
}

.btn-view:hover {
    background: var(--gray-600);
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

.selected-count {
    font-size: 0.875rem;
    color: var(--gray-600);
}

.total-count {
    font-size: 0.875rem;
    color: var(--gray-600);
}

.footer-actions {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.bulk-actions {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.bulk-select {
    min-width: 180px;
}

.btn-bulk-action {
    padding: 0.5rem 1rem;
    background: var(--primary);
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 0.875rem;
    cursor: pointer;
    transition: background 0.2s ease;
}

.btn-bulk-action:hover {
    background: var(--primary-dark);
}

.pagination-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: var(--gray-600);
}

.page-size {
    width: 80px;
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

    .footer-actions {
        flex-direction: column;
        gap: 1rem;
        width: 100%;
    }

    .bulk-actions {
        width: 100%;
    }

    .bulk-select {
        flex: 1;
    }

    .image-modal .modal-body {
        padding: 1rem;
        min-height: 300px;
    }
}

/* Loading Animation */
@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

.loading {
    animation: pulse 1.5s ease-in-out infinite;
}

.content-wrapper {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%) !important;
    min-height: 100vh;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}

/* Asegúrate de que los estilos no afecten al sidebar */
.main-sidebar {
    font-family: 'Source Sans Pro','Helvetica Neue',Helvetica,Arial,sans-serif !important;
    font-size: 17px !important;
}

.sidebar-menu li a {
    font-size: 17px !important;
    font-family: 'Source Sans Pro','Helvetica Neue',Helvetica,Arial,sans-serif !important;
}

/* O mejor aún, encapsula todos tus estilos dentro de clases específicas */
.dashboard-content {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}

.dashboard-content * {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}
</style>
@endsection

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

    // Toggle filters panel
    $('#filterToggle').on('click', function() {
        $('#filtersPanel').toggleClass('show');
        $(this).toggleClass('active');
    });

    // Select all checkbox
    $('#selectAll').on('change', function() {
        const isChecked = $(this).prop('checked');
        $('.row-checkbox').prop('checked', isChecked);
        updateSelectedCount();
    });

    // Individual row checkbox
    $('.row-checkbox').on('change', function() {
        if (!this.checked) {
            $('#selectAll').prop('checked', false);
        } else {
            // Check if all checkboxes are checked
            const allChecked = $('.row-checkbox:checked').length === $('.row-checkbox').length;
            $('#selectAll').prop('checked', allChecked);
        }
        updateSelectedCount();
    });

    // Update selected count
    function updateSelectedCount() {
        const selectedCount = $('.row-checkbox:checked').length;
        $('#selectedRows').text(selectedCount);

        // Add/remove selected class
        $('.envio-row').removeClass('selected');
        $('.row-checkbox:checked').each(function() {
            $(this).closest('tr').addClass('selected');
        });
    }

    // Search functionality
    $('#searchInput').on('input', function() {
        filterTable();
    });

    // Filter functionality
    $('#statusFilter, #unitFilter, #sortSelect, #minAmount, #maxAmount, #startDate, #endDate').on('change', function() {
        filterTable();
    });

    // Clear filters
    $('#clearFilters').on('click', function() {
        $('#statusFilter, #unitFilter, #sortSelect').val('');
        $('#minAmount, #maxAmount, #startDate, #endDate').val('');
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
        const startDate = $('#startDate').val();
        const endDate = $('#endDate').val();

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
        applySorting();
    }

    // Apply sorting
    function applySorting() {
        const sortBy = $('#sortSelect').val();
        const $tbody = $('#enviosTable tbody');
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

    // Bulk actions - CONEXIÓN REAL CON BACKEND
    $('#applyBulkAction').on('click', function() {
        const action = $('#bulkAction').val();
        const selectedIds = [];

        $('.row-checkbox:checked').each(function() {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length === 0) {
            showNotification('Selecciona al menos un envío', 'warning');
            return;
        }

        if (!action) {
            showNotification('Selecciona una acción', 'warning');
            return;
        }

        performBulkAction(action, selectedIds);
    });

    // Perform bulk action - CONEXIÓN REAL CON BASE DE DATOS
    function performBulkAction(action, ids) {
        let actionText = '';
        let actionType = '';

        if (action === 'delete') {
            actionText = 'eliminar';
            actionType = 'eliminación';
        } else {
            actionText = `marcar como ${action}`;
            actionType = 'cambio de estado';
        }

        Swal.fire({
            title: 'Confirmar Acción',
            html: `¿Estás seguro de que quieres <strong>${actionText}</strong> ${ids.length} envío(s)?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, continuar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280'
        }).then((result) => {
            if (result.isConfirmed) {
                // Mostrar loading
                Swal.fire({
                    title: 'Procesando...',
                    html: `Aplicando ${actionType} a ${ids.length} envío(s)`,
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Enviar petición AJAX al servidor
                $.ajax({
                    url: '{{ route("envios.bulk-action") }}',
                    method: 'POST',
                    data: {
                        action: action,
                        ids: ids
                    },
                    success: function(response) {
                        Swal.close();

                        if (response.success) {
                            if (action === 'delete') {
                                // Eliminar filas de la tabla
                                ids.forEach(id => {
                                    $(`#row${id}`).closest('tr').remove();
                                });
                                showNotification(`${ids.length} envío(s) eliminados correctamente`, 'success');
                            } else {
                                // Actualizar estado visualmente
                                ids.forEach(id => {
                                    updateRowStatus(id, action);
                                });
                                showNotification(`${ids.length} envío(s) actualizados a "${action}"`, 'success');
                            }

                            // Actualizar contadores
                            updateSelectedCount();

                        } else {
                            showNotification('Error al procesar la acción: ' + response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.close();
                        let errorMessage = 'Error al procesar la acción';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        showNotification(errorMessage, 'error');
                    }
                });
            }
        });
    }

    // Función para actualizar el estado de una fila
    function updateRowStatus(id, newStatus) {
        const $row = $(`#row${id}`).closest('tr');
        const $statusCell = $row.find('.status-indicator');

        // Actualizar datos
        $row.data('status', newStatus);

        // Actualizar visualmente
        $statusCell.removeClass('status-pendiente status-en-camino status-entregado status-cancelado');
        $statusCell.addClass(`status-${newStatus.toLowerCase().replace(' ', '-')}`);
        $statusCell.find('.status-text').text(newStatus);

        // Actualizar el punto de estado
        const $statusDot = $statusCell.find('.status-dot');
        $statusDot.removeClass().addClass('status-dot');

        switch(newStatus) {
            case 'Pendiente':
                $statusDot.css('background', '#f59e0b');
                break;
            case 'En camino':
                $statusDot.css('background', '#3b82f6');
                break;
            case 'Entregado':
                $statusDot.css('background', '#10b981');
                break;
            case 'Cancelado':
                $statusDot.css('background', '#ef4444');
                break;
        }
    }

    // Refresh button - RECARGA LA PÁGINA
    $('#refreshBtn').on('click', function() {
        const $btn = $(this);
        $btn.addClass('loading');

        // Recargar la página para obtener datos actualizados
        setTimeout(() => {
            location.reload();
        }, 500);
    });

    // Export functionality
    $('#exportBtn').on('click', function() {
        const selectedIds = [];
        $('.row-checkbox:checked').each(function() {
            selectedIds.push($(this).val());
        });

        const exportText = selectedIds.length > 0 ?
            ` (${selectedIds.length} seleccionados)` :
            ' (todos los envíos)';

        Swal.fire({
            title: 'Exportar Datos',
            html: `Selecciona el formato de exportación para${exportText}`,
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Excel',
            cancelButtonText: 'PDF',
            showDenyButton: true,
            denyButtonText: 'CSV'
        }).then((result) => {
            if (result.isConfirmed) {
                showNotification('Exportando a Excel...', 'success');
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                showNotification('Exportando a PDF...', 'success');
            } else if (result.isDenied) {
                showNotification('Exportando a CSV...', 'success');
            }
        });
    });

    // Notification function
    function showNotification(message, type) {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        Toast.fire({
            icon: type,
            title: message
        });
    }

    // Initial filter application
    filterTable();
});

// Función para mostrar imagen en modal
function showImageModal(imageSrc, envioId) {
    console.log('Mostrando imagen:', imageSrc, 'para envío:', envioId);

    // Actualizar contenido del modal
    $('#modalImage').attr('src', imageSrc);
    $('#modalEnvioId').text(envioId);

    // Configurar enlace de descarga
    $('#downloadImage').attr('href', imageSrc);
    $('#downloadImage').attr('download', `envio-${envioId}-imagen.jpg`);

    // Mostrar modal
    const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
    imageModal.show();
}

// Eliminar con SweetAlert2 mejorado
function confirmDelete(button) {
    const envioId = $(button).data('envio-id');
    const envioRow = $(button).closest('tr');
    const envioCode = envioRow.find('.envio-id').text();

    Swal.fire({
        title: '¿Eliminar envío?',
        html: `Estás a punto de eliminar el envío <strong>${envioCode}</strong><br>Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar carga
            Swal.fire({
                title: 'Eliminando...',
                text: 'Por favor espere',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading()
                }
            });

            // Enviar formulario de eliminación
            const form = $('<form>', {
                method: 'POST',
                action: `/envios/${envioId}`
            }).append(
                $('<input>', {type: 'hidden', name: '_token', value: '{{ csrf_token() }}'}),
                $('<input>', {type: 'hidden', name: '_method', value: 'DELETE'})
            );
            $('body').append(form);
            form.submit();
        }
    });
}

// Funciones adicionales
function viewEnvio(id) {
    showNotification(`Vista detallada del envío #${id}`, 'info');
}

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
@endsection

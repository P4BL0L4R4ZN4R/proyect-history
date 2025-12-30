@extends('adminlte::page')

@section('title', 'Calendario de Rutas')
@section('content')
        <div class="card-header bg-gradient-info text-white text-center">
            <h4 class="mb-0">📅 Calendario de Rutas</h4>
        </div>
        <div cla
    <div id="calendarHeader" style="max-width: 1100px; margin: 40px auto 0; display: flex; justify-content: space-between; align-items: center; font-size: 0.9rem; color: var(--color-text-dark); font-weight: 600;">
        <div>
            <strong>Leyenda:</strong> 
            <span style="color: var(--color-success)">🟢 Activo</span> &nbsp;|&nbsp; 
            <span style="color: var(--color-danger)">🔴 Inactivo</span> &nbsp;|&nbsp; 
            <span style="color: var(--color-warning)">🟡 En viaje</span>
        </div>
    </div>

    <!-- Calendario -->
    <div id="calendar"></div>

    <!-- Modal para eventos -->
    <div id="eventModal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
        <h2 id="modalTitle">Gestión de Horario</h2>
        <form id="eventForm" autocomplete="off">
            @csrf
            <input type="hidden" id="eventId" name="id" />
            <input type="hidden" id="schedule_date" name="schedule_date" />

            <label for="id_route_unit">Unidad de Ruta:</label>
            <select id="id_route_unit" name="id_route_unit" required>
                <option value="">Seleccione una unidad</option>
@foreach($units as $unit)
    @php
        $start = $unit->route->locationStart->locality ?? 'Sin inicio';
        $end = $unit->route->locationEnd->locality ?? 'Sin fin';
        $driverName = $unit->driverUnit->driver->user->name ?? 'Sin conductor';
    @endphp
    <option value="{{ $unit->id }}">
        Unidad #{{ $unit->id }} - Ruta: {{ $start }} ➔ {{ $end }} - Conductor: {{ $driverName }}
    </option>
@endforeach

            </select>

            <label for="schedule_time">Hora:</label>
            <input type="time" id="schedule_time" name="schedule_time" required />

            <label for="status">Estado:</label>
            <select id="status" name="status" required>
                <option value="">Seleccione estado</option>
                <option value="Activo">🟢 Activo</option>
                <option value="Inactivo">🔴 Inactivo</option>
                <option value="En viaje">🟡 En viaje</option>
            </select>

            <div class="modal-actions">
                <div>
<button type="submit" id="saveBtn">Guardar</button>
                    <button type="button" id="closeModal">Cancelar</button>
                </div>
                <button type="button" id="deleteEvent" style="display: none;">Eliminar</button>
            </div>
        </form>
    </div>


@push('css')
    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
    <style>
                :root {
            --color-primary: #3b82f6; /* Azul vibrante */
            --color-secondary: #2563eb; /* Azul más oscuro */
            --color-success: #22c55e; /* Verde */
            --color-warning: #facc15; /* Amarillo */
            --color-danger: #ef4444; /* Rojo */
            --color-background: #f0f4f8; /* Fondo suave */
            --color-text-dark: #1e293b;
            --color-text-light: #f9fafb;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--color-background);
            margin: 0;
            padding: 0;
            color: var(--color-text-dark);
        }

        h1 {
            text-align: center;
            padding: 28px 0;
            margin: 0;
            font-size: 2.25rem;
            color: var(--color-primary);
            background: white;
            box-shadow: 0 3px 8px rgba(59, 130, 246, 0.3);
            font-weight: 700;
            letter-spacing: 0.05em;
        }

        #calendar {
            max-width: 1100px;
            margin: 40px auto;
            background: white;
            padding: 35px 40px;
            border-radius: 18px;
            box-shadow: 0 10px 24px rgba(0,0,0,0.1);
            transition: box-shadow 0.3s ease;
        }

        #calendar:hover {
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        }
        #eventModal input[type="time"] {
    max-width: 100%;
    width: 100%;
    box-sizing: border-box; /* para incluir padding y border en el ancho */
}


        /* Modal */
        #eventModal {
            display: none;
            position: fixed;
            top: 12%;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1100;
            background: white;
            width: 460px;
            padding: 30px 35px;
            border-radius: 16px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.25);
            font-size: 1rem;
            color: var(--color-text-dark);
            font-weight: 500;
        }

        #eventModal h2 {
            margin-top: 0;
            margin-bottom: 22px;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--color-primary);
            letter-spacing: 0.02em;
        }

        #eventModal label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--color-text-dark);
        }

        #eventModal input,
        #eventModal select {
            width: 100%;
            padding: 12px 14px;
            margin-bottom: 18px;
            border: 2px solid #d1d5db;
            border-radius: 10px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
            outline-offset: 2px;
        }

        #eventModal input:focus,
        #eventModal select:focus {
            border-color: var(--color-primary);
            outline: none;
            box-shadow: 0 0 8px var(--color-primary);
        }

        #eventModal button {
            padding: 12px 20px;
            font-size: 1rem;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.25s ease, box-shadow 0.25s ease;
            user-select: none;
        }

        #eventModal button[type="submit"] {
            background-color: var(--color-primary);
            color: var(--color-text-light);
            box-shadow: 0 6px 12px rgba(59, 130, 246, 0.4);
        }

        #eventModal button[type="submit"]:hover {
            background-color: var(--color-secondary);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.5);
        }

        #eventModal #closeModal {
            background-color: #6b7280;
            color: var(--color-text-light);
            margin-left: 14px;
            box-shadow: 0 4px 10px rgba(107, 114, 128, 0.4);
        }

        #eventModal #closeModal:hover {
            background-color: #4b5563;
            box-shadow: 0 6px 14px rgba(75, 85, 99, 0.5);
        }

        #eventModal #deleteEvent {
            background-color: var(--color-danger);
            color: var(--color-text-light);
            float: right;
            box-shadow: 0 6px 14px rgba(239, 68, 68, 0.5);
            margin-top: -4px;
            margin-right: 0;
        }

        #eventModal #deleteEvent:hover {
            background-color: #b91c1c;
            box-shadow: 0 8px 18px rgba(185, 28, 28, 0.7);
        }

        .modal-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }

        /* Eventos en calendario - mejor presentación */
        .fc .fc-daygrid-event {
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            max-width: 100%;
            display: block;
            font-size: 0.9rem;
            padding: 4px 8px;
            border-radius: 8px;
            font-weight: 600;
            color: white !important;
            box-shadow: 0 2px 8px rgb(0 0 0 / 0.15);
            cursor: pointer;
        }

        /* Colores de eventos por estado */
        .fc-daygrid-event[data-status="Activo"] {
            background-color: var(--color-success) !important;
            border-color: var(--color-success) !important;
        }

        .fc-daygrid-event[data-status="Inactivo"] {
            background-color: var(--color-danger) !important;
            border-color: var(--color-danger) !important;
        }

        .fc-daygrid-event[data-status="En viaje"] {
            background-color: var(--color-warning) !important;
            border-color: var(--color-warning) !important;
            color: #1f2937 !important; /* texto oscuro para contraste */
        }

        /* Tooltip personalizado */
        .fc-daygrid-event[title] {
            position: relative;
        }

        /* Responsive tweaks */
        @media (max-width: 600px) {
            #calendar {
                padding: 20px 15px;
                margin: 20px 10px;
            }

            #eventModal {
                width: 90%;
                padding: 20px;
            }
        }
       .fc-day-today {
    background-color: rgba(66, 97, 155, 0.15) !important; /* azul suave translúcido */
    border: 1px solid rgba(100, 149, 237, 0.4) !important;
    border-radius: 6px;
}

.fc-timegrid-event .fc-event-title,
.fc-timegrid-event .fc-event-time,
.fc-timegrid-event .fc-event-main {
  white-space: normal !important;
  word-wrap: break-word !important;
  overflow: hidden !important;
  text-overflow: ellipsis !important;
}

    </style>
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let calendarEl = document.getElementById('calendar');
        let modal = document.getElementById('eventModal');
        let form = document.getElementById('eventForm');
        let deleteBtn = document.getElementById('deleteEvent');

        let calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',  // vista inicial: mes
    locale: 'es',
    selectable: true,
    editable: true,
    dayMaxEventRows: 2,
    

    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
    },
     buttonText: {
      day: 'Día',
      week: 'Semana',
      month: 'Mes',
      today: 'Hoy',
    },
    allDayText: 'Todo el día',
    events: '{{ route("route_unit_schedule.events") }}',

select(info) {
    const date = new Date(info.start);
    const schedule_date = date.toISOString().split('T')[0];
    const schedule_time = date.toTimeString().substring(0, 5); // formato HH:MM

    openModal({
        schedule_date: schedule_date,
        schedule_time: schedule_time
    });
},

    eventClick(info) {
        let event = info.event;
        openModal({
            id: event.id,
            id_route_unit: event.extendedProps.route_unit_id,
            schedule_date: event.startStr.split('T')[0],
            schedule_time: event.startStr.split('T')[1]?.substring(0, 5) || '12:00',
            status: event.extendedProps.status,
        }, true);
    },

    eventDrop(info) {
        updateEventDateTime(info.event);
    },

    eventResize(info) {
        updateEventDateTime(info.event);
    },

    eventContent(arg) {
        const estado = arg.event.extendedProps.status;
        const icono = {
            'Activo': '🟢',
            'Inactivo': '🔴',
            'En viaje': '🟡'
        }[estado] || '❔';
        const title = arg.event.title || `Unidad #${arg.event.extendedProps.route_unit_id}`;
        return { html: `<b>${icono}</b> ${title}`, domNodes: [] };
    },

    eventDidMount(info) {
        info.el.setAttribute('title', info.event.title || 'Evento de ruta');
        if (info.event.extendedProps.status) {
            info.el.dataset.status = info.event.extendedProps.status;
        }
    }
});


        calendar.render();

        function openModal(data = {}, isEdit = false) {
    form.reset();
    modal.style.display = 'block';
    deleteBtn.style.display = isEdit ? 'inline-block' : 'none';

    document.getElementById('eventId').value = data.id || '';
    document.getElementById('id_route_unit').value = data.id_route_unit || '';
    document.getElementById('schedule_date').value = data.schedule_date || '';
    document.getElementById('schedule_time').value = data.schedule_time || '';
    document.getElementById('status').value = data.status || '';

    // Cambiar texto del botón submit según si es editar o crear
    const saveBtn = document.getElementById('saveBtn');
    saveBtn.textContent = isEdit ? 'Actualizar' : 'Guardar';

    // Cierra popovers abiertos
    document.querySelectorAll('.fc-more-popover, .fc-popover').forEach(e => e.remove());
}


        document.getElementById('closeModal').onclick = () => modal.style.display = 'none';

deleteBtn.onclick = function () {
    const id = document.getElementById('eventId').value;

    // Ocultamos el modal antes de mostrar SweetAlert
    modal.style.display = 'none';

    Swal.fire({
        title: '¿Eliminar este horario?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            axios.delete(`/route-unit-schedule/${id}`).then(() => {
                calendar.refetchEvents();
                Swal.fire('Eliminado', 'El horario fue eliminado correctamente.', 'success');
            }).catch(() => {
                Swal.fire('Error', 'No se pudo eliminar.', 'error');
            });
        } else {
            // Si cancelan, volvemos a mostrar el modal
            modal.style.display = 'block';
        }
    });
};


        form.onsubmit = function (e) {
            e.preventDefault();
            const id = document.getElementById('eventId').value;
            const url = id ? `/route-unit-schedule/${id}` : '/route-unit-schedule';
            const method = id ? 'put' : 'post';

            const data = {
                id_route_unit: document.getElementById('id_route_unit').value,
                schedule_date: document.getElementById('schedule_date').value,
                schedule_time: document.getElementById('schedule_time').value,
                status: document.getElementById('status').value,
                _token: '{{ csrf_token() }}'
            };

            axios({ method, url, data }).then(() => {
                calendar.refetchEvents();
                modal.style.display = 'none';
                Swal.fire('Guardado', 'El horario fue guardado correctamente.', 'success');
            }).catch(error => {
                Swal.fire('Error', error.response?.data?.message || 'Verifique los campos.', 'error');
            });
        };

        function updateEventDateTime(event) {
            const id = event.id;
            const date = event.startStr.split('T')[0];
            const time = event.startStr.split('T')[1]?.substring(0, 5) || '12:00';

            axios.put(`/route-unit-schedule/${id}`, {
                schedule_date: date,
                schedule_time: time,
                id_route_unit: event.extendedProps.route_unit_id,
                status: event.extendedProps.status,
                _token: '{{ csrf_token() }}'
            }).then(() => {
                calendar.refetchEvents();
            }).catch(() => {
                Swal.fire('Error', 'Error actualizando el evento.', 'error');
                calendar.refetchEvents();
            });
        }
    });
</script>
@endpush
@endsection

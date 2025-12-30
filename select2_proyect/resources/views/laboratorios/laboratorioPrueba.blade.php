@include('layouts.plantilla')

@extends('adminlte::page')

@section('plugins.Sweetalert2', true)




@section('title', 'PanelLab | Administrador de laboratorios')

@section('content_header')
@endsection

@section('content')




{{-- --blue: #007bff;
--indigo: #6610f2;
--purple: #6f42c1;
--pink: #e83e8c;
--red: #dc3545;
--orange: #fd7e14;
--yellow: #ffc107;
--green: #28a745;
--teal: #20c997;
--cyan: #17a2b8;
--white: #fff;
--gray: #6c757d;
--gray-dark: #343a40;
--primary: #007bff;
--secondary: #6c757d;
--success: #28a745;
--info: #17a2b8;
--warning: #ffc107;
--danger: #dc3545;
--light: #f8f9fa;
--dark: #343a40; --}}

<!-- Agrega el botón de búsqueda antes de tu script -->
{{-- <button id="buscarBtn">Buscar</button> --}}
{{-- <h1>En Pruebas, Disculpe las molestias</h1> --}}




<h1>Listado de Laboratorios</h1>







{{-- 
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Seleccionamos todos los botones con la clase 'deshabilitar-todos'
        document.querySelectorAll('.deshabilitar-todos').forEach(button => {
            button.addEventListener('click', () => {
                console.log('Botón deshabilitar-todos clicado.');

                // Seleccionamos todos los botones dentro de los contenedores 'tooltip-container'
                const botones = document.querySelectorAll('.deshabilitar button');
                console.log('Botones seleccionados:', botones);

                // Recorremos todos los botones y los deshabilitamos
                botones.forEach(boton => {
                    console.log('Deshabilitando botón:', boton);
                    this.disabled = true;
                    boton.disabled = true;
                    boton.classList.add('btn-disabled');
                });
                
                console.log('Todos los botones han sido deshabilitados.');
            });
        });
    });
</script> --}}





    <div class="d-flex justify-content-end align-items-center gap-3" style="margin-right: 10px; margin-bottom:10px">



            <div class="tooltip-container">
                    <a type="button" href="{{ route('laboratorio.Cfdiveterinaria') }}"class="comic-button-warning-md" style="font-family: Arial, Helvetica, sans-serif; margin-right: 10px;">
                        <input type="hidden" name="usuario" value="{{ Auth::user()->name }}">
                            <i class="fas fa-paw"></i> 
                    </a>
                <span class="tooltipUI bg-warning">Veterinarias</span>
                <span class="text"></span>
            </div>

            <div class="tooltip-container">
                <a type="button" href="{{ route('laboratorio.Cfdisuscripcion') }}" class="comic-button-danger-md" style="font-family: Arial, Helvetica, sans-serif; margin-right: 10px;">
                        <input type="hidden" name="usuario" value="{{ Auth::user()->name }}">
                            <i class="fas fa-power-off" ></i> 
                        </a>
                    <span class="tooltipUI bg-danger">Estado de suscripciones</span>
                    <span class="text"></span>
            </div>

            <div class="tooltip-container">
                <a type="button" href="{{ route('laboratorio.Cfdiversion') }}"  class="comic-button-success-md" style="font-family: Arial, Helvetica, sans-serif; margin-right: 10px;">
                    <input type="hidden" name="usuario" value="{{ Auth::user()->name }}">
                        <i class="fab fa-windows"></i>
                
                    </a>
                        <span class="tooltipUI bg-success">Revisar Version</span>
                        <span class="text"></span>
            </div>


            <div class="tooltip-container">
                <a type="button" href="{{ route('laboratorio.Labsinuso') }}" class="comic-button-primary-md" style="font-family: Arial, Helvetica, sans-serif; margin-right: 10px;">
                    <input type="hidden" name="usuario" value="{{ Auth::user()->name }}">
                    
                        <i class="far fa-frown" ></i>
                </a>    
                    <span class="tooltipUI bg-primary">Laboratorios sin uso actualmente</span>
                    <span class="text"></span>
            </div>


            <div class="tooltip-container">
                <a type="button" href="{{ route('laboratorio.filterCFDI') }}" class="comic-button-secondary-md" style="font-family: Arial, Helvetica, sans-serif; margin-right: 10px;">
                    <input type="hidden" name="usuario" value="{{ Auth::user()->name }}">
                        <i class="fas fa-file-alt" ></i> 
                
                    </a>    
                    <span class="tooltipUI bg-secondary">Reporte de consumos mensuales</span>
                    <span class="text"></span>
            </div>

            
        


            
            @include('laboratorios.modal.crear')
            <button type="button" class="comic-button-info-md" data-toggle="modal" data-target="#modalTabs" style="font-family: Arial, Helvetica, sans-serif;">
                <input type="hidden" name="usuario" value="{{ Auth::user()->name }}">
                <i class="fas fa-plus"></i> Agregar nuevo Laboratorio
            </button>

    </div>






<div class="container" id="wave-menu">
    <div class="loadernew" id="wave-menu"></div>
</div>



        <div class="container-fluid" style="width: 100%; overflow-x: auto; margin: auto; ">


            {{-- <table class="table table-borderless">
                <thead>
                    <th colspan="8"></th>
                    <th colspan="2">Ordenes</th>
                    <th colspan="2"></th>
                    
                </thead>
            
                <tbody>
                    {{-- <td>d</td>
                    <td>f</td>
                    <td>s</td>
                    <td>w</td>
                    <td>v</td>
                    <td>n</td>
                    <td>p</td>
                    <td>ñ</td>
                    <td>q</td>
                    <td>z</td>
                    <td>b</td>
                    <td>b</td> 

                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tbody>
                
            </table> --}}


            <table id="laboratorios-table"  class="table-borderless"  >
            
                <thead>
                    <tr>
                        <th style="width: 1%;  height: auto; text-align: center;" data-orderable="false" class="table-secondary">Nombre</th>
                        <th style="width: 20%; height: auto;  text-align: center;" >Laboratorio</th>
                        <th style="width: 10%; height: auto;  text-align: center;" >Version</th>
                        <th style="width: 10%; height: auto;  text-align: center;" >Veterinaria</th>
                        <th style="width: 12%; height: auto;  text-align: center;" >Ultima actualizacion</th>
                        
                       
                       

                            <th class="tooltip-containerTH">Dia 
                                <i class="fas fa-file-medical-alt" style="color:  #007BFF;  height: auto;" ></i> 
                                    <span class="tooltipTH bg-primary">Ordenes por dia</span>    
                            </th>


                            <th class="tooltip-containerTH">Mes 
                                <i class="fas fa-clipboard-check" style="color: #28A745;  height: auto;"></i>
                                    <span class="tooltipTH bg-success">Ordenes por mes</span>  
                            </th>

                            <th class="tooltip-containerTH">Año 
                                <i class="fas fa-clipboard-list"  style="color: #DC3545;  height: auto;" ></i>
                                    <span class="tooltipTH bg-danger">Ordenes por año</span>  
                            </th>

                            <th class="tooltip-containerTH" style="text-align: center;">Mes antes  
                                <br>
                                <i class="fas fa-calendar-day" style="color: #6c757d; height: auto; text-align:center"></i>
                                    <span class="tooltipTH bg-secondary">Ordenes del mes anterior</span>  
                            </th>

                            <th class="tooltip-containerTH" style="text-align: center;" title="maximo de ordenes por mes">mes max 
                                <br>
                                <i class="fas fa-exclamation-circle"  style="color: #17A2B8;  height: auto; text-align:center"  ></i>
                                    <span class="tooltipTH bg-info">Máximo de ordenes por mes</span>  
                            </th> 

                            <th style="width: 10%; height: auto; text-align: center;" data-orderable="false" data-searchable="false" >Acciones</th>
                            <th style="width: 10%; height: auto;  text-align: center;" >Suscripcion</th>
                    </tr>  
                </thead>
                
            
            </table>

        </div>







        @include('layouts/js')

@endsection











@section('css')

<link href="https://cdn.datatables.net/v/bs5/dt-2.1.0/datatables.min.css" rel="stylesheet">

@stop

@section('js')

 
<script src="https://cdn.datatables.net/v/bs5/dt-2.1.0/datatables.min.js"></script>

@stop

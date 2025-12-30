@yield('css')




<style>
    .btn-disabled {
        cursor: not-allowed; /* Cambia el cursor a 'no permitido' */
        opacity: 0.4; /* Reduce la opacidad del botón para dar apariencia de deshabilitado */
        pointer-events:none; 
        transition: none; /* Desactiva las animaciones y transiciones */
    }

</style>



<style>
    .switch-disabled {
    cursor: not-allowed; /* Cambia el cursor a 'no permitido' */
    opacity: 0.5; /* Reduce la opacidad para indicar que está deshabilitado */
    pointer-events: none; /* Impide la interacción con el interruptor */
}

</style>







<style>
    .comic-button-secondary-outline {
        display: inline-block;
        padding: 4px 7px; /* Tamaño del botón */
        font-size: 13px;  /* Tamaño del texto y icono */
        font-weight: bold;
        text-align: center;
        text-decoration: none;
        color: #fff;
        background-color: #6c757d;
        border: 1px solid #5a6268; /* Borde más oscuro */
        border-radius: 3px; /* Bordes redondeados */
        box-shadow: 2px 2px 0px #495057; /* Sombra más oscura */
        transition: all 0.3s ease;
        cursor: pointer;

        margin-left: 1px;
        margin-bottom: 2px;
        margin-right: 2px;
    }

    .comic-button-secondary-outline:hover {
        background-color: #6c757d;
        color: #fff;
        border: 1px solid #5a6268; /* Borde en hover más oscuro */
        box-shadow: 2px 2px 0px #495057; /* Sombra en hover más oscura */
    }

    .comic-button-secondary-outline:active {
        background-color: #6c757d;
        box-shadow: none;
        transform: translateY(6px); /* Efecto de presionar */
    }
</style>

<style>
    .comic-button-light-outline {
        display: inline-block;
        padding: 4px 7px; /* Tamaño del botón */
        font-size: 13px;  /* Tamaño del texto y icono */
        font-weight: bold;
        text-align: center;
        text-decoration: none;
        color: #fff;
        background-color: #f8f9fa;
        border: 1px solid #ced4da; /* Borde más oscuro */
        border-radius: 3px; /* Bordes redondeados */
        box-shadow: 2px 2px 0px #adb5bd; /* Sombra más oscura */
        transition: all 0.3s ease;
        cursor: pointer;

        margin-left: 1px;
        margin-bottom: 2px;
        margin-right: 2px;
    }

    .comic-button-light-outline:hover {
        background-color: #f8f9fa;
        color: #fff;
        border: 1px solid #ced4da; /* Borde en hover más oscuro */
        box-shadow: 2px 2px 0px #adb5bd; /* Sombra en hover más oscura */
    }

    .comic-button-light-outline:active {
        background-color: #f8f9fa;
        box-shadow: none;
        transform: translateY(6px); /* Efecto de presionar */
    }
</style>

<style>
    .comic-button-dark-outline {
        display: inline-block;
        padding: 4px 7px; /* Tamaño del botón */
        font-size: 13px;  /* Tamaño del texto y icono */
        font-weight: bold;
        text-align: center;
        text-decoration: none;
        color: #fff;
        background-color: #343a40;
        border: 1px solid #23272b; /* Borde más oscuro */
        border-radius: 3px; /* Bordes redondeados */
        box-shadow: 2px 2px 0px #1d2124; /* Sombra más oscura */
        transition: all 0.3s ease;
        cursor: pointer;

        margin-left: 1px;
        margin-bottom: 2px;
        margin-right: 2px;
    }

    .comic-button-dark-outline:hover {
        background-color: #343a40;
        color: #fff;
        border: 1px solid #23272b; /* Borde en hover más oscuro */
        box-shadow: 2px 2px 0px #1d2124; /* Sombra en hover más oscura */
    }

    .comic-button-dark-outline:active {
        background-color: #343a40;
        box-shadow: none;
        transform: translateY(6px); /* Efecto de presionar */
    }
</style>

<style>
    .comic-button-success-outline {
        display: inline-block;
        padding: 4px 7px; /* Tamaño del botón */
        font-size: 13px;  /* Tamaño del texto y icono */
        font-weight: bold;
        text-align: center;
        text-decoration: none;
        color: #28a745;
        background-color: #fff;
        border: 1px solid #004d00; /* Borde */
        border-radius: 3px; /* Bordes redondeados */
        box-shadow: 2px 2px 0px #004d00; /* Sombra más oscura */
        transition: all 0.3s ease;
        cursor: pointer;

        margin-left: 1px;
        margin-bottom: 2px;
        margin-right: 2px;
    }

    .comic-button-success-outline:hover {
        background-color: #28a745;
        color: #fff; 
        border: 1px solid #004d00; /* Borde en hover */
        box-shadow: 2px 2px 0px #004d00; /* Sombra en hover más oscura */
    }

    .comic-button-success-outline:active {
        background-color: #28a745;
        color: #fff; 
        box-shadow: none;
        transform: translateY(6px); /* Efecto de presionar */
    }
</style>

<style>
    .comic-button-primary-outline {
        display: inline-block;
        padding: 4px 7px; /* Tamaño del botón */
        font-size: 13px;  /* Tamaño del texto y icono */
        font-weight: bold;
        text-align: center;
        text-decoration: none;
        color: #fff;
        background-color: #007bff;
        border: 1px solid #003d7a; /* Borde */
        border-radius: 3px; /* Bordes redondeados */
        box-shadow: 2px 2px 0px #003d7a; /* Sombra más oscura */
        transition: all 0.3s ease;
        cursor: pointer;

        margin-left: 1px;
        margin-bottom: 2px;
        margin-right: 2px;
    }

    .comic-button-primary-outline:hover {
        background-color: #007bff;
        color: #fff;
        border: 1px solid #003d7a; /* Borde en hover */
        box-shadow: 2px 2px 0px #003d7a; /* Sombra en hover más oscura */
    }

    .comic-button-primary-outline:active {
        background-color: #007bff;
        box-shadow: none;
        transform: translateY(6px); /* Efecto de presionar */
    }
</style>

<style>
    .comic-button-danger-outline {
        display: inline-block;
        padding: 4px 7px; /* Tamaño del botón */
        font-size: 13px;  /* Tamaño del texto y icono */
        font-weight: bold;
        text-align: center;
        text-decoration: none;
        color: #dc3545;
        background-color: #fff;
        border: 1px solid #b02a37; /* Borde */
        border-radius: 3px; /* Bordes redondeados */
        box-shadow: 2px 2px 0px #b02a37; /* Sombra más oscura */
        transition: all 0.3s ease;
        cursor: pointer;

        margin-left: 1px;
        margin-bottom: 2px;
        margin-right: 2px;
    }

    .comic-button-danger-outline:hover {
        background-color: #dc3545;
        color: #fff;
        border: 1px solid #b02a37; /* Borde en hover */
        box-shadow: 2px 2px 0px #b02a37; /* Sombra en hover más oscura */
    }

    .comic-button-danger-outline:active {
        background-color: #dc3545;
        color: #fff;
        box-shadow: none;
        transform: translateY(6px); /* Efecto de presionar */
    }
</style>

<style>
    .comic-button-info-outline {
        display: inline-block;
        padding: 4px 7px; /* Tamaño del botón */
        font-size: 13px;  /* Tamaño del texto y icono */
        font-weight: bold;
        text-align: center;
        text-decoration: none;
        color: #fff;
        background-color: #17a2b8;
        border: 1px solid #005a6f; /* Borde */
        border-radius: 3px; /* Bordes redondeados */
        box-shadow: 2px 2px 0px #005a6f; /* Sombra más oscura */
        transition: all 0.3s ease;
        cursor: pointer;

        margin-left: 1px;
        margin-bottom: 2px;
        margin-right: 2px;
    }

    .comic-button-info-outline:hover {
        background-color: #17a2b8;
        color: #fff;
        border: 1px solid #005a6f; /* Borde en hover */
        box-shadow: 2px 2px 0px #005a6f; /* Sombra en hover más oscura */
    }

    .comic-button-info-outline:active {
        background-color: #17a2b8;
        box-shadow: none;
        transform: translateY(6px); /* Efecto de presionar */
    }
</style>

<style>
    .comic-button-warning-outline {
        display: inline-block;
        padding: 4px 7px; /* Tamaño del botón */
        font-size: 13px;  /* Tamaño del texto y icono */
        font-weight: bold;
        text-align: center;
        text-decoration: none;
        color: #fff;
        background-color: #ffc107;
        border: 1px solid #cc9a00; /* Borde */
        border-radius: 3px; /* Bordes redondeados */
        box-shadow: 2px 2px 0px #cc9a00; /* Sombra más oscura */
        transition: all 0.3s ease;
        cursor: pointer;

        margin-left: 1px;
        margin-bottom: 2px;
        margin-right: 2px;
    }

    .comic-button-warning-outline:hover {
        background-color: #ffc107;
        color: #fff;
        border: 1px solid #cc9a00; /* Borde en hover */
        box-shadow: 2px 2px 0px #cc9a00; /* Sombra en hover más oscura */
    }

    .comic-button-warning-outline:active {
        background-color: #ffc107;
        box-shadow: none;
        transform: translateY(6px); /* Efecto de presionar */
    }
</style>




<style>
    .comic-button-secondary-outline-md {
        display: inline-block;
        padding: 8px 13px; /* Tamaño del botón */
        font-size: auto;   /* Tamaño del texto y icono */
        font-weight: bold;
        text-align: center;
        text-decoration: none;
        color: #fff;
        background-color: #6c757d;
        border: 1px solid #5a6268; /* Borde más oscuro */
        border-radius: 3px; /* Bordes redondeados */
        box-shadow: 2px 2px 0px #495057; /* Sombra más oscura */
        transition: all 0.3s ease;
        cursor: pointer;

        margin-left: 1px;
        margin-bottom: 2px;
        margin-right: 2px;
    }

    .comic-button-secondary-outline-md:hover {
        background-color: #6c757d;
        color: #fff;
        border: 1px solid #5a6268; /* Borde en hover más oscuro */
        box-shadow: 2px 2px 0px #495057; /* Sombra en hover más oscura */
    }

    .comic-button-secondary-outline-md:active {
        background-color: #6c757d;
        box-shadow: none;
        transform: translateY(6px); /* Efecto de presionar */
    }
</style>

<style>
    .comic-button-light-outline-md {
        display: inline-block;
        padding: 4px 7px; /* Tamaño del botón */
        font-size: auto;   /* Tamaño del texto y icono */
        font-weight: bold;
        text-align: center;
        text-decoration: none;
        color: #fff;
        background-color: #f8f9fa;
        border: 1px solid #ced4da; /* Borde más oscuro */
        border-radius: 3px; /* Bordes redondeados */
        box-shadow: 2px 2px 0px #adb5bd; /* Sombra más oscura */
        transition: all 0.3s ease;
        cursor: pointer;

        margin-left: 1px;
        margin-bottom: 2px;
        margin-right: 2px;
    }

    .comic-button-light-outline-md:hover {
        background-color: #f8f9fa;
        color: #fff;
        border: 1px solid #ced4da; /* Borde en hover más oscuro */
        box-shadow: 2px 2px 0px #adb5bd; /* Sombra en hover más oscura */
    }

    .comic-button-light-outline-md:active {
        background-color: #f8f9fa;
        box-shadow: none;
        transform: translateY(6px); /* Efecto de presionar */
    }
</style>

<style>
    .comic-button-dark-outline-md {
        display: inline-block;
        padding: 4px 7px; /* Tamaño del botón */
        font-size: auto;   /* Tamaño del texto y icono */
        font-weight: bold;
        text-align: center;
        text-decoration: none;
        color: #fff;
        background-color: #343a40;
        border: 1px solid #23272b; /* Borde más oscuro */
        border-radius: 3px; /* Bordes redondeados */
        box-shadow: 2px 2px 0px #1d2124; /* Sombra más oscura */
        transition: all 0.3s ease;
        cursor: pointer;

        margin-left: 1px;
        margin-bottom: 2px;
        margin-right: 2px;
    }

    .comic-button-dark-outline-md:hover {
        background-color: #343a40;
        color: #fff;
        border: 1px solid #23272b; /* Borde en hover más oscuro */
        box-shadow: 2px 2px 0px #1d2124; /* Sombra en hover más oscura */
    }

    .comic-button-dark-outline-md:active {
        background-color: #343a40;
        box-shadow: none;
        transform: translateY(6px); /* Efecto de presionar */
    }
</style>

<style>
    .comic-button-success-outline-md {
        display: inline-block;
        padding: 8px 13px; /* Tamaño del botón */
        font-size: auto;   /* Tamaño del texto y icono */
        font-weight: bold;
        text-align: center;
        text-decoration: none;
        color: #28a745;
        background-color: #fff;
        border: 1px solid #004d00; /* Borde */
        border-radius: 3px; /* Bordes redondeados */
        box-shadow: 2px 2px 0px #004d00; /* Sombra más oscura */
        transition: all 0.3s ease;
        cursor: pointer;

        margin-left: 1px;
        margin-bottom: 2px;
        margin-right: 2px;
    }

    .comic-button-success-outline-md:hover {
        background-color: #28a745;
        color: #fff; 
        border: 1px solid #004d00; /* Borde en hover */
        box-shadow: 2px 2px 0px #004d00; /* Sombra en hover más oscura */
    }

    .comic-button-success-outline-md:active {
        background-color: #28a745;
        color: #fff; 
        box-shadow: none;
        transform: translateY(6px); /* Efecto de presionar */
    }
</style>

<style>
    .comic-button-primary-outline-md {
        display: inline-block;
        padding: 4px 7px; /* Tamaño del botón */
        font-size: 13px;  /* Tamaño del texto y icono */
        font-weight: bold;
        text-align: center;
        text-decoration: none;
        color: #fff;
        background-color: #007bff;
        border: 1px solid #003d7a; /* Borde */
        border-radius: 3px; /* Bordes redondeados */
        box-shadow: 2px 2px 0px #003d7a; /* Sombra más oscura */
        transition: all 0.3s ease;
        cursor: pointer;

        margin-left: 1px;
        margin-bottom: 2px;
        margin-right: 2px;
    }

    .comic-button-primary-outline-md:hover {
        background-color: #007bff;
        color: #fff;
        border: 1px solid #003d7a; /* Borde en hover */
        box-shadow: 2px 2px 0px #003d7a; /* Sombra en hover más oscura */
    }

    .comic-button-primary-outline-md:active {
        background-color: #007bff;
        box-shadow: none;
        transform: translateY(6px); /* Efecto de presionar */
    }
</style>

<style>
    .comic-button-danger-outline-md {
        display: inline-block;
        padding: 8px 13px; /* Tamaño del botón */
        font-size: auto;  /* Tamaño del texto y icono */
        font-weight: bold;
        text-align: center;
        text-decoration: none;
        color: #dc3545;
        background-color: #fff;
        border: 1px solid #b02a37; /* Borde */
        border-radius: 3px; /* Bordes redondeados */
        box-shadow: 2px 2px 0px #b02a37; /* Sombra más oscura */
        transition: all 0.3s ease;
        cursor: pointer;

        margin-left: 1px;
        margin-bottom: 2px;
        margin-right: 2px;
    }

    .comic-button-danger-outline-md:hover {
        background-color: #dc3545;
        color: #fff;
        border: 1px solid #b02a37; /* Borde en hover */
        box-shadow: 2px 2px 0px #b02a37; /* Sombra en hover más oscura */
    }

    .comic-button-danger-outline-md:active {
        background-color: #dc3545;
        color: #fff;
        box-shadow: none;
        transform: translateY(6px); /* Efecto de presionar */
    }
</style>

<style>
    .comic-button-info-outline-md {
        display: inline-block;
        padding: 4px 7px; /* Tamaño del botón */
        font-size: auto;   /* Tamaño del texto y icono */
        font-weight: bold;
        text-align: center;
        text-decoration: none;
        color: #fff;
        background-color: #17a2b8;
        border: 1px solid #005a6f; /* Borde */
        border-radius: 3px; /* Bordes redondeados */
        box-shadow: 2px 2px 0px #005a6f; /* Sombra más oscura */
        transition: all 0.3s ease;
        cursor: pointer;

        margin-left: 1px;
        margin-bottom: 2px;
        margin-right: 2px;
    }

    .comic-button-info-outline-md:hover {
        background-color: #17a2b8;
        color: #fff;
        border: 1px solid #005a6f; /* Borde en hover */
        box-shadow: 2px 2px 0px #005a6f; /* Sombra en hover más oscura */
    }

    .comic-button-info-outline-md:active {
        background-color: #17a2b8;
        box-shadow: none;
        transform: translateY(6px); /* Efecto de presionar */
    }
</style>

<style>
    .comic-button-warning-outline-md {
        display: inline-block;
        padding: 4px 7px; /* Tamaño del botón */
        font-size: auto;   /* Tamaño del texto y icono */
        font-weight: bold;
        text-align: center;
        text-decoration: none;
        color: #fff;
        background-color: #ffc107;
        border: 1px solid #cc9a00; /* Borde */
        border-radius: 3px; /* Bordes redondeados */
        box-shadow: 2px 2px 0px #cc9a00; /* Sombra más oscura */
        transition: all 0.3s ease;
        cursor: pointer;

        margin-left: 1px;
        margin-bottom: 2px;
        margin-right: 2px;
    }

    .comic-button-warning-outline-md:hover {
        background-color: #ffc107;
        color: #fff;
        border: 1px solid #cc9a00; /* Borde en hover */
        box-shadow: 2px 2px 0px #cc9a00; /* Sombra en hover más oscura */
    }

    .comic-button-warning-outline-md:active {
        background-color: #ffc107;
        box-shadow: none;
        transform: translateY(6px); /* Efecto de presionar */
    }
</style>



















<style>
    .comic-button-secondary {
        display: inline-block;
        padding: 4px 7px; /* Tamaño del botón */
        font-size: 13px;  /* Tamaño del texto y icono */
        font-weight: bold;
        text-align: center;
        text-decoration: none;
        color: #fff;
        background-color: #6c757d;
        border: 1px solid #5a6268; /* Borde más oscuro */
        border-radius: 3px; /* Bordes redondeados */
        box-shadow: 2px 2px 0px #495057; /* Sombra más oscura */
        transition: all 0.3s ease;
        cursor: pointer;

        margin-left: 1px;
        margin-bottom: 2px;
        margin-right: 2px;
    }

    .comic-button-secondary:hover {
        background-color: #6c757d;
        color: #fff;
        border: 1px solid #5a6268; /* Borde en hover más oscuro */
        box-shadow: 2px 2px 0px #495057; /* Sombra en hover más oscura */
    }

    .comic-button-secondary:active {
        background-color: #6c757d;
        box-shadow: none;
        transform: translateY(6px); /* Efecto de presionar */
    }
</style>

<style>
    .comic-button-light {
        display: inline-block;
        padding: 4px 7px; /* Tamaño del botón */
        font-size: 13px;  /* Tamaño del texto y icono */
        font-weight: bold;
        text-align: center;
        text-decoration: none;
        color: #fff;
        background-color: #f8f9fa;
        border: 1px solid #ced4da; /* Borde más oscuro */
        border-radius: 3px; /* Bordes redondeados */
        box-shadow: 2px 2px 0px #adb5bd; /* Sombra más oscura */
        transition: all 0.3s ease;
        cursor: pointer;

        margin-left: 1px;
        margin-bottom: 2px;
        margin-right: 2px;
    }

    .comic-button-light:hover {
        background-color: #f8f9fa;
        color: #fff;
        border: 1px solid #ced4da; /* Borde en hover más oscuro */
        box-shadow: 2px 2px 0px #adb5bd; /* Sombra en hover más oscura */
    }

    .comic-button-light:active {
        background-color: #f8f9fa;
        box-shadow: none;
        transform: translateY(6px); /* Efecto de presionar */
    }
</style>

<style>
    .comic-button-dark {
        display: inline-block;
        padding: 4px 7px; /* Tamaño del botón */
        font-size: 13px;  /* Tamaño del texto y icono */
        font-weight: bold;
        text-align: center;
        text-decoration: none;
        color: #fff;
        background-color: #343a40;
        border: 1px solid #23272b; /* Borde más oscuro */
        border-radius: 3px; /* Bordes redondeados */
        box-shadow: 2px 2px 0px #1d2124; /* Sombra más oscura */
        transition: all 0.3s ease;
        cursor: pointer;

        margin-left: 1px;
        margin-bottom: 2px;
        margin-right: 2px;
    }

    .comic-button-dark:hover {
        background-color: #343a40;
        color: #fff;
        border: 1px solid #23272b; /* Borde en hover más oscuro */
        box-shadow: 2px 2px 0px #1d2124; /* Sombra en hover más oscura */
    }

    .comic-button-dark:active {
        background-color: #343a40;
        box-shadow: none;
        transform: translateY(6px); /* Efecto de presionar */
    }
</style>

<style>
    .comic-button-success {
        display: inline-block;
        padding: 4px 7px; /* Tamaño del botón */
        font-size: 13px;  /* Tamaño del texto y icono */
        font-weight: bold;
        text-align: center;
        text-decoration: none;
        color: #fff;
        background-color: #28a745;
        border: 1px solid #004d00; /* Borde */
        border-radius: 3px; /* Bordes redondeados */
        box-shadow: 2px 2px 0px #004d00; /* Sombra más oscura */
        transition: all 0.3s ease;
        cursor: pointer;

        margin-left: 1px;
        margin-bottom: 2px;
        margin-right: 2px;
    }

    .comic-button-success:hover {
        background-color: #28a745;
        color: #fff;
        border: 1px solid #004d00; /* Borde en hover */
        box-shadow: 2px 2px 0px #004d00; /* Sombra en hover más oscura */
    }

    .comic-button-success:active {
        background-color: #28a745;
        box-shadow: none;
        transform: translateY(6px); /* Efecto de presionar */
    }
</style>

<style>
    .comic-button-primary {
        display: inline-block;
        padding: 4px 7px; /* Tamaño del botón */
        font-size: 13px;  /* Tamaño del texto y icono */
        font-weight: bold;
        text-align: center;
        text-decoration: none;
        color: #fff;
        background-color: #007bff;
        border: 1px solid #003d7a; /* Borde */
        border-radius: 3px; /* Bordes redondeados */
        box-shadow: 2px 2px 0px #003d7a; /* Sombra más oscura */
        transition: all 0.3s ease;
        cursor: pointer;

        margin-left: 1px;
        margin-bottom: 2px;
        margin-right: 2px;
    }

    .comic-button-primary:hover {
        background-color: #007bff;
        color: #fff;
        border: 1px solid #003d7a; /* Borde en hover */
        box-shadow: 2px 2px 0px #003d7a; /* Sombra en hover más oscura */
    }

    .comic-button-primary:active {
        background-color: #007bff;
        box-shadow: none;
        transform: translateY(6px); /* Efecto de presionar */
    }
</style>

<style>
    .comic-button-danger {
        display: inline-block;
        padding: 4px 7px; /* Tamaño del botón */
        font-size: 13px;  /* Tamaño del texto y icono */
        font-weight: bold;
        text-align: center;
        text-decoration: none;
        color: #fff;
        background-color: #dc3545;
        border: 1px solid #b02a37; /* Borde */
        border-radius: 3px; /* Bordes redondeados */
        box-shadow: 2px 2px 0px #b02a37; /* Sombra más oscura */
        transition: all 0.3s ease;
        cursor: pointer;

        margin-left: 1px;
        margin-bottom: 2px;
        margin-right: 2px;
    }

    .comic-button-danger:hover {
        background-color: #dc3545;
        color: #fff;
        border: 1px solid #b02a37; /* Borde en hover */
        box-shadow: 2px 2px 0px #b02a37; /* Sombra en hover más oscura */
    }

    .comic-button-danger:active {
        background-color: #dc3545;
        box-shadow: none;
        transform: translateY(6px); /* Efecto de presionar */
    }
</style>

<style>
    .comic-button-info {
        display: inline-block;
        padding: 4px 7px; /* Tamaño del botón */
        font-size: 13px;  /* Tamaño del texto y icono */
        font-weight: bold;
        text-align: center;
        text-decoration: none;
        color: #fff;
        background-color: #17a2b8;
        border: 1px solid #005a6f; /* Borde */
        border-radius: 3px; /* Bordes redondeados */
        box-shadow: 2px 2px 0px #005a6f; /* Sombra más oscura */
        transition: all 0.3s ease;
        cursor: pointer;

        margin-left: 1px;
        margin-bottom: 2px;
        margin-right: 2px;
    }

    .comic-button-info:hover {
        background-color: #17a2b8;
        color: #fff;
        border: 1px solid #005a6f; /* Borde en hover */
        box-shadow: 2px 2px 0px #005a6f; /* Sombra en hover más oscura */
    }

    .comic-button-info:active {
        background-color: #17a2b8;
        box-shadow: none;
        transform: translateY(6px); /* Efecto de presionar */
    }
</style>

<style>
    .comic-button-warning {
        display: inline-block;
        padding: 4px 7px; /* Tamaño del botón */
        font-size: 13px;  /* Tamaño del texto y icono */
        font-weight: bold;
        text-align: center;
        text-decoration: none;
        color: #fff;
        background-color: #ffc107;
        border: 1px solid #cc9a00; /* Borde */
        border-radius: 3px; /* Bordes redondeados */
        box-shadow: 2px 2px 0px #cc9a00; /* Sombra más oscura */
        transition: all 0.3s ease;
        cursor: pointer;

        margin-left: 1px;
        margin-bottom: 2px;
        margin-right: 2px;
    }

    .comic-button-warning:hover {
        background-color: #ffc107;
        color: #fff;
        border: 1px solid #cc9a00; /* Borde en hover */
        box-shadow: 2px 2px 0px #cc9a00; /* Sombra en hover más oscura */
    }

    .comic-button-warning:active {
        background-color: #ffc107;
        box-shadow: none;
        transform: translateY(6px); /* Efecto de presionar */
    }
</style>








<style>
    .comic-button-secondary-md {
        display: inline-block;
        padding: 8px 13px; /* Tamaño del botón */
        font-size: auto;  /* Tamaño del texto y icono */
        font-weight: bold;
        text-align: center;
        text-decoration: none;
        color: #fff;
        background-color: #6c757d;
        border: 1px solid #5a6268; /* Borde más oscuro */
        border-radius: 3px; /* Bordes redondeados */
        box-shadow: 2px 2px 0px #495057; /* Sombra más oscura */
        transition: all 0.3s ease;
        cursor: pointer;

        margin-left: 1px;
        margin-bottom: 2px;
        margin-right: 2px;
    }

    .comic-button-secondary-md:hover {
        background-color: #6c757d;
        color: #fff;
        border: 1px solid #5a6268; /* Borde en hover más oscuro */
        box-shadow: 2px 2px 0px #495057; /* Sombra en hover más oscura */
    }

    .comic-button-secondary-md:active {
        background-color: #6c757d;
        box-shadow: none;
        transform: translateY(6px); /* Efecto de presionar */
    }
</style>

<style>
    .comic-button-light-md {
        display: inline-block;
        padding: 8px 13px; /* Tamaño del botón */
        font-size: auto;  /* Tamaño del texto y icono */
        font-weight: bold;
        text-align: center;
        text-decoration: none;
        color: #212529;
        background-color: #f8f9fa;
        border: 1px solid #ced4da; /* Borde más oscuro */
        border-radius: 3px; /* Bordes redondeados */
        box-shadow: 2px 2px 0px #adb5bd; /* Sombra más oscura */
        transition: all 0.3s ease;
        cursor: pointer;

        margin-left: 1px;
        margin-bottom: 2px;
        margin-right: 2px;
    }

    .comic-button-light-md:hover {
        background-color: #f8f9fa;
        color: #212529;
        border: 1px solid #ced4da; /* Borde en hover más oscuro */
        box-shadow: 2px 2px 0px #adb5bd; /* Sombra en hover más oscura */
    }

    .comic-button-light-md:active {
        background-color: #f8f9fa;
        box-shadow: none;
        transform: translateY(6px); /* Efecto de presionar */
    }
</style>

<style>
    .comic-button-dark-md {
        display: inline-block;
        padding: 8px 13px; /* Tamaño del botón */
        font-size: auto;  /* Tamaño del texto y icono */
        font-weight: bold;
        text-align: center;
        text-decoration: none;
        color: #fff;
        background-color: #343a40;
        border: 1px solid #23272b; /* Borde más oscuro */
        border-radius: 3px; /* Bordes redondeados */
        box-shadow: 2px 2px 0px #1d2124; /* Sombra más oscura */
        transition: all 0.3s ease;
        cursor: pointer;

        margin-left: 1px;
        margin-bottom: 2px;
        margin-right: 2px;
    }

    .comic-button-dark-md:hover {
        background-color: #343a40;
        color: #fff;
        border: 1px solid #23272b; /* Borde en hover más oscuro */
        box-shadow: 2px 2px 0px #1d2124; /* Sombra en hover más oscura */
    }

    .comic-button-dark-md:active {
        background-color: #343a40;
        box-shadow: none;
        transform: translateY(6px); /* Efecto de presionar */
    }
</style>

<style>
    .comic-button-success-md {
        display: inline-block;
        padding: 8px 13px; /* Tamaño del botón */
        font-size: auto;  /* Tamaño del texto y icono */
        font-weight: bold;
        text-align: center;
        text-decoration: none;
        color: #fff;
        background-color: #28a745;
        border: 1px solid #004d00; /* Borde */
        border-radius: 3px; /* Bordes redondeados */
        box-shadow: 2px 2px 0px #004d00; /* Sombra más oscura */
        transition: all 0.3s ease;
        cursor: pointer;

        margin-left: 1px;
        margin-bottom: 2px;
        margin-right: 2px;
    }

    .comic-button-success-md:hover {
        background-color: #28a745;
        color: #fff;
        border: 1px solid #004d00; /* Borde en hover */
        box-shadow: 2px 2px 0px #004d00; /* Sombra en hover más oscura */
    }

    .comic-button-success-md:active {
        background-color: #28a745;
        box-shadow: none;
        transform: translateY(6px); /* Efecto de presionar */
    }
</style>

<style>
    .comic-button-primary-md {
        display: inline-block;
        padding: 8px 13px; /* Tamaño del botón */
        font-size: auto;  /* Tamaño del texto y icono */
        font-weight: bold;
        text-align: center;
        text-decoration: none;
        color: #fff;
        background-color: #007bff;
        border: 1px solid #003d7a; /* Borde */
        border-radius: 3px; /* Bordes redondeados */
        box-shadow: 2px 2px 0px #003d7a; /* Sombra más oscura */
        transition: all 0.3s ease;
        cursor: pointer;

        margin-left: 1px;
        margin-bottom: 2px;
        margin-right: 2px;
    }

    .comic-button-primary-md:hover {
        background-color: #007bff;
        color: #fff;
        border: 1px solid #003d7a; /* Borde en hover */
        box-shadow: 2px 2px 0px #003d7a; /* Sombra en hover más oscura */
    }

    .comic-button-primary-md:active {
        background-color: #007bff;
        box-shadow: none;
        transform: translateY(6px); /* Efecto de presionar */
    }
</style>

<style>
    .comic-button-danger-md {
        display: inline-block;
        padding: 8px 13px; /* Tamaño del botón */
        font-size: auto;  /* Tamaño del texto y icono */
        font-weight: bold;
        text-align: center;
        text-decoration: none;
        color: #fff;
        background-color: #dc3545;
        border: 1px solid #b02a37; /* Borde */
        border-radius: 3px; /* Bordes redondeados */
        box-shadow: 2px 2px 0px #b02a37; /* Sombra más oscura */
        transition: all 0.3s ease;
        cursor: pointer;

        margin-left: 1px;
        margin-bottom: 2px;
        margin-right: 2px;
    }

    .comic-button-danger-md:hover {
        background-color: #dc3545;
        color: #fff;
        border: 1px solid #b02a37; /* Borde en hover */
        box-shadow: 2px 2px 0px #b02a37; /* Sombra en hover más oscura */
    }

    .comic-button-danger-md:active {
        background-color: #dc3545;
        box-shadow: none;
        transform: translateY(6px); /* Efecto de presionar */
    }
</style>

<style>
    .comic-button-info-md {
        display: inline-block;
        padding: 8px 13px; /* Tamaño del botón */
        font-size: auto;  /* Tamaño del texto y icono */
        font-weight: bold;
        text-align: center;
        text-decoration: none;
        color: #fff;
        background-color: #17a2b8;
        border: 1px solid #005a6f; /* Borde */
        border-radius: 3px; /* Bordes redondeados */
        box-shadow: 2px 2px 0px #005a6f; /* Sombra más oscura */
        transition: all 0.3s ease;
        cursor: pointer;

        margin-left: 1px;
        margin-bottom: 2px;
        margin-right: 2px;
    }

    .comic-button-info-md:hover {
        background-color: #17a2b8;
        color: #fff;
        border: 1px solid #005a6f; /* Borde en hover */
        box-shadow: 2px 2px 0px #005a6f; /* Sombra en hover más oscura */
    }

    .comic-button-info-md:active {
        background-color: #17a2b8;
        box-shadow: none;
        transform: translateY(6px); /* Efecto de presionar */
    }
</style>

<style>
    .comic-button-warning-md {
        display: inline-block;
        padding: 8px 13px; /* Tamaño del botón */
        font-size: auto;  /* Tamaño del texto y icono */
        font-weight: bold;
        text-align: center;
        text-decoration: none;
        color: #fff;
        background-color: #ffc107;
        border: 1px solid #cc9a00; /* Borde */
        border-radius: 3px; /* Bordes redondeados */
        box-shadow: 2px 2px 0px #cc9a00; /* Sombra más oscura */
        transition: all 0.3s ease;
        cursor: pointer;

        margin-left: 1px;
        margin-bottom: 2px;
        margin-right: 2px;
    }

    .comic-button-warning-md:hover {
        background-color: #ffc107;
        color: #fff;
        border: 1px solid #cc9a00; /* Borde en hover */
        box-shadow: 2px 2px 0px #cc9a00; /* Sombra en hover más oscura */
    }

    .comic-button-warning-md:active {
        background-color: #ffc107;
        box-shadow: none;
        transform: translateY(6px); /* Efecto de presionar */
    }
</style>


<style>
    .comic-button-secondary-XL {
        display: inline-block;
        padding: 8px 13px; /* Tamaño del botón */
        /* font-size: 13px;  Tamaño del texto y icono */
        /* font-weight: bold; */
        text-align: center;
        /* text-decoration: none; */
        color: #fff;
        background-color: #6c757d;
        border: 1px solid #5a6268; /* Borde más oscuro */
        border-radius: 3px; /* Bordes redondeados */
        box-shadow: 2px 2px 0px #495057; /* Sombra más oscura */
        transition: all 0.3s ease;
        cursor: pointer;

        margin-left: 1px;
        margin-bottom: 2px;
        margin-right: 2px;
    }

    .comic-button-secondary-XL:hover {
        background-color: #6c757d;
        color: #fff;
        border: 1px solid #5a6268; /* Borde en hover más oscuro */
        box-shadow: 2px 2px 0px #495057; /* Sombra en hover más oscura */
    }

    .comic-button-secondary-XL:active {
        background-color: #6c757d;
        box-shadow: none;
        transform: translateY(6px); /* Efecto de presionar */
    }
</style>




<style>
    .comic-button-success-XL {
        display: inline-block;
        padding: 8px 13px; /* Tamaño del botón */
        /* font-size: 13px;  Tamaño del texto y icono */
        /* font-weight: bold; */
        text-align: center;
        /* text-decoration: none; */
        color: #fff;
        background-color: #28a745;
        border: 1px solid #004d00; /* Borde */
        border-radius: 3px; /* Bordes redondeados */
        box-shadow: 2px 2px 0px #004d00; /* Sombra más oscura */
        transition: all 0.3s ease;
        cursor: pointer;

        margin-left: 1px;
        margin-bottom: 2px;
        margin-right: 2px;
    }

    .comic-button-success-XL:hover {
        background-color: #28a745;
        color: #fff;
        border: 1px solid #004d00; /* Borde en hover */
        box-shadow: 2px 2px 0px #004d00; /* Sombra en hover más oscura */
    }

    .comic-button-success-XL:active {
        background-color: #28a745;
        box-shadow: none;
        transform: translateY(6px); /* Efecto de presionar */
    }
</style>



{{-- Loader --}}

<style>
    .container {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100px;  /* Ancho del contenedor */
        height: 100px; /* Altura del contenedor */
        background-color:transparent; 
        border-radius: 10px; /* Opcional: redondear bordes del contenedor */
        position: absolute; /* Posiciona el contenedor relativamente a su contenedor padre */
        top: 50%; /* Centrar verticalmente */
        left: 50%; /* Centrar horizontalmente */
        transform: translate(-50%, -50%); 
    }

    .loadernew {
        --dim: 5rem;
        width: var(--dim);
        height: var(--dim);
        border: 7px solid #2596be;
        border-top-color: transparent;
        border-bottom-color: transparent;
        border-radius: 50%;
        animation: spin_51 1.5s infinite linear;
    }

    @keyframes spin_51 {
        from {
            transform: rotate(0);
        }
        to {
            transform: rotate(360deg);
        }
    }
</style>

{{-- Tooltips --}}


<style>


    .tooltip-container {
    /* --background: #22d3ee; */
    position: relative;
    /* background: var(--background); */
    cursor: pointer;
    transition: background 0.3s;
    /* font-size: 17px; */
    /* padding: 0.7em 1.8em; */
    }




    .tooltipUI {
    position: absolute;
    top: 100%;
    left: 50%;
    transform: translateX(-50%) translateY(-10%); /* Adjusted the initial position */
    opacity: 0;
    pointer-events: none;
    transition: all 0.3s cubic-bezier(0.68, -0.55, 0.27, 1.55);
    /* background: #17a2b8;  */
    color: #fff;
    border-radius: 0.3em;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    text-align: center;
    font-size: 14px;
    width: auto; /* Adjusted the width */
    padding: 0.5em 1em; /* Adjusted padding */
    white-space: nowrap; /* Prevent text wrapping */
    }

    .tooltip-container:hover .tooltipUI {
    top: -110%; /* Adjusted the tooltip position */
    opacity: 1;
    pointer-events: auto;
    transform: translateX(-50%) translateY(0);
    }



</style>


<style>


    .tooltip-container {
    /* --background: #22d3ee; */
    position: relative;
    /* background: var(--background); */
    cursor: pointer;
    transition: background 0.3s;
    /* font-size: 17px; */
    /* padding: 0.7em 1.8em; */
    }




    .tooltip-md {
    position: absolute;
    top: 100%;
    left: 50%;
    transform: translateX(-50%) translateY(-10%); /* Adjusted the initial position */
    opacity: 0;
    pointer-events: none;
    transition: all 0.3s cubic-bezier(0.68, -0.55, 0.27, 1.55);
    /* background: #17a2b8;  */
    color: #fff;
    border-radius: 0.3em;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    text-align: center;
    font-size: 14px;
    width: auto; /* Adjusted the width */
    padding: 0.5em 1em; /* Adjusted padding */
    white-space: nowrap; /* Prevent text wrapping */
    }

    .tooltip-container:hover .tooltip-md {
    top: -90%; /* Adjusted the tooltip position */
    opacity: 1;
    pointer-events: auto;
    transform: translateX(-50%) translateY(0);
    }



</style>


<style>


    .tooltip-containerSW {
    /* --background: #22d3ee; */
    position: relative;
    /* background: var(--background); */
    cursor: pointer;
    transition: background 0.3s;
    /* font-size: 17px; */
    /* padding: 0.7em 1.8em; */
    }




    .tooltipSW {
    position: absolute;
    top: 100%;
    left: 50%;
    transform: translateX(-50%) translateY(-10%); /* Adjusted the initial position */
    opacity: 0;
    pointer-events: none;
    transition: all 0.3s cubic-bezier(0.68, -0.55, 0.27, 1.55);
    /* background: #17a2b8;  */
    color: #fff;
    border-radius: 0.3em;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    text-align: center;
    font-size: 14px;
    width: auto; /* Adjusted the width */
    padding: 0.5em 1em; /* Adjusted padding */
    white-space: nowrap; /* Prevent text wrapping */
    }

    .tooltip-containerSW:hover .tooltipSW {
    top: -220%; /* Adjusted the tooltip position */
    opacity: 1;
    pointer-events: auto;
    transform: translateX(-50%) translateY(0);
    }



</style>










<style>
            .tooltip-containerTH {
        /* --background: #22d3ee; */
        position: relative;
        /* background: var(--background); */
        cursor: pointer;
        transition: background 0.3s;
        /* font-size: 17px; */
        /* padding: 0.7em 1.8em; */
        }

        .tooltipTH {
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%) translateY(-10%); /* Adjusted the initial position */
        opacity: 0;
        pointer-events: none;
        transition: all 0.3s cubic-bezier(0.68, -0.55, 0.27, 1.55);
        /* background: #17a2b8;  */
        color: #fff;
        border-radius: 0.3em;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        text-align: center;
        font-size: 14px;
        width: auto; /* Adjusted the width */
        padding: 0.5em 1em; /* Adjusted padding */
        white-space: nowrap; /* Prevent text wrapping */
        }

        .tooltip-containerTH:hover .tooltipTH {
        top: -10%; /* Adjusted the tooltip position */
        opacity: 1;
        pointer-events: auto;
        transform: translateX(-50%) translateY(0);
        }
</style>


<style>
    .tooltip-containerBT {
        position: relative;
        cursor: pointer;
        transition: background 0.3s;
    }

    .tooltipBT {
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%) translateY(-10%); /* Ajustado la posición inicial */
        opacity: 0;
        pointer-events: none;
        transition: all 0.3s cubic-bezier(0.68, -0.55, 0.27, 1.55);
        color: #fff;
        border-radius: 0.3em;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        text-align: center;
        font-size: 14px;
        width: auto; /* Ajustado el ancho */
        padding: 0.5em 1em; /* Ajustado el relleno */
        white-space: nowrap; /* Evitar el ajuste del texto */
        z-index: 9999; /* Asegura que el tooltip esté en el frente */
    }

    .tooltip-containerBT:hover .tooltipBT {
        top: 130%; /* Ajustado la posición del tooltip */
        opacity: 1;
        pointer-events: auto;
        transform: translateX(-50%) translateY(0);
    }
</style>




<style>
    @keyframes clockwise {
    0% {
        transform: rotate(0deg);
    }

    100% {
        transform: rotate(360deg);
    }
            }

            @keyframes counter-clockwise {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(-360deg);
            }
            }

        .gearbox {
        background: #111;
        height: 150px;
        width: 200px;
        position: relative;
        border: none;
        overflow: hidden;
        border-radius: 6px;
        box-shadow: 0px 0px 0px 1px rgba(255, 255, 255, 0.1);
        left: 50%; /* Centrar horizontalmente */
        top: 50%; /* Centrar verticalmente */
        transform: translate(-50%, -50%); /* Ajustar la posición para el centro exacto */
        }

    .gearbox .overlay {
    border-radius: 6px;
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 10;
    box-shadow: inset 0px 0px 20px black;
    transition: background 0.2s;
    }

    .gearbox .overlay {
    background: transparent;
    }

    .gear {
    position: absolute;
    height: 60px;
    width: 60px;
    box-shadow: 0px -1px 0px 0px #888888, 0px 1px 0px 0px black;
    border-radius: 30px;
    }

    .gear.large {
    height: 120px;
    width: 120px;
    border-radius: 60px;
    }

    .gear.large:after {
    height: 96px;
    width: 96px;
    border-radius: 48px;
    margin-left: -48px;
    margin-top: -48px;
    }

    .gear.one {
    top: 12px;
    left: 10px;
    }

    .gear.two {
    top: 61px;
    left: 60px;
    }

    .gear.three {
    top: 110px;
    left: 10px;
    }

    .gear.four {
    top: 13px;
    left: 128px;
    }

    .gear:after {
    content: "";
    position: absolute;
    height: 36px;
    width: 36px;
    border-radius: 36px;
    background: #111;
    top: 50%;
    left: 50%;
    margin-left: -18px;
    margin-top: -18px;
    z-index: 3;
    box-shadow: 0px 0px 10px rgba(255, 255, 255, 0.1), inset 0px 0px 10px rgba(0, 0, 0, 0.1), inset 0px 2px 0px 0px #090909, inset 0px -1px 0px 0px #888888;
    }

    .gear-inner {
    position: relative;
    height: 100%;
    width: 100%;
    background: #555;
    border-radius: 30px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .large .gear-inner {
    border-radius: 60px;
    }

    .gear.one .gear-inner {
    animation: counter-clockwise 3s infinite linear;
    }

    .gear.two .gear-inner {
    animation: clockwise 3s infinite linear;
    }

    .gear.three .gear-inner {
    animation: counter-clockwise 3s infinite linear;
    }

    .gear.four .gear-inner {
    animation: counter-clockwise 6s infinite linear;
    }

    .gear-inner .bar {
    background: #555;
    height: 16px;
    width: 76px;
    position: absolute;
    left: 50%;
    margin-left: -38px;
    top: 50%;
    margin-top: -8px;
    border-radius: 2px;
    border-left: 1px solid rgba(255, 255, 255, 0.1);
    border-right: 1px solid rgba(255, 255, 255, 0.1);
    }

    .large .gear-inner .bar {
    margin-left: -68px;
    width: 136px;
    }

    .gear-inner .bar:nth-child(2) {
    transform: rotate(60deg);
    }

    .gear-inner .bar:nth-child(3) {
    transform: rotate(120deg);
    }

    .gear-inner .bar:nth-child(4) {
    transform: rotate(90deg);
    }

    .gear-inner .bar:nth-child(5) {
    transform: rotate(30deg);
    }

    .gear-inner .bar:nth-child(6) {
    transform: rotate(150deg);
    } 
</style>

<style>
            .loaderTable {
        --cell-size: 52px;
        --cell-spacing: 1px;
        --cells: 3;
        --total-size: calc(var(--cells) * (var(--cell-size) + 2 * var(--cell-spacing)));
        display: flex;
        flex-wrap: wrap;
        width: var(--total-size);
        height: var(--total-size);
        
        }

        .cell {
        flex: 0 0 var(--cell-size);
        margin: var(--cell-spacing);
        background-color: transparent;
        box-sizing: border-box;
        border-radius: 4px;
        animation: 1.5s ripple ease infinite;
        
        }

        .cell.d-1 {
        animation-delay: 100ms;
        }

        .cell.d-2 {
        animation-delay: 200ms;
        }

        .cell.d-3 {
        animation-delay: 300ms;
        }

        .cell.d-4 {
        animation-delay: 400ms;
        }

        .cell:nth-child(1) {
        --cell-color: #00FF87;
        }

        .cell:nth-child(2) {
        --cell-color: #0CFD95;
        }

        .cell:nth-child(3) {
        --cell-color: #17FBA2;
        }

        .cell:nth-child(4) {
        --cell-color: #23F9B2;
        }

        .cell:nth-child(5) {
        --cell-color: #30F7C3;
        }

        .cell:nth-child(6) {
        --cell-color: #3DF5D4;
        }

        .cell:nth-child(7) {
        --cell-color: #45F4DE;
        }

        .cell:nth-child(8) {
        --cell-color: #53F1F0;
        }

        .cell:nth-child(9) {
        --cell-color: #60EFFF;
        }

        /*Animation*/
        @keyframes ripple {
        0% {
            background-color: transparent;
        }

        30% {
            background-color: var(--cell-color);
        }

        60% {
            background-color: transparent;
        }

        100% {
            background-color: transparent;
        }
        }
</style>





















    <style>
        div.flex.justify-between.flex-1.sm\:hidden {
            display: none !important;
        }
    </style>




    <style>
        .table-secondaria {
            --bs-table-color: #000;
            --bs-table-bg: #e2e3e5;
            --bs-table-border-color: #b5b6b7;
            --bs-table-striped-bg: #d7d8da;
            --bs-table-striped-color: #000;
            --bs-table-active-bg: #cbccce;
            --bs-table-active-color: #000;
            --bs-table-hover-bg: #d1d2d4;
            --bs-table-hover-color: #000;
            color: var(--bs-table-color);
            border-color: var(--bs-table-border-color);
        }
    </style>


<style>
        .hourglassBackground {
    position: relative;
    background-color: rgb(71, 60, 60);
    height: 130px;
    width: 130px;
    border-radius: 50%;
    margin: 30px auto;
            }

        .hourglassContainer {
        position: absolute;
        top: 30px;
        left: 40px;
        width: 50px;
        height: 70px;
        -webkit-animation: hourglassRotate 2s ease-in 0s infinite;
        animation: hourglassRotate 2s ease-in 0s infinite;
        transform-style: preserve-3d;
        perspective: 1000px;
        }

        .hourglassContainer div,
        .hourglassContainer div:before,
        .hourglassContainer div:after {
        transform-style: preserve-3d;
        }

        @-webkit-keyframes hourglassRotate {
        0% {
            transform: rotateX(0deg);
        }

        50% {
            transform: rotateX(180deg);
        }

        100% {
            transform: rotateX(180deg);
        }
        }

        @keyframes hourglassRotate {
        0% {
            transform: rotateX(0deg);
        }

        50% {
            transform: rotateX(180deg);
        }

        100% {
            transform: rotateX(180deg);
        }
        }

        .hourglassCapTop {
        top: 0;
        }

        .hourglassCapTop:before {
        top: -25px;
        }

        .hourglassCapTop:after {
        top: -20px;
        }

        .hourglassCapBottom {
        bottom: 0;
        }

        .hourglassCapBottom:before {
        bottom: -25px;
        }

        .hourglassCapBottom:after {
        bottom: -20px;
        }

        .hourglassGlassTop {
        transform: rotateX(90deg);
        position: absolute;
        top: -16px;
        left: 3px;
        border-radius: 50%;
        width: 44px;
        height: 44px;
        background-color: #999999;
        }

        .hourglassGlass {
        perspective: 100px;
        position: absolute;
        top: 32px;
        left: 20px;
        width: 10px;
        height: 6px;
        background-color: #999999;
        opacity: 0.5;
        }

        .hourglassGlass:before,
        .hourglassGlass:after {
        content: '';
        display: block;
        position: absolute;
        background-color: #999999;
        left: -17px;
        width: 44px;
        height: 28px;
        }

        .hourglassGlass:before {
        top: -27px;
        border-radius: 0 0 25px 25px;
        }

        .hourglassGlass:after {
        bottom: -27px;
        border-radius: 25px 25px 0 0;
        }

        .hourglassCurves:before,
        .hourglassCurves:after {
        content: '';
        display: block;
        position: absolute;
        top: 32px;
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background-color: #333;
        animation: hideCurves 2s ease-in 0s infinite;
        }

        .hourglassCurves:before {
        left: 15px;
        }

        .hourglassCurves:after {
        left: 29px;
        }

        @-webkit-keyframes hideCurves {
        0% {
            opacity: 1;
        }

        25% {
            opacity: 0;
        }

        30% {
            opacity: 0;
        }

        40% {
            opacity: 1;
        }

        100% {
            opacity: 1;
        }
        }

        @keyframes hideCurves {
        0% {
            opacity: 1;
        }

        25% {
            opacity: 0;
        }

        30% {
            opacity: 0;
        }

        40% {
            opacity: 1;
        }

        100% {
            opacity: 1;
        }
        }

        .hourglassSandStream:before {
        content: '';
        display: block;
        position: absolute;
        left: 24px;
        width: 3px;
        background-color: white;
        -webkit-animation: sandStream1 2s ease-in 0s infinite;
        animation: sandStream1 2s ease-in 0s infinite;
        }

        .hourglassSandStream:after {
        content: '';
        display: block;
        position: absolute;
        top: 36px;
        left: 19px;
        border-left: 6px solid transparent;
        border-right: 6px solid transparent;
        border-bottom: 6px solid #fff;
        animation: sandStream2 2s ease-in 0s infinite;
        }

        @-webkit-keyframes sandStream1 {
        0% {
            height: 0;
            top: 35px;
        }

        50% {
            height: 0;
            top: 45px;
        }

        60% {
            height: 35px;
            top: 8px;
        }

        85% {
            height: 35px;
            top: 8px;
        }

        100% {
            height: 0;
            top: 8px;
        }
        }

        @keyframes sandStream1 {
        0% {
            height: 0;
            top: 35px;
        }

        50% {
            height: 0;
            top: 45px;
        }

        60% {
            height: 35px;
            top: 8px;
        }

        85% {
            height: 35px;
            top: 8px;
        }

        100% {
            height: 0;
            top: 8px;
        }
        }

        @-webkit-keyframes sandStream2 {
        0% {
            opacity: 0;
        }

        50% {
            opacity: 0;
        }

        51% {
            opacity: 1;
        }

        90% {
            opacity: 1;
        }

        91% {
            opacity: 0;
        }

        100% {
            opacity: 0;
        }
        }

        @keyframes sandStream2 {
        0% {
            opacity: 0;
        }

        50% {
            opacity: 0;
        }

        51% {
            opacity: 1;
        }

        90% {
            opacity: 1;
        }

        91% {
            opacity: 0;
        }

        100% {
            opacity: 0;
        }
        }

        .hourglassSand:before,
        .hourglassSand:after {
        content: '';
        display: block;
        position: absolute;
        left: 6px;
        background-color: white;
        perspective: 500px;
        }

        .hourglassSand:before {
        top: 8px;
        width: 39px;
        border-radius: 3px 3px 30px 30px;
        animation: sandFillup 2s ease-in 0s infinite;
        }

        .hourglassSand:after {
        border-radius: 30px 30px 3px 3px;
        animation: sandDeplete 2s ease-in 0s infinite;
        }

        @-webkit-keyframes sandFillup {
        0% {
            opacity: 0;
            height: 0;
        }

        60% {
            opacity: 1;
            height: 0;
        }

        100% {
            opacity: 1;
            height: 17px;
        }
        }

        @keyframes sandFillup {
        0% {
            opacity: 0;
            height: 0;
        }

        60% {
            opacity: 1;
            height: 0;
        }

        100% {
            opacity: 1;
            height: 17px;
        }
        }

        @-webkit-keyframes sandDeplete {
        0% {
            opacity: 0;
            top: 45px;
            height: 17px;
            width: 38px;
            left: 6px;
        }

        1% {
            opacity: 1;
            top: 45px;
            height: 17px;
            width: 38px;
            left: 6px;
        }

        24% {
            opacity: 1;
            top: 45px;
            height: 17px;
            width: 38px;
            left: 6px;
        }

        25% {
            opacity: 1;
            top: 41px;
            height: 17px;
            width: 38px;
            left: 6px;
        }

        50% {
            opacity: 1;
            top: 41px;
            height: 17px;
            width: 38px;
            left: 6px;
        }

        90% {
            opacity: 1;
            top: 41px;
            height: 0;
            width: 10px;
            left: 20px;
        }
        }

        @keyframes sandDeplete {
        0% {
            opacity: 0;
            top: 45px;
            height: 17px;
            width: 38px;
            left: 6px;
        }

        1% {
            opacity: 1;
            top: 45px;
            height: 17px;
            width: 38px;
            left: 6px;
        }

        24% {
            opacity: 1;
            top: 45px;
            height: 17px;
            width: 38px;
            left: 6px;
        }

        25% {
            opacity: 1;
            top: 41px;
            height: 17px;
            width: 38px;
            left: 6px;
        }

        50% {
            opacity: 1;
            top: 41px;
            height: 17px;
            width: 38px;
            left: 6px;
        }

        90% {
            opacity: 1;
            top: 41px;
            height: 0;
            width: 10px;
            left: 20px;
        }
        }

</style>



<style>
    .chart-container {
        width: 100%;
        overflow-x: auto;
        margin: auto;
    }
</style>



    <style>
        .w-5 ,.h-5 {
            height: 15px;
        }

    </style>

        <style>

                .switch-SUSCRIPCION {
                    position: relative;
                    display: inline-block;
                    width: 130px;
                    height: 25px;
                }

                .switch-SUSCRIPCION input {
                    display: none;
                }

                .slider-SUSCRIPCION {
                    position: absolute;
                    cursor: pointer;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background-color: #dc3545;
                    -webkit-transition: .4s;
                    transition: .4s;
                    border-radius: 34px;
                }

                .slider-SUSCRIPCION:before {
                    position: absolute;
                    content: "";
                    height: 17px;
                    width: 17px;
                    left: 3px;

                    bottom: 4px;
                    background-color: white;
                    -webkit-transition: .4s;
                    transition: .4s;
                    border-radius: 50%;
                }

                input:checked + .slider-SUSCRIPCION {
                    background-color: #28a745;
                }

                input:focus + .slider-SUSCRIPCION {
                    box-shadow: 0 0 1px #28a745;
                }

                input:checked + .slider-SUSCRIPCION:before {
                    -webkit-transform: translateX(17px);
                    -ms-transform: translateX(26px);
                    transform: translateX(106px);
                }


                .slider-SUSCRIPCION:after {
                    content: 'DESACTIVADO';
                    font-family: Arial, Helvetica, sans-serif;
                    color: white;
                    display: block;
                    position: absolute;
                    transform: translate(-50%,-50%);
                    top: 50%;
                    left: 50%;

                    font-size: 10px;

                }

                input:checked + .slider-SUSCRIPCION:after {
                    content: 'ACTIVADO';
                    font-family: Arial, Helvetica, sans-serif;
                }




                .switch-VETERINARIA {
                    position: relative;
                    display: inline-block;
                    width: 130px;
                    height: 25px;
                }

                .switch-VETERINARIA input {
                    display: none;
                }

                .slider-VETERINARIA {
                    position: absolute;
                    cursor: pointer;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background-color: #28a745;
                    -webkit-transition: .4s;
                    transition: .4s;
                    border-radius: 34px;
                }

                .slider-VETERINARIA:before {
                    position: absolute;
                    content: "";
                    height: 17px;
                    width: 17px;
                    left: 3px;
                    bottom: 4px;
                    background-color: white;
                    -webkit-transition: .4s;
                    transition: .4s;
                    border-radius: 50%;
                }

                input:checked + .slider-VETERINARIA {
                    background-color: #007bff;
                }

                input:focus + .slider-VETERINARIA {
                    box-shadow: 0 0 1px #007bff;
                }

                input:checked + .slider-VETERINARIA:before {
                    -webkit-transform: translateX(17px);
                    -ms-transform: translateX(26px);
                    transform: translateX(106px);
                }


                .slider-VETERINARIA:after {
                    content: 'Laboratorio';
                    font-family: Arial, Helvetica, sans-serif;
                    color: white;
                    display: block;
                    position: absolute;
                    transform: translate(-50%,-50%);
                    top: 50%;
                    left: 50%;

                    font-size: 10px;

                }

                input:checked + .slider-VETERINARIA:after {
                    content: 'Veterinaria';
                    font-family: Arial, Helvetica, sans-serif;
                }


        </style>





    <style>
        .no-border-bottom th {
            border-bottom: none !important;
        }
        .with-border-top th {
            border-top: none !important;
        }
        .table-borderless th, .table-borderless td {
            border: none !important;
        }
    </style>

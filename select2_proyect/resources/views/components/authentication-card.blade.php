<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">

<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>



<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
    <div>
        {{ $logo }}
    </div>

      <div class="container mt-4">
        <div class="row justify-content-center">
          <div class="col-7 col-sm-6 col-md-5 col-lg-4">
            <div class="card shadow-sm">
            <div class="card-header bg-info " style="text-align: center">
                <h3 class="text-light">Iniciar sesión</h3>
            </div>
                
                <div class="card-body">
                    <!-- Contenido de la tarjeta aquí --> 
                    {{ $slot }}

              </div>

            </div>
          </div>
        </div>
      </div>
      


    {{-- <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
        <div class="card-header bg-primary">
            Iniciar sesion
        </div>
        <div class="card-body">

         

        </div>
    </div> --}}

</div>

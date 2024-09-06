@extends('adminlte::page')

@section('title', 'Hoteles')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Hoteles</h1>
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addHotelModal">
            <i class="fas fa-plus"></i> Agregar Hotel
        </button>
    </div>
@endsection

@section('content')
@if (session('success'))
    <div class="alert alert-primary alert-dismissible fade show" role="alert" id="success-alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
<div class="container">
    <div class="card">
            <div class="card-tools">
                <br>
                <div class="input-group input-group-sm ml-auto" style="width: 250px;">
                    <input type="text" id="searchInput" class="form-control" placeholder="Buscar...">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-default">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered mb-0">
                    <thead class="thead-dark">
                        <tr class="text-center">
                            <th>NOMBRE</th>
                            <th>GERENTE</th>
                            <th>OPERACIONES</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @foreach($hotels as $hotel)
                        <tr>
                            <td class="align-middle">{{ $hotel->name }}</td>
                            <td class="align-middle">{{ $hotel->manager }}</td>
                            <td class="text-center align-middle">
                                <a class="btn btn-secondary btn-sm" href="{{ route('hotel_config', ['hotelId' => $hotel->id]) }}" title="Configurar hotel">
                                    <i class="fa fa-cog"></i>
                                </a>
                                <button class="btn btn-primary btn-sm" onclick="openEditModal('{{ $hotel->id }}', '{{ $hotel->name }}', '{{ $hotel->manager }}')" title="Editar hotel">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <a class="btn btn-danger btn-delete btn-sm" href="#" data-id="{{ $hotel->id }}" title="Eliminar hotel">
                                    <i class="fa fa-trash"></i> 
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <div class="d-flex justify-content-center">
                {{ $hotels->links('vendor.adminlte.pagination') }}
            </div>
        </div>
    </div>
</div>
<!-- Modal para agregar hotel-->
<div class="modal fade" id="addHotelModal" tabindex="-1" role="dialog" aria-labelledby="addHotelModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addHotelModalLabel">Agregar Hotel</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.hoteles.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="hotelName">Nombre del Hotel</label>
                        <input type="text" class="form-control" id="hotelName" name="hotelName" required>
                    </div>
                    <div class="form-group">
                        <label for="managerName">Gerente</label>
                        <input type="text" class="form-control" id="managerName" name="managerName" required>
                    </div>
                    <div class="form-group">
                        <label for="hotelImage">Imagen del Hotel (1156x768)</label>
                        <input type="file" class="form-control-file" id="hotelImage" name="hotelImage" accept=".jpg, .jpeg, .png" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" >Guardar Hotel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Editar Hotel -->
<div class="modal fade" id="editHotelModal" tabindex="-1" role="dialog" aria-labelledby="editHotelModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editHotelModalLabel">Editar Hotel</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editHotelForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editHotelId" name="hotelId">
                    <div class="form-group">
                        <label for="editHotelName">Nombre del Hotel</label>
                        <input type="text" class="form-control" id="editHotelName" name="hotelName" required>
                    </div>
                    <div class="form-group">
                        <label for="editManagerName">Gerente</label>
                        <input type="text" class="form-control" id="editManagerName" name="managerName" required>
                    </div>
                    <div class="form-group">
                        <label for="editHotelImage">Imagen del Hotel (1156x768)</label>
                        <input type="file" class="form-control-file" id="editHotelImage" name="hotelImage" accept="image/jpeg,image/png,image/jpg">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación de Eliminación -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmar Eliminación</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                ¿Estás seguro de que deseas eliminar este hotel? Esta acción eliminará todos los registros asociados.
            </div>
            <div class="modal-footer">
                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const searchInput = document.getElementById('searchInput');
                const tableBody = document.getElementById('tableBody');
                const rows = tableBody.getElementsByTagName('tr');

                searchInput.addEventListener('input', function () {
                    const filter = searchInput.value.toLowerCase();

                    Array.from(rows).forEach(row => {
                        const cells = row.getElementsByTagName('td');
                        let match = false;

                        Array.from(cells).forEach(cell => {
                            if (cell.textContent.toLowerCase().includes(filter)) {
                                match = true;
                            }
                        });

                        row.style.display = match ? '' : 'none';
                    });
                });
            });

            document.getElementById('hotelImage').addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const img = new Image();
                    img.onload = function() {
                        if (img.width !== 1156 || img.height !== 768) {
                            alert('La imagen debe tener un tamaño de 1156x768 píxeles.');
                            document.getElementById('hotelImage').value = ''; // Limpiar la selección de la imagen
                        }
                    };
                    img.src = URL.createObjectURL(file);
                }
            });

            function openEditModal(hotelId, hotelName, managerName) {
                document.getElementById('editHotelId').value = hotelId;
                document.getElementById('editHotelName').value = hotelName;
                document.getElementById('editManagerName').value = managerName;
                document.getElementById('editHotelForm').action = '/update/hotel/' + hotelId;

                $('#editHotelModal').modal('show');
            }

            document.addEventListener('DOMContentLoaded', function () {
                const deleteButtons = document.querySelectorAll('.btn-delete');
                
                deleteButtons.forEach(button => {
                    button.addEventListener('click', function () {
                        const hotelId = this.getAttribute('data-id');
                        const form = document.getElementById('deleteForm');
                        
                        // Configura la URL de acción del formulario
                        form.action = `/delete/hotel/${hotelId}`; 
                        
                        // Muestra el modal
                        $('#deleteModal').modal('show');
                    });
                });
            });

            //Script para mostrar mensaje de exito por 3 segundos 
            document.addEventListener("DOMContentLoaded", function() {
            // Configura un temporizador de 3 segundos para ocultar la alerta
                setTimeout(function() {
                    var alert = document.getElementById('success-alert');
                    if (alert) {
                        alert.classList.remove('show'); 
                        alert.classList.add('fade');    
                        setTimeout(function() {
                            alert.remove(); 
                        }, 300); 
                    }
                }, 2000); 
            });
        

    </script>
@endsection

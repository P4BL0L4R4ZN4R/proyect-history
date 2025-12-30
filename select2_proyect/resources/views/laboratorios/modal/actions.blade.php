                                        <tr class="group">
                                            <td colspan="6">${group}</td>
                                            <td colspan="2">
                                                <div class="btn-group" role="group">
                                                    
                                                    <button class="btn btn-warning btn-sm editar-laboratorio"  data-laboratorio-id="${Idlab}">
                                                        <i class="fas fa-edit" style="color:white;"></i> 
                                                    </button>
                                                    <button class="btn btn-success btn-sm configurar-conexion" data-laboratorio-id="${Idlab}">
                                                        <i class="fas fa-cog"></i> 
                                                    </button>
                                                    <form id="deleteFormLaboratorio${laboratorioId}" action="{{ route('laboratorio.destroy', '') }}/${laboratorioId}" method="post">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" name="usuario" value="${usuario}">  
                                                        <button class="btn btn-danger btn-sm" title="Eliminar" type="button" style="border-radius:0%">
                                                            <i class="fas fa-trash" style="color: white"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
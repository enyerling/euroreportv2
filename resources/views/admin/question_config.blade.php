@extends('adminlte::page')
@section('title', 'Preguntas Configuracion')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
@stop


@section('content_header')
  
    
@stop

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                  <center>
                    <br>
                    <h4>CONFIGURACION PREGUNTAS</h4>
                  </center>
                </div>
                <!-- /.box-header -->
                <form role="form" action="{{ route('guardar_preguntas') }}" method="POST">
                @csrf
                <input type="hidden" name="hotel_id" value="{{ $hotelId }}">
                <div class="box-body">
                    <div class="form-group">
                        <label for="sistemas"></label>
                        @foreach($hotelSystems as $hotelSystem)
                            <div class="card my-3">
                                <div class="card-header">
                                    <h3>{{ $hotelSystem->system->name }}:</h3>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group">
                                        @foreach($hotelSystem->system->questions as $question)
                                            <li class="list-group-item">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="question_{{ $question->id }}" name="selected_questions[]" value="{{ $question->id }}">
                                                        <label class="form-check-label" for="question_{{ $question->id }}">{{ $question->name }}</label>
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <!-- /.box-body -->
                <br>
                <div class="box-footer">
                    <center>
                        <button type="submit" class="btn btn-primary">Guardar Configuración</button>
                    </center>
                </div>
                <br>
                <!-- /.box-footer -->
            </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
    <script src="{{ asset('js/custom.js') }}"></script>
@stop

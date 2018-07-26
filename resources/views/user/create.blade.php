@extends('layouts')

@section('content')
<h1>Nuevo Usuario</h1>
            @if ($errors->any())
                <div class="alert alert-danger">
                    <h6>Por favor corrige los errores debajo:</h6>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
<form method="POST" action="{{url('usuarios')}}">
	{{ csrf_field() }}
     <div class="form-group">
        <label for="name">Nombre</label>
        <input type="text" name="name" class="form-control" id="name">
    </div>
    <div class="form-group">
        <label for="email">Email address:</label>
        <input type="email" name="email" class="form-control" id="email">
    </div>
  <div class="form-group">
    <label for="pwd">Password:</label>
    <input type="password" name="password" class="form-control" id="pwd">
  </div>
	<button type="submit" class="btn btn-primary">Guardar</button>
</form>
@endsection

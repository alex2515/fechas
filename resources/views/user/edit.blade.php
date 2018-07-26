@extends('layouts')

@section('content')
<h1>Editar Usuario</h1>
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
<form method="POST" action="{{url("usuarios/{$user->id}")}}">
    {{ method_field('PUT') }}
	{{ csrf_field() }}
	<label>Nombre</label>
	<input type="text" name="name" id="name" placeholder="" value="{{ old('name', $user->name)}}">
	<label>Correo eléctronico</label>
	<input type="email" name="email" id="email" placeholder="" value="{{ old('email', $user->email) }}">
	<label>Contraseña</label>
	<input type="password" name="password" id="password" placeholder="" value="">
	<button type="submit">Guardar</button>
    <a href="{{route('user.index')}}">Regresar</a>
</form>

@endsection

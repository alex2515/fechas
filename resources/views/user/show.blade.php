@extends('layouts')

@section('content')
<h1>{{ $title }} # {{ $user->id }}</h1>
<p>Nombre del usuario: {{ $user->name }}</p>
<p>Correo eléctronico: {{ $user->email }}</p>
<a href="{{route('user.index')}}">Regresar</a>

@endsection

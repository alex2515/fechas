@extends('layouts')

@section('content')

<div class="d-flex justify-content-between align-items-end mb-3">
	<h1 class="pb-1">{{ $title }}</h1>
	<p>
		<a href="{{ url('/usuarios/nuevo') }}" class="btn btn-primary">Nuevo usuario</a>
	</p>
</div>

@if($users->isNotEmpty())
<table class="table" id="example">
	<thead class="thead-dark">
		<tr>
			<th scope="col">#</th>
			<th scope="col">Nombres</th>
			<th scope="col">Correo</th>
			<th scope="col">Opcciones</th>
		</tr>
	</thead>
	<tbody>
		@foreach($users as $user)
		<tr>
			<th scope="row">{{$user->id}}</th>
			<td>{{$user->name}}</td>
			<td>{{$user->email}}</td>
			<td>
				
				<form action="{{route('user.destroy', $user)}}" method="POST">
					{{ csrf_field() }}
					{{ method_field('DELETE') }}
					<a href="{{route('user.show', $user) }}" class="btn btn-link"><span class="oi oi-eye"></span></a>
					<a href="{{route('user.edit', $user) }}" class="btn btn-link"><span class="oi oi-pencil"></span></a>
					<button type="submit" class="btn btn-link"><span class="oi oi-trash"></span></button>
				</form>
			</td>
		</tr>
		@endforeach
	</tbody>
</table>
@else
<p>No hay usuarios registrados</p>
@endif
@endsection

<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\UserFormRequest;

class UserController extends Controller
{
	public function index(){
		$users = User::all();
		//dd($users);
		$title = "Listado de usuarios";
		//return 'Lista de Usuarios';
		return view ('user.index', compact('title', 'users'));
	}
    //
    public function show(User $user)
    {
    	$title = 'Mostrando detalles del Usuarios';
		return view ('user.show', compact('title', 'user'));
	}

	public function create()
	{
		return view('user.create');
	}

	public function store(Request $request)
	{
		$rules = [
			'name' => 'required',
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => 'required'
        ];

        $menssage = ['name.required' => 'El campo nombre es obligatorio',];

		$this->validate($request, $rules, $menssage);
		/*$data = request()->validate([
			'name' => 'required',
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => 'required',
		], [
			'name.required' => 'El campo nombre es obligatorio'
		]);

		User::create([
			'name' => $data['name'],
			'email' => $data['email'],
			'password' => bcrypt($data['password'])
		]);*/

		$user = new User;
		$user->name = $request->input('name');
		$user->email = $request->input('email');
		$user->password = bcrypt($request->input('password'));
		$user->remember_token = str_random(10);
		$user->save();

		return redirect()->route('user.index');
	}

	public function edit(User $user)
	{
		return view('user.edit',compact('user'));
	}

	public function update(User $user, Request $request)
	{
		$rules = [
			'name' => 'required',
            'email' => ['required','email', Rule::unique('users')->ignore($user->id)],
            'password' => '',
		];

		$this->validate($request, $rules);

		$user->name = $request->name;
		$user->email = $request->email;
		
		if ($request->password != null ) {
			$user->password = bcrypt($request->input('password'));		
		} else
		{
			unset($user->password);
		}
		$user->update();

		return redirect()->route('user.show', compact('user'));
	}

	public function destroy(User $user)
	{
		$user->delete();

		return redirect()->route('user.index');

	}

}

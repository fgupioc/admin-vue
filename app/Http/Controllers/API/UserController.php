<?php

namespace App\Http\Controllers\API;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::latest()->paginate(10);
        return response()->json($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            "name" => 'required|string|max:191',
            "email" => 'required|email|max:191|unique:users',
            "password" => 'required|min:6',
        ], [
            "name.required" => "El campo nombre es obligatorio.",
            "email.required" => "El campo correo es obligatorio.",
            "password.required" => "El campo contraseña es obligatorio.",
        ]);
        $request->request->add(["password" => Hash::make($request->password)]);
        $request->request->remove("photo");
        $user = User::create($request->all());
        return response()->json(["data" => $user]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $this->validate($request, [
            "name" => 'required|string|max:191',
            "email" => 'required|email|max:191|unique:users,email,' . $user->id,
            "password" => 'sometimes|min:6',
        ], [
            "name.required" => "El campo nombre es obligatorio.",
            "email.required" => "El campo correo es obligatorio.",
        ]);
        $user->update($request->all());
        return response()->json($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(["status" => 200], 200);
    }
}

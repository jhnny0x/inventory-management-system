<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(User $model)
    {
        $users = User::paginate(25);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(UserRequest $request)
    {
        $request->merge(['password' => Hash::make($request->get('password'))]);
        User::create($request->all());
        return redirect()->route('users.index')->withStatus('User successfully created.');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(UserRequest $request, User $user)
    {
        $hasPassword = $request->get('password');
        $request->merge(['password' => Hash::make($request->get('password'))]);
        $request->except([$hasPassword ? '' : 'password']);
        $user->update($request->all());

        return redirect()->route('users.index')->withStatus('User successfully updated.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->withStatus('User successfully deleted.');
    }
}

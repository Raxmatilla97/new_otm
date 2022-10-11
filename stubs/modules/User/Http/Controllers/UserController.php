<?php

namespace Modules\User\Http\Controllers;

use Modules\Support\Http\Controllers\BackendController;
use Modules\User\Http\Requests\UserValidate;
use Modules\User\Models\User;

class UserController extends BackendController
{
    public function index()
    {
        $users = User::orderBy('name')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'created_at' => $user->created_at->format('d/m/Y H:i').'h',
                ];
            });

        return inertia('User/UserIndex', [
            'users' => $users,
        ]);
    }

    public function create()
    {
        return inertia('User/UserForm');
    }

    public function store(UserValidate $request)
    {
        $user = User::create($request->validated());

        return redirect()->back()
            ->with('success', 'User created.');

        // return redirect()->route('aclUserRole.edit', $user->id)
        //     ->with('success', 'Usuário criado, defina as permissões de acesso.');
    }

    public function edit($id)
    {
        $user = User::select('id', 'name', 'email')->find($id);

        return inertia('User/UserForm', [
            'user' => $user,
        ]);
    }

    public function update(UserValidate $request, $id)
    {
        $user = User::findOrFail($id);

        $params = $request->validated();

        if (empty($params['password'])) {
            unset($params['password']);
        }

        $user->update($params);

        return redirect()->back()
            ->with('success', 'User updated.');
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();

        return redirect()->route('user.index')
            ->with('success', 'User deleted.');
    }
}

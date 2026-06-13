<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    use \App\Traits\UserPhotoFileStorage;
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filterByName = $request->query('name');
        $filterByEmail = $request->query('email');
        $filterByBlocked = $request->query('blocked');
        $filterByUserType = $request->query('user_type');

        $userQuery = User::query();
        
        if ($filterByName) {
            $userQuery->where('name', 'like', "%$filterByName%");
        }
        if ($filterByEmail) {
            $userQuery->where('email', 'like', "%$filterByEmail%");
        }
        if(isset($filterByBlocked) && $filterByBlocked !== '') {
            $userQuery->where('blocked', $filterByBlocked);
        }
        if($filterByUserType){
            $userQuery->where('user_type', $filterByUserType);
        }
        
        $users = $userQuery->orderBy('name')->paginate(20)->withQueryString();
        return view('users.index', compact('users', 'filterByName', 'filterByEmail', 'filterByBlocked', 'filterByUserType'));
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('users.show')->with('user', $user);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $newUser = new User();
        return view('users.create')->with('user', $newUser);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'user_type' => 'required|in:A,F',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $newUser = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'user_type' => $validated['user_type'],
            'password' => Hash::make($validated['password']),
        ]);

        if ($request->hasFile('image_file')) {
            $this->storeUserPhoto($request->file('image_file'), $newUser);
        }

        return redirect()->route('users.index')
            ->with('toast', [
                'variant' => 'success',
                'text' => "A conta para '{$newUser->name}' foi criada com sucesso!"
            ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        if ($user->user_type === 'C') {
            abort(403, 'Ação não autorizada. Não é permitido editar contas de clientes.');
        }

        return view('users.edit')->with('user', $user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        if ($user->user_type === 'C') {
            abort(403, 'Ação não autorizada. Não é permitido editar contas de clientes.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'user_type' => 'required|in:A,F',
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->user_type = $validated['user_type'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        if ($request->hasFile('image_file')) {
            $this->deleteUserPhoto($user);
            $this->storeUserPhoto($request->file('image_file'), $user);
        }

        return redirect()->route('users.index')
            ->with('toast', [
                'variant' => 'success',
                'text' => "A conta '{$user->name}' foi atualizada com sucesso!"
            ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if ($user->user_type === 'C') {
            $hasOrders = $user->customer && $user->customer->orders()->exists();

            if ($hasOrders) {
                $user->delete();
                $alertMsg = "O cliente {$user->name} foi removido (soft delete), pois possui histórico de encomendas.";
            } else {
                $this->deleteUserPhoto($user);
                $user->forceDelete();
                $alertMsg = "O cliente {$user->name} foi removido permanentemente do sistema.";
            }
        } else {
            $this->deleteUserPhoto($user);
            $user->forceDelete();
            $alertMsg = "A conta de funcionário/admin ({$user->name}) foi removida permanentemente.";
        }

        return redirect()->route('users.index')
            ->with('toast', [
                'variant' => 'success',
                'text' => $alertMsg
            ]);
    }

    public function destroyPhoto(User $user)
    {
        $this->deleteUserPhoto($user);
        
        return redirect()->back()
            ->with('toast', [
                'variant' => 'success',
                'text' => "A fotografia de {$user->name} foi removida."
            ]);
    }

    public function block_unblock(User $user)
    {
        $user->blocked = !$user->blocked;
        $user->save();
        
        $status = $user->blocked ? 'bloqueada' : 'desbloqueada';
        
        return redirect()->route('users.index')
            ->with('toast', [
                'variant' => 'success',
                'text' => "A conta de {$user->name} foi {$status} com sucesso."
            ]);
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

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
        $userQuery = User::query();
        if ($filterByName) {
            $userQuery->where('name', 'like', "%$filterByName%");
        }
        if ($filterByEmail) {
            $userQuery->where('email', 'like', "%$filterByEmail%");
        }
        if($filterByBlocked){
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
        $newUser = User::create($request->validated());
        if ($request->image_file) {
            $this->storeUserPhoto($request->image_file, $newUser);
        }
        $url = route('users.show', ['user' => $newUser]);
        $htmlMessage = "User <a href='$url'><strong>{$newUser->id}</strong>
                    - '{$newUser->name}'</a> has been created successfully!";
        return redirect()->route('users.index')
            ->with('alert-type', 'success')
            ->with('alert-msg', $htmlMessage);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('users.edit')->with('user', $user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $user->update($request->validated());
        if ($request->image_file) {
            $this->deleteUserPhoto($user);
            $this->storeUserPhoto($request->image_file, $user);
        }
        $url = route('users.show', ['user' => $user]);
        $htmlMessage = "User <a href='$url'><strong>{$user->id}</strong> -
                    '{$user->name}'</a> has been updated successfully!";
        return redirect()->route('users.index')
            ->with('alert-type', 'success')
            ->with('alert-msg', $htmlMessage);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        $alertType = 'success';
        $alertMsg = "User {$user->name} ({$user->id}) has been deleted
                            successfully!";
        return redirect()->route('users.index')
            ->with('alert-type', $alertType)
            ->with('alert-msg', $alertMsg);
    }

    public function destroyPhoto(User $user)
    {
        $this->deleteUserPhoto($user);
        $url = route('users.show', ['user' => $user]);
        $htmlMessage = "Photo of user <a href='$url'><strong>{$user->id}</strong> -
                    '{$user->name}'</a> has been deleted successfully!";
        return redirect()->route('users.show', ['user' => $user])
            ->with('alert-type', 'success')
            ->with('alert-msg', $htmlMessage);
    }

    public function block_unblock(User $user)
    {
        $user->blocked = !$user->blocked;
        $user->save();
        $status = $user->is_blocked ? 'blocked' : 'unblocked';
        $url = route('users.show', ['user' => $user]);
        $htmlMessage = "User <a href='$url'><strong>{$user->id}</strong> -
                    '{$user->name}'</a> has been {$status} successfully!";
        return redirect()->route('users.show', ['user' => $user])
            ->with('alert-type', 'success')
            ->with('alert-msg', $htmlMessage);
    }
}

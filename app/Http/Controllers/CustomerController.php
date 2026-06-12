<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\User;
use App\Http\Requests\CustomerFormRequest;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    use \App\Traits\UserPhotoFileStorage;

    public function index(Request $request)
    {
        $filterByName = $request->query('name');
        $filterByEmail = $request->query('email');
        $filterByBlocked = $request->query('blocked');

        $customerQuery = Customer::query()->with('user');

        if ($filterByName) {
            $customerQuery->whereHas('user', function ($q) use ($filterByName) {
                $q->where('name', 'like', "%$filterByName%");
            });
        }
        if ($filterByEmail) {
            $customerQuery->whereHas('user', function ($q) use ($filterByEmail) {
                $q->where('email', 'like', "%$filterByEmail%");
            });
        }
        if($filterByBlocked !== null && $filterByBlocked !== ''){
            $customerQuery->whereHas('user', function ($q) use ($filterByBlocked) {
                $q->where('blocked', $filterByBlocked);
            });
        }
        
        $customers = $customerQuery->paginate(20)->withQueryString();
        
        return view('customers.index', compact('customers', 'filterByName', 'filterByEmail', 'filterByBlocked'));
    }

    public function create()
    {
        $customer = new Customer();
        $user = new User();
        return view('customers.create', compact('customer', 'user'));
    }

    public function store(CustomerFormRequest $request)
    {
        $validated = $request->validated();
        $validated['user_type'] = 'C';
        $validated['password'] = bcrypt($validated['password']);

        $newUser = User::create($validated);
        
        if ($request->image_file) {
            $this->storeUserPhoto($request->image_file, $newUser);
        }

        $newCustomer = new Customer($validated);
        $newCustomer->id = $newUser->id;
        $newCustomer->save();

        $url = route('customers.show', ['customer' => $newCustomer]);
        $htmlMessage = "Customer <a href='$url'><strong>{$newCustomer->id}</strong>
                    - '{$newUser->name}'</a> has been created successfully!";
        return redirect()->route('customers.index')
            ->with('alert-type', 'success')
            ->with('alert-msg', $htmlMessage);
    }

    public function show(Customer $customer)
    {
        $customer->load('user');
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        $customer->load('user');
        return view('customers.edit', compact('customer'));
    }

    public function update(CustomerFormRequest $request, Customer $customer)
    {
        $validated = $request->validated();
        
        $customer->user->update($validated);
        
        if ($request->image_file) {
            $this->deleteUserPhoto($customer->user);
            $this->storeUserPhoto($request->image_file, $customer->user);
        }

        $customer->update($validated);

        $url = route('customers.show', ['customer' => $customer]);
        $htmlMessage = "Customer <a href='$url'><strong>{$customer->id}</strong> -
                    '{$customer->user->name}'</a> has been updated successfully!";
        return redirect()->route('customers.index')
            ->with('alert-type', 'success')
            ->with('alert-msg', $htmlMessage);
    }

    public function destroy(Customer $customer)
    {
        $user = $customer->user;
        $customer->delete();
        $user->delete();

        $alertType = 'success';
        $alertMsg = "Customer {$user->name} ({$customer->id}) has been deleted successfully!";
        return redirect()->route('customers.index')
            ->with('alert-type', $alertType)
            ->with('alert-msg', $alertMsg);
    }
}

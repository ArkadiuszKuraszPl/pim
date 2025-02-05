<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Country;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Mail\NewUserNotification;
use Spatie\Permission\Models\Role;
use App\Mail\UserPasswordGenerated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserCreatedNotification;
use Illuminate\Http\RedirectResponse;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $users = User::with('roles')->get(); // Pobranie użytkowników z relacją ról
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    {
        $roles = Role::all(); // Pobierz wszystkie role z tabeli `roles`
        $countries = Country::all();
        $userRole = Auth::user()->roles->pluck('name')->first();

        $users = User::whereDoesntHave('roles', function ($query) {
            $query->whereIn('name', ['admin', 'hr']);
        })->get();

        return view('users.create', [
            'users' => $users,
        ], compact('roles', 'userRole', 'countries'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        // Walidacja danych wejściowych
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:255',
            'role' => 'required|exists:roles,name', // Walidacja roli
            'parent_id' => 'nullable|exists:users,id',
            'country_id' => 'nullable|exists:countries,id',
        ]);

        $generatedPassword = Str::random(12);

        // Kontynuuj tworzenie użytkownika
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($generatedPassword), // Hashowanie hasła
            'parent_id' => $request->parent_id,
            'country_id' => $request->country_id
        ]);
    
        // Przypisywanie roli
        $user->assignRole($validatedData['role']);
    
        // Wysyłanie e-maila do nowego użytkownika
        Mail::to($user->email)->send(new UserCreatedNotification($user, $generatedPassword));
    
        // Wysyłanie e-maila na adres firmowy
        $companyEmail = 'kontakt@designak.pl';
        Mail::to($companyEmail)->send(new NewUserNotification($user));

        return redirect(route('users.index'))->with('status', 'Użytkownik został dodany i przypisano mu rolę.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): View
    {
        $roles = Role::all();
        $userRole = $user->getRoleNames()->first();
        return view("users.edit", compact('user', 'roles', 'userRole'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        // Walidacja danych wejściowych
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|exists:roles,name', // Walidacja roli
        ]);

        $user->name = $validatedData['name'];
        if ($user->email !== $validatedData['email']) {
            $user->email = $validatedData['email']; // Zmieniamy e-mail tylko, gdy jest inny niż obecny
        }
        $user->save();

        // Aktualizacja roli użytkownika
        if ($user->roles->first()->name !== $validatedData['role']) {
            $user->syncRoles([$validatedData['role']]); // Usuwa poprzednie role i przypisuje nową
        }

        return redirect(route('users.index'))->with('status', 'Zaktualizowano użytkownika');
    }

    public function generatePassword(User $user): RedirectResponse
    {
        // Wygenerowanie losowego hasła
        $newPassword = Str::random(12);

        // Aktualizacja hasła użytkownika (z hashowaniem)
        $user->password = Hash::make($newPassword);
        $user->save();

        // Wysyłanie e-maila z nowym hasłem
        Mail::to($user->email)->send(new UserPasswordGenerated($user, $newPassword));

        // Powrót z komunikatem sukcesu
        return redirect()->back()->with('status', 'Hasło zostało wygenerowane, wysłane na e-mail oraz zapisane w bazie.');
    }


    public function editProfile(): View
    {
        $user = Auth::user();
        return view('users.profile', compact('user'));
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:255',
        ]);

        $user->update($validatedData);

        return redirect()->route('profile.edit')->with('status', 'Dane zostały zaktualizowane.');
    }
}

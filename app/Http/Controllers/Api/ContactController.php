<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ContactController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Fetch all users except the currently authenticated one
        $contacts = User::where('id', '!=', $userId)
            ->select('id', 'name', 'email', 'profile_picture', 'is_online', 'last_seen')
            ->get();

        return response()->json($contacts);
    }
}

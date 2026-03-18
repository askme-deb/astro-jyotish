<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Services\Api\Clients\UserApiService;
use Illuminate\Support\Facades\Session;


class ProfileController extends Controller
{
    protected $userApiService;

    public function __construct()
    {
        $this->userApiService = new UserApiService(config('auth_api'));
    }

    /**
     * Display the user's profile page.
     */
    public function index()
    {
         $userId = session('api_user_id');
            if (!$userId) {
                return redirect()->route('home');
            }
        // Use session for now; could fetch from API if needed
        return view('profile');
    }

    /**
     * Handle profile update POST
     */
    public function update(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'mobile_no' => 'required|string',
            'alt_ph' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'country_id' => 'nullable|integer',
            'pincode' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $token = $request->cookie('auth_api_token') ?? $request->bearerToken();
        if (!$token) {
            $msg = 'Authentication token missing.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => $msg], 401);
            }
            return redirect()->back()->with('error', $msg);
        }

        $data = $request->only([
            'first_name', 'last_name', 'email', 'mobile_no', 'alt_ph', 'address', 'city', 'state', 'country_id', 'pincode'
        ]);

        $result = $this->userApiService->updateProfile($data, $token);

        if (isset($result['status']) && $result['status']) {
            // Update session user data
            $user = session('auth.user');
            foreach ($data as $k => $v) {
                $user[$k] = $v;
            }
            session(['auth.user' => $user]);
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => 'Profile updated successfully.']);
            }
            return redirect()->back()->with('success', 'Profile updated successfully.');
        } else {
            $msg = $result['message'] ?? 'Profile update failed.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => $msg], 400);
            }
            return redirect()->back()->with('error', $msg);
        }
    }
}

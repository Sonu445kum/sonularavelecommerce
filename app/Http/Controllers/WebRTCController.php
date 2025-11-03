<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WebRTCController extends Controller
{
    public function index()
    {
        return view('webrtc.chat');
    }

    public function signal(Request $request)
    {
        // This will be used for WebRTC signaling between peers
        // In production, use Laravel Echo with Pusher or Socket.io
        return response()->json([
            'success' => true,
            'data' => $request->all()
        ]);
    }
}

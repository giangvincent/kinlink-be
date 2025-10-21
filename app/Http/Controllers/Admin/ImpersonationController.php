<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ImpersonationController extends Controller
{
    public function leave(Request $request): RedirectResponse
    {
        $impersonatorId = $request->session()->pull('impersonator_id');

        if ($impersonatorId) {
            $impersonator = User::find($impersonatorId);

            if ($impersonator) {
                auth()->login($impersonator);
            }
        }

        return redirect()->route('filament.admin.pages.dashboard');
    }
}

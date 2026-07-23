<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Suffragan;

class SuffraganController extends Controller
{
    public function showLoginForm()
    {
        return view('suffragan.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'documentationnumber' => 'required|exists:suffragans,documentationnumber',
        ]);

        session(['suffragan_documentationnumber' => $request->documentationnumber]);

        return redirect()->route('suffragan.vote');
    }

    public function vote()
    {
        $documentationNumber = session('suffragan_documentationnumber');
        $suffragan = Suffragan::where('documentationnumber', $documentationNumber)->first();

        return view('suffragan.vote', compact('suffragan'));
    }
}

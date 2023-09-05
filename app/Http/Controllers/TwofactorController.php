<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


class TwofactorController extends Controller
{

    public function index()
    {
        return view('auth.verfyfiedCode');
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if ( $request->input('code') == $user->code ) {
            // dd( $user->code );s
            $user->resetCode();
            return redirect()->route('dashboard');
        }
        return redirect()->back()->withErrors( [ 'code' => 'Code Not Verfified Please Try Again' ] );
    }


    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}

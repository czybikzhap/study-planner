<?php

namespace App\Http\Controllers;

use App\Models\Direction;
use App\Models\Profile;
use Illuminate\Http\Request;

class DirectionController extends Controller
{
    public function index()
    {
        $directions = Direction::all();
        $profiles = $directions->profiles;

        return view('directions.index', compact('directions', 'profiles'));
    }



}

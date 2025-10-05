<?php

namespace App\Http\Controllers;

use App\Services\PriorityService;

class DirectionController extends Controller
{
    private PriorityService $priorityService;

    public function __construct(PriorityService $priorityService)
    {
        $this->priorityService = $priorityService;
    }

    public function index()
    {
        $directions = $this->priorityService->getAllDirectionsWithProfiles();
        return view('directions.index', compact('directions'));
    }




}

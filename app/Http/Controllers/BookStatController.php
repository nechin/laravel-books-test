<?php

namespace App\Http\Controllers;

use App\Http\Resources\StatResource;
use App\Journal;
use App\Services\JournalStatService;
use Illuminate\Http\Request;

class BookStatController extends Controller
{
    /**
     * @var \App\Services\JournalStatService
     */
    protected $service;

    /**
     * BookStatController constructor.
     *
     * @param \App\Services\JournalStatService $service
     */
    public function __construct(JournalStatService $service)
    {
        $this->service = $service;
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return array
	 */
    public function __invoke(Request $request)
    {
        return $this->service->get_data();
    }
}

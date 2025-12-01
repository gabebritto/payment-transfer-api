<?php

namespace App\Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\User\Http\Requests\CreateRetailerRequest;
use App\Modules\User\Services\RetailerService;
use Illuminate\Http\JsonResponse;

class RetailerController extends Controller
{
    public function __construct(
        private readonly RetailerService $retailerService
    ) {
    }

    public function store(CreateRetailerRequest $request): JsonResponse
    {
        $retailer = $this->retailerService->create($request->validated());

        return response()->json($retailer, 201);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUpdateCompany;
use App\Http\Resources\CompanyResource;
use App\Jobs\CompanyCreated;
use App\Services\CompanyService;
use App\Services\EvaluationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use JsonException;

class CompanyController extends Controller
{
    public function __construct(
        protected EvaluationService $evaluationService,
        protected CompanyService $companyService
    ) {
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $companies = $this->companyService->getCompanies($request->get('filter', ''));
        return CompanyResource::collection($companies);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreUpdateCompany $request
     * @return CompanyResource
     */
    public function store(StoreUpdateCompany $request): CompanyResource
    {
        $company = $this->companyService->createNewCompany($request->validated(), $request->image);
        CompanyCreated::dispatch($company->email)->onQueue('queue_email');

        return new CompanyResource($company);
    }

    /**
     * Display the specified resource.
     *
     * @param string $uuid
     * @return CompanyResource
     * @throws JsonException
     */
    public function show(string $uuid): CompanyResource
    {
        $company = $this->companyService->getCompanyByUUID($uuid);
        $evaluations = $this->evaluationService->getEvaluationsCompany($uuid);

        return (new CompanyResource($company))
            ->additional(
                ['evaluations' => json_decode($evaluations, false, 512, JSON_THROW_ON_ERROR)]
            );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param StoreUpdateCompany $request
     * @param string $uuid
     * @return JsonResponse
     */
    public function update(StoreUpdateCompany $request, string $uuid): JsonResponse
    {
        $this->companyService->updateCompany($request->validated(), $uuid, $request->image);
        return response()->json(['message' => 'Updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $uuid
     * @return JsonResponse
     */
    public function destroy(string $uuid): JsonResponse
    {
        $this->companyService->deleteCompany($uuid);
        return response()->json([], 204);
    }
}

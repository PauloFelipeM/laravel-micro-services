<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUpdateCategory;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    public function __construct(protected Category $repository)
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $categories = $this->repository->query()->get();
        return CategoryResource::collection($categories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreUpdateCategory $request
     * @return CategoryResource
     */
    public function store(StoreUpdateCategory $request): CategoryResource
    {
        $category = $this->repository->query()->create($request->validated());
        return new CategoryResource($category);
    }

    /**
     * Display the specified resource.
     *
     * @param string $url
     * @return CategoryResource
     */
    public function show(string $url): CategoryResource
    {
        $category = $this->repository->query()->where('url', $url)->firstOrFail();

        return new CategoryResource($category);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param StoreUpdateCategory $request
     * @param string $url
     * @return JsonResponse
     */
    public function update(StoreUpdateCategory $request, string $url): JsonResponse
    {
        $category = $this->repository->query()->where('url', $url)->firstOrFail();
        $category->update($request->validated());

        return response()->json(['message' => 'success']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $url
     * @return JsonResponse
     */
    public function destroy(string $url): JsonResponse
    {
        $category = $this->repository->query()->where('url', $url)->firstOrFail();
        $category->delete();

        return response()->json([], 204);
    }
}

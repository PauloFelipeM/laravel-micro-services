<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class CompanyService
{
    public function __construct(protected Company $repository)
    {
    }

    public function getCompanies(string $filter = ''): LengthAwarePaginator
    {
        return $this->repository->getCompanies($filter);
    }

    public function createNewCompany(array $data, UploadedFile $image)
    {
        $path = $this->uploadImage($image);
        $data['image'] = $path;

        return $this->repository->query()->create($data);
    }

    public function getCompanyByUUID(string $uuid = null)
    {
        return $this->repository->query()->where('uuid', $uuid)->firstOrFail();
    }

    public function deleteCompany(string $uuid = null)
    {
        $company = $this->getCompanyByUUID($uuid);

        if (Storage::exists($company->image)) {
            Storage::delete($company->image);
        }

        return $company->delete();
    }

    public function updateCompany(array $data, string $uuid = '', UploadedFile $image = null)
    {
        $company = $this->getCompanyByUUID($uuid);

        if ($image) {
            if (Storage::exists($company->image)) {
                Storage::delete($company->image);
            }

            $data['image'] = $this->uploadImage($image);
        }

        return $company->update($data);
    }

    private function uploadImage(UploadedFile $image)
    {
        return $image->store('companies');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Pagination\LengthAwarePaginator;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'uuid',
        'name',
        'url',
        'phone',
        'whatsapp',
        'email',
        'facebook',
        'instagram',
        'youtube',
        'image',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }


    public function getCompanies(string $filter = ''): LengthAwarePaginator
    {
        return $this->with('category')
            ->where(function ($query) use ($filter) {
                if ($filter !== '') {
                    $query->where('name', 'LIKE', "%{$filter}%");
                    $query->orWhere('email', '=', $filter);
                    $query->orWhere('phone', '=', $filter);
                }
            })
            ->paginate();
    }
}

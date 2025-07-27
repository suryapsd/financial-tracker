<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BudgetPlan extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function savings()
    {
        return $this->hasMany(Saving::class, 'goal_id')->where('type', 'plan');
    }

    public function category()
    {
        return $this->belongsTo(Category::class)->where('type', 'plan');
    }
}

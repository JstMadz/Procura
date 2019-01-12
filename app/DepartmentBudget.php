<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class DepartmentBudget extends Pivot
{
    protected $guarded = [];

    protected $table = 'department_budgets';

    public function department(){
        return $this->belongsTo('App\Department');
    }

    public function projects(){
        return $this->hasMany('App\Project', 'department_budget_id');
    }

    public function getTotalAttribute(){
        return bcadd($this->fund_101, $this->fund_164);
    }

    public function getRemainingAttribute(){
        $approvedProjects = $this->projects()->where('is_approved', true)->get();
        
        $allocated = 0;
        foreach($approvedProjects as $project){
            $allocated = bcadd($allocated, $project->total_budget);
        }

        return bcsub($this->total, $allocated);
    }
}

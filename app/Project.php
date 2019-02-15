<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = ['budget_year_id', 'title', 'user_id', 'department_budget_id', 'is_approved'];

    protected $appends = ['total_budget', 'total_budget_with_contingency'];

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function items(){
        return $this->hasMany('App\ProjectItem');
    }

    public function year(){
        return $this->belongsTo('App\BudgetYear', 'budget_year_id');
    }

    public function department_budget(){
        return $this->belongsTo('App\DepartmentBudget');
    }

    public function getApproverAttribute(){
        return $this->department_budget->department->sector->head->user;
    }
    
    public function getDepartmentAttribute(){
        return $this->department_budget->department;
    }

    public function getTotalBudgetAttribute(){
        $items = $this->items;

        $total=0;
        foreach($items as $item){
            $total = bcadd($total, $item->estimated_budget);
        }

        return $total;
    }

    public function getTotalBudgetWithContingencyAttribute(){
        $total = bcadd($this->total_budget, bcmul($this->total_budget, "0.20", 5), 5);
        return number_format($total, 2, ".", "");
    }

    public function scopeApproved($query){
        return $query->where('is_approved', 1);
    }

    public function addItem($attributes){
        // dd($attributes);
        $project_item = $this->items()->create($attributes);
        $project_item->addSchedules($attributes['schedules']);
    }

    public function approve($approved = true){ //public function approve($remarks, $approved = true){
        $this->update(["is_approved" => $approved]);
        // $this->addRemarks($remarks);
    }

    public function reject(){ //    public function reject($remarks){
        // $this->approve($remarks, false);
        $this->approve(false);
    }

    // public function addRemarks($remarks){
    //     $this->update(compact("remarks"));
    // }
}

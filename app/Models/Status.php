<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
	protected $fillable = ['content'];
    //一对多关联，关联User模型,需要在User模型中也创建statuses()方法
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

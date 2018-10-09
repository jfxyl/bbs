<?php

namespace App\Models;

class Topic extends Model
{
	protected $fillable = ['title', 'body', 'category_id', 'excerpt', 'slug'];
	
	public function category()
	{
		return $this->belongsTo(Category::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function scopeWithOrder($query,$order)
	{
		switch($order){
			case 'recent':
				$query->orderBy('created_at','desc');
				break;
			default:
				$query->orderBy('updated_at','desc');
				break;
		}
	}

	public function link($params = [])
	{
		return route('topics.show',array_merge([$this->id,$this->slug],$params));
	}

	public function replies()
	{
		return $this->hasMany(Reply::class);
	}
}
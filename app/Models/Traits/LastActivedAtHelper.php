<?php 
namespace App\Models\Traits;

use Redis;
use Carbon\Carbon;

trait LastActivedAtHelper
{
	// 缓存相关
    protected $hash_prefix = 'larabbs_last_actived_at_';
	protected $field_prefix = 'user_';
	
	public function recordLastActivedAt()
	{
		// 获取今天的日期
        $date = Carbon::now()->toDateString();

        // Redis 哈希表的命名，如：larabbs_last_actived_at_2017-10-21
		$hash = $this->hash_prefix . $date;
		
		// 字段名称，如：user_1
		$field = $this->field_prefix . $this->id;

		// 当前时间，如：2017-10-21 08:35:15
        $now = Carbon::now()->toDateTimeString();

        // 数据写入 Redis ，字段已存在会被更新
        Redis::hSet($hash, $field, $now);
	}

	public function syncUserActivedAt()
	{
		$yesterday_date = Carbon::yesterday()->toDateString();

		$hash = $this->hash_prefix . $yesterday_date;

		$dates = Redis::hGetAll($hash);

		foreach($dates as $user_id => $actived_at) {
			$user_id = str_replace($this->field_prefix,'',$user_id);
			// 只有当用户存在时才更新到数据库中
            if ($user = $this->find($user_id)) {
                $user->last_actived_at = $actived_at;
                $user->save();
            }
		}

		// 以数据库为中心的存储，既已同步，即可删除
        Redis::del($hash);
	}

	public function getLastActivedAtAttribute($value)
	{
		// 获取今天的日期
        $date = Carbon::now()->toDateString();

        // Redis 哈希表的命名，如：larabbs_last_actived_at_2017-10-21
		$hash = $this->hash_prefix . $date;
		
		// 字段名称，如：user_1
		$field = $this->field_prefix . $this->id;

		$datetime = Redis::hGet($hash,$field)?:$value;

		// 如果存在的话，返回时间对应的 Carbon 实体
        if ($datetime) {
            return new Carbon($datetime);
        } else {
        // 否则使用用户注册时间
            return $this->created_at;
        }
	}
}
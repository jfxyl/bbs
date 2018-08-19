<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\UserRequest;
use App\Handlers\ImageUploadHandler;

class UsersController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth',['except' => ['show']]);
	}

	public function show(User $user)
	{
		return view('users.show',['user' => $user]);
	}

	public function edit(User $user)
	{
		$this->authorize('update',$user);
		return view('users.edit',['user' => $user]);
	}

	// public function update(UserRequest $request, User $user)
    // {
	// 	var_dump($user);
	// 	\DB::enableQueryLog();
	// 	var_dump($request->all());
	// 	$user->update(['introduction' => 'sssss']);

	// 	var_dump(\DB::getQueryLog());
	// 	echo 111;
    //     // return redirect()->route('users.show', $user->id)->with('success', '个人资料更新成功！');
	// }
	
	public function update(User $user,UserRequest $request,ImageUploadHandler $uploader)
    {
		$this->authorize('update',$user);
		$data = $request->all();
		if($request->avatar){
			$result = $uploader->save($request->avatar,'avatars',$user->id,362);
			if($result){
				$data['avatar'] = $result['path'];
			}
		}
        $user->update($data);
        return redirect()->route('users.show', $user->id)->with('success', '个人资料更新成功！');
    }
}

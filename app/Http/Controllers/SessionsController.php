<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Auth;

class SessionsController extends Controller
{
	public function __construct(){
		//指定只让未登录用户访问的方法
		$this->middleware('guest',[
			'only'=>['create']
		]);
	}
	//登录页面
    public function create()
    {
        return view('sessions.create');
    }
    
    //登录
    public function store(Request $request){
    	$credentials = $this->validate($request, [
           'email' => 'required|email|max:255',
           'password' => 'required'
        ]);
        if(Auth::attempt($credentials,$request->has('remember'))){
            session()->flash('success', '欢迎回来！');
            //intended方法可以返回到登录前访问的地址
            return redirect()->intended(route('users.show', [Auth::user()]));
        }else{
            session()->flash('danger', '很抱歉，您的邮箱和密码不匹配');
            return redirect()->back();
        }
    }
    
    //退出
    public function destroy()
    {
        Auth::logout();
        session()->flash('success', '您已成功退出！');
        return redirect('login');
    }
}

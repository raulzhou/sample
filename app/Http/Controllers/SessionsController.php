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
            //如果激活了账号，则登录
            if(Auth::user()->activated){
                session()->flash('success', '欢迎回来！');
                //intended方法可以返回到登录前访问的地址
                return redirect()->intended(route('users.show', [Auth::user()]));
            }else{
                //没有激活账号，则不让登录
                Auth::logout();
                session()->flash('warning', '你的账号未激活，请检查邮箱中的注册邮件进行激活。');
                return redirect('/');
            }
            
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

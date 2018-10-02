<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\User;
use Auth;
use Mail;

class UsersController extends Controller
{
    public function __construct(){  
        //指定不需要登录就可访问的方法
        $this->middleware('auth',
            ['except'=>['index','show','create','store','confirmEmail']
        ]);
        //指定只让未登录用户访问的方法
        $this->middleware('guest',[
            'only'=>['create']
        ]);
    }
    
    //用户列表
    public function index(){
       $users = User::paginate(10);
       return view('users.index',compact('users'));
    }
    //注册页面
    public function create()
    {
        return view('users.create');
    }
    
    //注册后跳转页面
    public function show(User $user){
        $statuses = $user->statuses()->orderBy('created_at','DESC')->paginate(10);
    	return view('users.show',compact('user','statuses'));
    }
    
    //注册
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:50',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|min:6'
        ]);
        $user = User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>bcrypt($request->password)
        ]);
        
        //注册成功后，发送激活账号邮件
        $this->sendEmailConfirmationTo($user);
        //Auth::login($user);
        session()->flash('success', '验证邮件已发送到你的注册邮箱上，请注意查收!');
        //return redirect()->route('users.show', [$user]);
        return redirect('/');
    }

    //编辑资料页面
    public function edit(User $user){
        //检测登录用户操作的是否是自己的资料
        $this->authorize('update', $user);
        return view('users.edit',compact('user'));
    }
    
    //编辑资料提交方法
    public function update(User $user, Request $request){
        //验证提交数据
        $this->validate($request,[
            'name'=>'required|max:50',
            'password'=>'nullable|confirmed|min:6'
        ]);
        $this->authorize('update', $user);
        $data = [];
        $data['name'] = $request->name;
        if($request->password){
            $data['password'] = bcrypt($request->password);
        }
        $user->update($data);
        session()->flash('success','编辑资料成功');
        return redirect()->route('users.show', $user->id);
    }

    //删除用户
    public function destroy(User $user){
        $this->authorize('destroy', $user);
        $user->delete();
        session()->flash('success','删除用户成功');
        return back();
    }

    //注册成功后发激活邮件
    public function sendEmailConfirmationTo($user){
        $view = 'emails.confirm';
        $data = compact('user');
        $to = $user->email;
        $subject = "感谢注册 Sample 应用！请确认你的邮箱。";

        Mail::send($view,$data,function($message) use ($to,$subject){
            $message->to($to)->subject($subject);
        });
    }
    
    //接收用户激活方法
    public function confirmEmail($token){
        $user = User::where('activation_token', $token)->firstOrFail();
        $user->activated = true;
        $user->activation_token = null;
        $user->save();

        Auth::login($user);
        session()->flash('success', '恭喜你，激活成功！');
        return redirect()->route('users.show', [$user]);
    }

}

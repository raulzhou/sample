<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    
    //更新时验证更新的必须是自己的资料
    public function update(User $currentUser,User $user){
        return $currentUser->id === $user->id;
    }
    
    //删除时验证必须是管理员，并且不能删除自己
    public function destroy(User $currentUser,User $user){
        return $currentUser->is_admin && $currentUser->id !== $user->id;
    }
}

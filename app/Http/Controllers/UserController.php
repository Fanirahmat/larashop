<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

//use App\User;

class UserController extends Controller
{
    public function __construct(){
        //OTORISASI GATE HAK AKSES
        $this->middleware(function($request,$next){
            if (Gate::allows('manage-users'))
            {
                return $next($request);
            } 
            else 
            {
                abort(403,'anda tidak memiliki cukup hak akses');
            }
        });
    }

    public function index(Request $request)
    {
        $users = \App\User::paginate(10);
        $filterKeyword = $request->get('keyword');
        $status = $request->get('status');
        if ($filterKeyword) 
        {
            if ($status) 
            {
               //menampilkan data berdasarkan keyword dan status
                $users = \App\User::where('email', 'LIKE', "%$filterKeyword%")->where('status', $status)->paginate(10);
            } 
            else 
            {
                //menampilkan data berdasarkan keyword
                $users = \App\User::where('email', 'LIKE', "%$filterKeyword%")->paginate(10);
            }
        }
        else
        {
            if ($status) 
            {
               //menampilkan data berdasarkan status
                $users = \App\User::where('status', $status)->paginate(10);
            } 
            else 
            {
                $users = \App\User::paginate(10);
            }
        } 
        return view('user.index', ['users' => $users]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('user.create');
    }

   
    public function store(Request $request)
    {
        \Validator::make($request->all(),[
            "name" => "required|min:5|max:100",
            "username" => "required|min:5|max:20",
            "roles" => "required",
            "phone" => "required|digits_between:10,12",
            "address" => "required|min:20|max:200",
            "avatar" => "required",
            "email" => "required|email|unique:users",
            "password" => "required",
            "password_confirmation" => "required|same:password"
        ])->validate();

        $new_user = new \App\User;
        $new_user->name = $request->get('name');
        $new_user->username = $request->get('username');
        $new_user->roles = json_encode($request->get('roles'));
        $new_user->address = $request->get('address');
        $new_user->phone = $request->get('phone');
        $new_user->email = $request->get('email');
        $new_user->password = \Hash::make($request->get('password'));

        if($request->file('avatar'))
        {
            $file = $request->file('avatar')->store('avatars', 'public');
            $new_user->avatar = $file;
        }

        $new_user->save();
        return redirect()->route('users.create')->with('status', 'User successfully created');
    }

  
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = \App\User::findOrFail($id);
        return view('user.edit', ['user' => $user]);
    }

    
    public function update(Request $request, $id)
    {
        \Validator::make($request->all(), [
            "name" => "required|min:5|max:100",
            "roles" => "required",
            "phone" => "required|digits_between:10,12",
            "address" => "required|min:20|max:200"
        ])->validate();

        $user = \App\User::findOrFail($id);

        $user->name = $request->get('name');
        $user->roles = json_encode($request->get('roles'));
        $user->address = $request->get('address');
        $user->phone = $request->get('phone');
        if(file_exists(storage_path('app/public/' . $user->avatar)))
        {
            \Storage::delete('public/'.$user->avatar);
            $file = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $file;
        }
        else
        {
            $file = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $file;
        }

        $user->save();
        return redirect()->route('users.edit', [$id])->with('status', 'User
        succesfully updated');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = \App\User::findOrFail($id);
        \Storage::delete('public/'.$user->avatar);
        $user->delete();
        return redirect()->route('users.index')->with('status', 'User successfully deleted');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{

  public function __construct()
  {

  }

  public function index()
  {

//    $role = Role::with('permissions')->where('name', 'Super Admin')->first();
//    $role = Role::create(['name' => 'Super Admin']);
//    $role->givePermissionTo('edit users');
//    dd($role);

    $roles = Role::with('permissions')->get();

    return view('admin.roles.index', compact('roles'));
  }

}

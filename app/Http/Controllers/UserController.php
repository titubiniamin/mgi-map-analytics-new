<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\BloodGroup;
use App\Models\Brand;
use App\Models\Department;
use App\Models\Designation;
use App\Models\District;
use App\Models\Office;
use App\Models\User;
use App\Models\Zone;
use Auth;
use DataTables;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{

    /**
     * Show the users dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(): View
    {

        return view('users');
    }

    /**
     * Show User List
     *
     * @param Request $request
     * @return mixed
     */
    public function getUserList(Request $request): mixed
    {
        $data = User::with('roles', 'permissions','designation','department','zone','district','office', 'bloodGroup','brand')->get();
//        dd($data);
        $hasManageUser = Auth::user()->can('manage_user');

        return Datatables::of($data)
            ->addColumn('designation', function ($data) {
                return $data->designation ? $data->designation->name : 'N/A';
            })
            ->addColumn('department', function ($data) {
                return $data->department ? $data->department->name : 'N/A';
            })
            ->addColumn('zone', function ($data) {
                return $data->zone ? $data->zone->name : 'N/A';
            })
            ->addColumn('district', function ($data) {
                return $data->district ? $data->district->name : 'N/A';
            })
            ->addColumn('office', function ($data) {
                return $data->office ? $data->office->name : 'N/A';
            })
            ->addColumn('bloodGroup', function ($data) {
                return $data->bloodGroup ? $data->bloodGroup->name : 'N/A';
            })
            ->addColumn('employee_id', function ($data) {
                return $data->employee_id ? $data->employee_id : 'N/A';
            })
            ->addColumn('brand', function ($data) {
                return $data->brand ? $data->brand->name : 'N/A';
            })
            ->addColumn('roles', function ($data) {
                $roles = $data->getRoleNames()->toArray();
                $badge = '';
                if ($roles) {
                    $badge = implode(' , ', $roles);
                }

                return $badge;
            })
            ->addColumn('permissions', function ($data) {
                $roles = $data->getAllPermissions();
                $badges = '';
                foreach ($roles as $key => $role) {
                    $badges .= '<span class="badge badge-dark m-1">' . $role->name . '</span>';
                }

                return $badges;
            })
            ->addColumn('action', function ($data) use ($hasManageUser) {
                $output = '';
                if ($data->name == 'Super Admin') {
                    return '';
                }
                if ($hasManageUser) {
                    $output = '<div class="table-actions" style="display: flex">
                                <a href="' . url('user/' . $data->id) . '" ><i class="ik ik-edit f-16 mr-15 text-green"></i></a>
                                <a href="' . url('user/delete/' . $data->id) . '"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                            </div>';
                }

                return $output;
            })
            ->rawColumns(['roles', 'permissions', 'action'])
            ->make(true);
    }

    /**
     * User Create
     *
     * @return mixed
     */
    public function create(): mixed
    {
        try {
            $roles = Role::pluck('name', 'id');
            $designations = Designation::pluck('name', 'id');
            $departments = Department::pluck('name', 'id');
            $zones = Zone::pluck('name', 'id');
            $districts = District::pluck('name', 'id');
            $offices = Office::pluck('name', 'id');
            $blood_groups = BloodGroup::pluck('name', 'id');
            $brands = Brand::pluck('name', 'id');

            return view('create-user', compact('roles','designations','departments','zones','districts','offices','blood_groups','brands'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Store User
     *
     * @param UserRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(UserRequest $request): RedirectResponse
    {
//        dd($request->all());
        try {
            // Store user information
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password, // Ensure password is hashed
                'designation_id' => $request->designation_id, // Store the selected designation
                'department_id' => $request->department_id, // Store the selected department
                'zone_id' => $request->zone_id, // Store the selected zone
                'district_id' => $request->district_id, // Store the selected district
                'office_id' => $request->office_id, // Store the selected office
                'blood_group_id' => $request->blood_group_id, // Store the selected blood group
                'employee_id' => $request->employee_id, // Store the selected blood group
                'brand_id' => $request->brand_id, // Store the selected brand
            ]);

            if ($user) {
                // Assign the selected role to the user
                $user->syncRoles($request->role);

                return redirect('users')->with('success', 'New user created!');
            }

            return redirect('users')->with('error', 'Failed to create new user! Try again.');
        } catch (\Exception $e) {
            $bug = $e->getMessage();

            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Edit User
     *
     * @param int $id
     * @return mixed
     */
    public function edit($id): mixed
    {
        try {
            $designations = Designation::pluck('name', 'id');
            $departments = Department::pluck('name', 'id');
            $zones = Zone::pluck('name', 'id');
            $districts = District::pluck('name', 'id');
            $offices = Office::pluck('name', 'id');
            $blood_groups = BloodGroup::pluck('name', 'id');
            $brands = Brand::pluck('name', 'id');
            $user = User::with('roles', 'permissions','designation','department','zone','district','office', 'bloodGroup','brand')->find($id);
            if ($user) {
                $user_role = $user->roles->first();
                $roles = Role::pluck('name', 'id');

                return view('user-edit', compact('user', 'user_role', 'roles', 'designations','departments', 'zones', 'districts', 'offices', 'blood_groups', 'brands'));
            }

            return redirect('404');
        } catch (\Exception $e) {
            $bug = $e->getMessage();

            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Update User
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request): RedirectResponse
    {
        // Validation rules
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:users,id',
            'name' => 'required|string',
            'email' => 'required|email',
            'role' => 'required|exists:roles,id',
            'designation_id' => 'nullable|exists:designations,id',
            'department_id' => 'nullable|exists:departments,id',
            'zone_id' => 'nullable|exists:zones,id',
            'district_id' => 'nullable|exists:districts,id',
            'office_id' => 'nullable|exists:offices,id',
            'blood_group_id' => 'nullable|exists:blood_groups,id',
            'employee_id' => 'nullable',
            'brand_id' => 'nullable|exists:brands,id',
            'password' => 'nullable|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('error', $validator->messages()->first());
        }

        try {
            DB::beginTransaction();

            // Find the user
            $user = User::findOrFail($request->id);

            // Update user details
            $payload = [
                'name' => $request->name,
                'email' => $request->email,
            ];

            if ($request->filled('password')) {
                $payload['password'] = $request->password;
            }

            $user->update($payload);

            // Update relationships
            $user->designation_id = $request->designation_id;
            $user->department_id = $request->department_id;
            $user->zone_id = $request->zone_id;
            $user->district_id = $request->district_id;
            $user->office_id = $request->office_id;
            $user->blood_group_id = $request->blood_group_id;
            $user->brand_id = $request->brand_id;
            $user->save();

            // Sync roles
            $user->syncRoles([$request->role]);

            DB::commit();

            return redirect()->back()->with('success', 'User information updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Delete User
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id): RedirectResponse
    {
        if ($user = User::find($id)) {
            $user->delete();

            return redirect('users')->with('success', 'User removed!');
        }

        return redirect('users')->with('error', 'User not found');
    }
}

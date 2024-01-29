<?php

namespace App\Http\Controllers;

use App\Models\Birthday;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User; // Import the User model at the top of your controller


class BirthdayController extends Controller
{
    public function birthday()
    {
        $birthday = Birthday::all();
        return view('birthdaylist', ['birthdays' => $birthday]);
    }

    public function birthdayform()
    {
        $users = DB::table('users')
            ->select('name', 'id')
            ->whereNotIn('role', [4])
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        return view('birthdayform', ['users' => $users]);
    }

    public function employeeformpost(Request $request)
    {
        /* form validations  */
        $data = $request->validate([
            'ename' => 'required',
            'eid' => 'required',
            'edob' => 'required',
            'erole' => 'required',
        ]);
        /*  submit form data to database  */
        Birthday::create($data);
        return redirect(route('birthday'))->with('success', 'Data submitted successfully');
    }
    /*  edit form */
    public function employeeformpostedit(Birthday $id)
    {
        $users = DB::table('users')
            ->select('name', 'id')
            ->whereNotIn('role', [4])
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        return view('birthdayformedit', ['id' => $id, 'users' => $users]);
    }

    // public function employeeformpostedit(Birthday $id)
    // {
    //     $users = User::all();

    //     return view('birthdayformedit', ['id' => $id, 'users' => $users]);
    // }

    /* update or put form */
    public function employeeformpostput(Birthday $id, Request $request)
    {
        $data = $request->validate([
            'ename' => 'required',
            'eid' => 'required',
            'edob' => 'required|date',
            // 'erole' => 'required',
            'erole' => 'required', 'regex:/^[a-zA-Z]+$/',
        ]);

        $id->update($data);
        return redirect(route('birthday'))->with('success', 'updated successfully');
    }


    /* ***delete data from database*** */
    public function employeeformpostdelete(Birthday $id, Request $request)
    {

        if ($id->trashed()) {
            $id->forceDelete();
            return redirect(route('trashedlist'))->with('success', 'deleted successfully');
        }


        $id->delete();

        return redirect(route('birthday'))->with('success', 'deleted successfully');
    }
}

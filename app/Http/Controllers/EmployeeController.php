<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pageTitle = 'Employee List';

        // RAW SQL QUERY
        // $employees = DB::select('
        //     select *, employees.id as employee_id, positions.name as position_name
        //     from employees
        //     left join positions on employees.position_id = positions.id
        // ');

        // Query Builder
        $employees = DB::table('employees')->select('employees.*', 'employees.id as employee_id', 'positions.name as position_name')
            ->join('positions', 'positions.id', '=', 'employees.position_id')->get();
        return view('employee.index', [
            'pageTitle' => $pageTitle,
            'employees' => $employees
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Raw SQL
        // $pageTitle = 'Create Employee';
        // $positions = DB::select('select * from positions');

        // Query Builder
        $pageTitle = 'Create Employee';
        $positions = DB::table('positions')->get();
        return view('employee.create', compact('pageTitle', 'positions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $messages = [
            'required' => ':Attribute harus diisi.',
            'email' => 'Isi :attribute dengan format yang benar',
            'numeric' => 'Isi :attribute dengan angka'
        ];

        $validator = Validator::make($request->all(), [
            'firstName' => 'required',
            'lastName' => 'required',
            'email' => 'required|email',
            'age' => 'required|numeric',
        ], $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // INSERT QUERY
        DB::table('employees')->insert([
            'firstname' => $request->firstName,
            'lastname' => $request->lastName,
            'email' => $request->email,
            'age' => $request->age,
            'position_id' => $request->position,
        ]);

        return redirect()->route('employees.index');
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pageTitle = 'Employee Detail';

        // RAW SQL QUERY
        // $employee = collect(DB::select('
        //     select *, employees.id as employee_id, positions.name as position_name
        //     from employees
        //     left join positions on employees.position_id = positions.id
        //     where employees.id = ?
        // ', [$id]))->first();

        // Query Builder
        $employee = DB::table('employees')->select('employees.*', 'positions.name as position_name', 'positions.id as position_id')
            ->leftJoin('positions', 'positions.id', '=', 'employees.position_id')
            ->where('employees.id', $id)
            ->first();
        return view('employee.show', compact('pageTitle', 'employee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pageTitle = 'Edit Employee';
        $positions = DB::table('positions')->get();
        $employee = DB::table('employees')
            ->select('*', 'employees.id as employee_id', 'positions.name as position_name')
            ->leftJoin('positions', 'employees.position_id', 'positions.id')
            ->where('employees.id', $id)
            ->first();

        return view('employee.edit', compact('pageTitle', 'positions', 'employee'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        DB::table('employees')
            ->where('id', $id)
            ->update([
                'firstname' => $request->input('firstName'),
                'lastname' => $request->input('lastName'),
                'email' => $request->input('email'),
                'age' => $request->input('age'),
                'position_id' => $request->input('position')
            ]);
        return redirect()->route('employees.index');


        return view('employee.index', compact('pageTitle', 'employee'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::table('employees')
            ->where('id', $id)
            ->delete();

        return redirect()->route('employees.index');

    }
}

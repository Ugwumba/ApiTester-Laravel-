<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Validator; // Import Validator class
use Illuminate\Validation\ValidationException; // Import ValidationException class for custom validation error responses

class StudentController extends Controller
{
    public function index()
    {

        $student = Student::all();

        $data = [
            'status' => 200,
            'student' => $student
        ];

        return response()->json($data, 200);
    }

    //Add students
    public function add(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email|unique:students,email', // Unique validation rule added
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator); // Throw ValidationException on validation failure
            } else {
                // Check if student with the same name already exists
                $existingStudent = Student::where('name', $request->name)->first();

                if ($existingStudent) {
                    return response()->json([
                        'status' => 422,
                        'message' => 'A student with the same name already exists.'
                    ], 422);
                }

                // Check if student with the same email already exists
                $existingStudentWithEmail = Student::where('email', $request->email)->first();

                if ($existingStudentWithEmail) {
                    return response()->json([
                        'status' => 422,
                        'message' => 'A student with the same email already exists.'
                    ], 422);
                }

                // If no existing student found, proceed to create new student record
                $student = new Student;

                $student->name = $request->name;
                $student->email = $request->email;
                $student->phone = $request->phone;
                $student->save();

                $data = [
                    'status' => 200,
                    'message' => 'Data Uploaded'
                ];
                return response()->json($data, 200);
            }
        } catch (ValidationException $e) {
            // Handle validation errors
            return response()->json([
                'status' => 422,
                'message' => $e->validator->errors()->all()
            ], 422);
        }
    }

    //edit student
    public function edit(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email|unique:students,email,' . $id, // Unique validation rule added with ignoring current student
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator); // Throw ValidationException on validation failure
            } else {

                // If no existing student found, proceed to create new student record
                $student = Student::find($id);

                $student->name = $request->name;
                $student->email = $request->email;
                $student->phone = $request->phone;
                $student->save();

                $data = [
                    'status' => 200,
                    'message' => 'Data Updated Successfully'
                ];
                return response()->json($data, 200);
            }
        } catch (ValidationException $e) {
            // Handle validation errors
            return response()->json([
                'status' => 422,
                'message' => $e->validator->errors()->all()
            ], 422);
        }
    }

    //delete student

    public function delete($id)
    {
        $student = Student::find($id);

        $student->delete();

        $data = [
            'status' => 200,
            'message' => 'Data Deleted Successfully'
        ];

        return response()->json($data, 200);
    }
}

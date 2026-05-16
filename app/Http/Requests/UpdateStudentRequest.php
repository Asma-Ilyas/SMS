<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class UpdateStudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
     // Assumes route model binding

       public function rules()
    {
        $studentId = $this->route('student')->id;
        
        return [
            // Same rules as StoreStudentRequest, but with 'unique' ignoring current student
            'first_name'       => 'required|string|max:100',
            'middle_name'      => 'nullable|string|max:100',
            'last_name'        => 'required|string|max:100',
            'gender'           => ['required', Rule::in(['Male', 'Female'])],
            'date_of_birth'    => 'required|date',
            'dob_in_words'     => 'nullable|string|max:255',
            'religion'         => 'nullable|string|max:100',
            'caste_subcaste'   => 'nullable|string|max:100',
            'blood_group'      => 'nullable|string|max:10',
            'address'          => 'nullable|string',
            'phone'            => 'nullable|string|max:20',
            'email'            => 'nullable|email|max:100',
            'city'             => 'nullable|string|max:100',
            'state'            => 'nullable|string|max:100',
            'country'          => 'nullable|string|max:100',
            'extra_note'       => 'nullable|string',
            'mother_tongue'    => 'nullable|string|max:100',
            'birth_place'      => 'nullable|string|max:200',
            'previous_school_name'    => 'nullable|string|max:200',
            'previous_school_address' => 'nullable|string',
            'previous_class'          => 'nullable|string|max:50',
            'passout_year'            => 'nullable|string|max:10',
            'previous_category'       => 'nullable|string|max:50',
            'admission_date'    => 'required|date',
            'student_type'      => 'required|string|max:100',
            'class_id'          => 'required|exists:classes,id',
            'section'           => 'required|string|max:10',
            'admission_number'  => ['required', 'string', 'max:50', Rule::unique('students')->ignore($studentId)],
            'roll_number'       => 'nullable|string|max:50',
            'profile_photo'     => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'father_name'        => 'required|string|max:100',
            'father_phone'       => 'nullable|string|max:20',
            'father_occupation'  => 'nullable|string|max:100',
            'mother_name'        => 'nullable|string|max:100',
            'mother_phone'       => 'nullable|string|max:20',
            'mother_occupation'  => 'nullable|string|max:100',
            'parent_id_proof'    => 'nullable|file|mimes:jpg,png,pdf|max:2048',
            'parent_signature'   => 'nullable|image|mimes:jpg,png|max:1024',
            'assigned_concession' => 'nullable|string|max:200',
            'status'                => ['required', Rule::in(['Active', 'Inactive'])],
            'suspension_start_date' => 'nullable|date',
            'suspension_end_date'   => 'nullable|date|after_or_equal:suspension_start_date',
            'suspension_message'    => 'nullable|string|max:500',
        ];
    }
         
           
  
    }

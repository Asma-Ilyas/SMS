<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Stream;
use App\Models\ElectiveTrack;

class UpdateClassRequest extends FormRequest
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
    public function rules(): array
    {
        return [
             'academic_session_id' => 'required|exists:academic_sessions,id',
            'grade_id' => 'required|exists:grades,id',
            'stream_id' => 'required|exists:streams,id',
            'elective_track_id' => 'nullable|exists:elective_tracks,id',
            'section' => 'nullable|string|max:10',
            'capacity' => 'nullable|integer|min:1',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $stream = Stream::find($this->stream_id);
            
            if ($stream) {
                if ($stream->name === 'Science' && empty($this->elective_track_id)) {
                    $validator->errors()->add('elective_track_id', 'Science stream requires an elective track.');
                }
                
                if ($stream->name === 'Arts' && !empty($this->elective_track_id)) {
                    $validator->errors()->add('elective_track_id', 'Arts stream cannot have an elective track.');
                }
            }

            if ($this->elective_track_id) {
                $track = ElectiveTrack::find($this->elective_track_id);
                if ($track && $track->stream_id != $this->stream_id) {
                    $validator->errors()->add('elective_track_id', 'Selected track does not belong to the chosen stream.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'academic_session_id.required' => 'Please select an academic session.',
            'grade_id.required' => 'Please select a grade.',
            'stream_id.required' => 'Please select a stream.',
        ];
    }
}

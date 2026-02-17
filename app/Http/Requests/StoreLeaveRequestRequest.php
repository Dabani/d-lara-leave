<?php

namespace App\Http\Requests;

use App\Services\LeaveValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreLeaveRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'leave_type' => 'required|string|in:Casual Leave,Sick Leave,Emergency Leave,Study Leave,Maternity Leave,Paternity Leave,Annual Leave,Without Pay',
            'leave_from' => 'required|date|after_or_equal:today',
            'leave_to' => 'required|date|after_or_equal:leave_from',
            'reason' => 'nullable|string|max:1000',
            'medical_certificate' => 'required_if:leave_type,Sick Leave|nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:2048',
            'supporting_document' => 'required_if:leave_type,Study Leave|nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:2048',
            'is_first_attempt' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'leave_to.after_or_equal' => 'The "Leave To" date must be after or equal to the "Leave From" date.',
            'medical_certificate.required_if' => 'Medical certificate is required for sick leave.',
            'medical_certificate.mimes' => 'Medical certificate must be a PDF, JPG, JPEG, PNG, or WebP file.',
            'medical_certificate.max' => 'Medical certificate file size cannot exceed 2MB.',
            'supporting_document.required_if' => 'Supporting document is required for study leave (exam registration, professional exam notice, etc.).',
            'supporting_document.mimes' => 'Supporting document must be a PDF, JPG, JPEG, PNG, or WebP file.',
            'supporting_document.max' => 'Supporting document file size cannot exceed 2MB.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if (!$validator->errors()->any()) {
                $employee = auth()->user()->employee;
                
                if (!$employee) {
                    $validator->errors()->add('employee', 'Employee record not found.');
                    return;
                }

                $validationService = new LeaveValidationService();
                $result = $validationService->validateLeaveRequest($employee, $this->all());

                if (!$result['valid']) {
                    foreach ($result['errors'] as $error) {
                        $validator->errors()->add('leave_validation', $error);
                    }
                }

                if (!empty($result['warnings']) || !empty($result['info'])) {
                    session()->flash('leave_warnings', $result['warnings'] ?? []);
                    session()->flash('leave_info', $result['info'] ?? []);
                }
            }
            // Flag pre-annual emergency leave
            if ($this->leave_type === 'Emergency Leave') {
                $employee = auth()->user()->employee;
                if ($employee && !$employee->isAnnualLeaveEligible()) {
                    // Mark as pre-annual — allowed but will be deducted later
                    $this->merge(['is_pre_annual_emergency' => true]);
                }
            }
        });
    }
}

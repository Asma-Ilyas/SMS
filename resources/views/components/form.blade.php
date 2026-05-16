@php
    $formConfig = json_decode($data['form_config'] ?? '{}', true);
    $fields = $formConfig['fields'] ?? [];
    $submitText = $formConfig['submit_button_text'] ?? 'Submit';
    $successMessage = $formConfig['success_message'] ?? 'Application submitted successfully!';
    $styles = json_decode($data['style'] ?? '{}', true);
@endphp

<div class="{{ $styles['form_container'] ?? 'bg-white p-6 rounded-lg shadow-md max-w-2xl mx-auto my-10' }}">
    @if(isset($data['title']))
        <h2 class="{{ $styles['title_style'] ?? 'text-2xl font-bold text-center text-gray-800 mb-6' }}">
            {{ $data['title'] }}
        </h2>
    @endif

    <form id="admissionForm">
        @csrf
        @foreach($fields as $field)
            <div class="mb-5">
                <label class="{{ $styles['label'] ?? 'block text-sm font-medium text-gray-700 mb-1' }}">
                    {{ $field['label'] }} @if(($field['required'] ?? false))<span class="text-red-500">*</span>@endif
                </label>

                @if($field['type'] == 'text' || $field['type'] == 'email' || $field['type'] == 'tel')
                    <input type="{{ $field['type'] }}" name="{{ $field['name'] }}"
                           placeholder="{{ $field['placeholder'] ?? '' }}"
                           {{ ($field['required'] ?? false) ? 'required' : '' }}
                           class="{{ $styles['input'] ?? 'mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2' }}">

                @elseif($field['type'] == 'date')
                    <input type="date" name="{{ $field['name'] }}"
                           class="{{ $styles['input'] ?? 'mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2' }}">

                @elseif($field['type'] == 'select')
                    <select name="{{ $field['name'] }}" {{ ($field['required'] ?? false) ? 'required' : '' }}
                            class="{{ $styles['input'] ?? 'mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2' }}">
                        <option value="">Select {{ $field['label'] }}</option>
                        @foreach($field['options'] as $option)
                            <option value="{{ $option }}">{{ $option }}</option>
                        @endforeach
                    </select>

                @elseif($field['type'] == 'textarea')
                    <textarea name="{{ $field['name'] }}" rows="{{ $field['rows'] ?? 3 }}"
                              class="{{ $styles['input'] ?? 'mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2' }}"></textarea>
                @endif
            </div>
        @endforeach

        <button type="submit" class="{{ $styles['submit_button'] ?? 'w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md' }}">
            {{ $submitText }}
        </button>
        <div id="formAlert" class="hidden mt-4 text-sm"></div>
    </form>
</div>

<script>
    document.getElementById('admissionForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const alertDiv = document.getElementById('formAlert');

        fetch('{{ route("admission.apply") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.errors) {
                let errorsHtml = '<div class="text-red-600"><ul class="list-disc pl-5">';
                for (let field in data.errors) {
                    errorsHtml += `<li>${data.errors[field][0]}</li>`;
                }
                errorsHtml += '</ul></div>';
                alertDiv.innerHTML = errorsHtml;
                alertDiv.classList.remove('hidden', 'text-green-600');
                alertDiv.classList.add('text-red-600');
            } else {
                alertDiv.innerHTML = `<div class="text-green-600">${data.message}</div>`;
                alertDiv.classList.remove('hidden', 'text-red-600');
                alertDiv.classList.add('text-green-600');
                this.reset();
                setTimeout(() => {
                    alertDiv.classList.add('hidden');
                    // Optionally redirect or clear
                }, 3000);
            }
        })
        .catch(error => {
            alertDiv.innerHTML = '<div class="text-red-600">An error occurred. Please try again.</div>';
            alertDiv.classList.remove('hidden');
        });
    });
</script>
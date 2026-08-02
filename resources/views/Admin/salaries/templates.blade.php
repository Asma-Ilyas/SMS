@extends('layouts.app')

@section('title', 'Salary Templates')

@section('content')
<div class="container px-4 py-6 max-w-7xl mx-auto">

    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-6 mb-6 text-white">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold flex items-center gap-2">
                    <span>📋</span> Salary Templates
                </h1>
                <p class="text-indigo-100 text-sm mt-1">Manage salary templates for quick payroll generation</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.salaries.index') }}" class="px-4 py-2 bg-white/20 backdrop-blur-sm hover:bg-white/30 rounded-lg transition text-sm font-medium">
                    ← Back to Salary
                </a>
                <button onclick="openCreateModal()" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg transition text-sm font-medium">
                    ➕ New Template
                </button>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl p-4 mb-6 flex items-center gap-3">
            <span class="text-xl">✅</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Templates Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($templates as $template)
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 hover:shadow-md transition">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-slate-800 text-lg">{{ $template->name }}</h3>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $template->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                        {{ $template->is_active ? '✅ Active' : '❌ Inactive' }}
                    </span>
                </div>
                <p class="text-sm text-slate-400 mb-4">{{ $template->description ?? 'No description' }}</p>
                
                <div class="space-y-1 text-sm">
                    <div class="flex justify-between border-b border-slate-50 pb-1">
                        <span class="text-slate-500">Basic Salary</span>
                        <span class="font-semibold text-slate-700">${{ number_format($template->basic_salary, 2) }}</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-50 pb-1">
                        <span class="text-slate-500">Allowances</span>
                        <span class="font-semibold text-slate-700">${{ number_format($template->allowances ?? 0, 2) }}</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-50 pb-1">
                        <span class="text-slate-500">Medical Allowance</span>
                        <span class="font-semibold text-slate-700">${{ number_format($template->medical_allowance ?? 0, 2) }}</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-50 pb-1">
                        <span class="text-slate-500">Transport Allowance</span>
                        <span class="font-semibold text-slate-700">${{ number_format($template->transport_allowance ?? 0, 2) }}</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-50 pb-1">
                        <span class="text-slate-500">PF %</span>
                        <span class="font-semibold text-slate-700">{{ $template->pf_percentage ?? 0 }}%</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-50 pb-1">
                        <span class="text-slate-500">Tax %</span>
                        <span class="font-semibold text-slate-700">{{ $template->tax_percentage ?? 0 }}%</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-50 pb-1 font-bold text-indigo-600">
                        <span>Net Salary</span>
                        <span>${{ number_format($template->basic_salary + ($template->allowances ?? 0) + ($template->medical_allowance ?? 0) + ($template->transport_allowance ?? 0), 2) }}</span>
                    </div>
                </div>

                <div class="flex gap-2 mt-4 pt-4 border-t border-slate-100">
                    <button onclick="openEditModal({{ $template->id }})" 
                            class="flex-1 px-3 py-1.5 bg-amber-100 hover:bg-amber-200 text-amber-700 rounded-lg transition text-xs font-medium">
                        ✏️ Edit
                    </button>
                    <form method="POST" action="{{ route('admin.salaries.templates.destroy', $template->id) }}" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Delete this template?')" 
                                class="w-full px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg transition text-xs font-medium">
                            🗑️ Delete
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12 text-slate-400">
                <span class="text-3xl block mb-2">📋</span>
                <p>No salary templates found</p>
                <button onclick="openCreateModal()" class="mt-3 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition text-sm font-medium">
                    Create First Template
                </button>
            </div>
        @endforelse
    </div>
</div>

<!-- Create/Edit Modal -->
<div id="templateModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-slate-800" id="modalTitle">Create Salary Template</h3>
            <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600 text-xl">&times;</button>
        </div>
        <form method="POST" action="{{ route('admin.salaries.templates.store') }}" id="templateForm">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" name="id" id="templateId" value="">
            
            <div class="space-y-4">
                <div>
                    <label class="text-xs font-semibold text-slate-600 block mb-1">Template Name *</label>
                    <input type="text" name="name" id="templateName" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500" required>
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-600 block mb-1">Description</label>
                    <textarea name="description" id="templateDescription" rows="2" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-semibold text-slate-600 block mb-1">Basic Salary *</label>
                        <input type="number" name="basic_salary" id="templateBasicSalary" step="0.01" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500" required>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-600 block mb-1">Allowances</label>
                        <input type="number" name="allowances" id="templateAllowances" step="0.01" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-600 block mb-1">Medical Allowance</label>
                        <input type="number" name="medical_allowance" id="templateMedical" step="0.01" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-600 block mb-1">Transport Allowance</label>
                        <input type="number" name="transport_allowance" id="templateTransport" step="0.01" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-600 block mb-1">PF Percentage</label>
                        <input type="number" name="pf_percentage" id="templatePf" step="0.01" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-600 block mb-1">Tax Percentage</label>
                        <input type="number" name="tax_percentage" id="templateTax" step="0.01" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" id="templateActive" value="1" checked>
                        <span class="text-sm text-slate-600">Active</span>
                    </label>
                </div>
            </div>

            <div class="flex gap-3 mt-6 pt-4 border-t border-slate-100">
                <button type="button" onclick="closeModal()" class="flex-1 px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg transition text-sm font-medium">Cancel</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition text-sm font-medium">Save Template</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openCreateModal() {
        document.getElementById('modalTitle').textContent = 'Create Salary Template';
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('templateForm').action = "{{ route('admin.salaries.templates.store') }}";
        document.getElementById('templateId').value = '';
        document.getElementById('templateName').value = '';
        document.getElementById('templateDescription').value = '';
        document.getElementById('templateBasicSalary').value = '';
        document.getElementById('templateAllowances').value = '0';
        document.getElementById('templateMedical').value = '0';
        document.getElementById('templateTransport').value = '0';
        document.getElementById('templatePf').value = '5';
        document.getElementById('templateTax').value = '0';
        document.getElementById('templateActive').checked = true;
        document.getElementById('templateModal').classList.remove('hidden');
        document.getElementById('templateModal').classList.add('flex');
    }

    function openEditModal(id) {
        fetch(`/admin/salaries/templates/${id}/edit`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('modalTitle').textContent = 'Edit Salary Template';
                document.getElementById('formMethod').value = 'PUT';
                document.getElementById('templateForm').action = `/admin/salaries/templates/${id}`;
                document.getElementById('templateId').value = data.id;
                document.getElementById('templateName').value = data.name;
                document.getElementById('templateDescription').value = data.description || '';
                document.getElementById('templateBasicSalary').value = data.basic_salary;
                document.getElementById('templateAllowances').value = data.allowances || 0;
                document.getElementById('templateMedical').value = data.medical_allowance || 0;
                document.getElementById('templateTransport').value = data.transport_allowance || 0;
                document.getElementById('templatePf').value = data.pf_percentage || 5;
                document.getElementById('templateTax').value = data.tax_percentage || 0;
                document.getElementById('templateActive').checked = data.is_active;
                document.getElementById('templateModal').classList.remove('hidden');
                document.getElementById('templateModal').classList.add('flex');
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to load template data');
            });
    }

    function closeModal() {
        document.getElementById('templateModal').classList.add('hidden');
        document.getElementById('templateModal').classList.remove('flex');
    }

    document.getElementById('templateModal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
</script>
@endpush
@endsection
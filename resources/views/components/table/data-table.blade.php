@props([
    'columns' => [],
    'rows' => [],
    'actions' => true,
    'editRoute' => null,
    'deleteRoute' => null,
    'routeParameterName' => 'id'
])

<div class="bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100">
            <thead>
                <tr class="bg-gray-50/50">
                    @foreach($columns as $column)
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ $column }}
                        </th>
                    @endforeach
                    @if($actions)
                        <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Actions
                        </th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($rows as $row)
                    <tr class="hover:bg-blue-50/30 transition-colors duration-200">
                        @foreach($row as $key => $value)
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-700 font-medium">{{ $value }}</span>
                            </td>
                        @endforeach

                        @if($actions)
                            <td class="px-6 py-4 text-right whitespace-nowrap text-sm font-medium">
                                <div class="flex justify-end items-center space-x-3">
                                    @if($editRoute)
                                        @php $editParams = [$routeParameterName => $row['id'] ?? $row->id ?? null]; @endphp
                                        <a href="{{ route($editRoute, $editParams) }}" 
                                           class="inline-flex items-center text-indigo-600 hover:text-indigo-700 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition-all">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Edit
                                        </a>
                                    @endif

                                    @if($deleteRoute)
                                        @php $deleteParams = [$routeParameterName => $row['id'] ?? $row->id ?? null]; @endphp
                                        <form action="{{ route($deleteRoute, $deleteParams) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="inline-flex items-center text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition-all">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="100%" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span class="text-gray-500 font-medium text-lg">No records found</span>
                                <p class="text-gray-400 text-sm">Try adjusting your filters or adding a new entry.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if(isset($pagination) && $pagination)
        <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100">
            {{ $pagination }}
        </div>
    @endif
</div>
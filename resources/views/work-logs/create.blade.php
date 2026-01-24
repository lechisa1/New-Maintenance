@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold mb-6">Create Work Log</h1>
            
            <form action="{{ route('work-logs.store') }}" method="POST">
                @csrf
                
                <!-- Maintenance Request -->
                <div class="mb-4">
                    <label for="request_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Maintenance Request *
                    </label>
                    <select name="request_id" id="request_id" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select a request...</option>
                        @foreach($assignedRequests as $request)
                            <option value="{{ $request->id }}" 
                                {{ old('request_id', $maintenanceRequest?->id) == $request->id ? 'selected' : '' }}>
                                {{ $request->ticket_number }} - {{ $request->item?->name }} 
                                ({{ $request->getStatusText() }})
                            </option>
                        @endforeach
                    </select>
                    @error('request_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Work Done -->
                <div class="mb-4">
                    <label for="work_done" class="block text-sm font-medium text-gray-700 mb-2">
                        Work Done *
                    </label>
                    <textarea name="work_done" id="work_done" rows="4" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Describe the work you performed...">{{ old('work_done') }}</textarea>
                    @error('work_done')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Materials Used -->
                <div class="mb-4">
                    <label for="materials_used" class="block text-sm font-medium text-gray-700 mb-2">
                        Materials Used (Optional)
                    </label>
                    <textarea name="materials_used" id="materials_used" rows="2"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="List any materials or parts used...">{{ old('materials_used') }}</textarea>
                    @error('materials_used')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <!-- Time Spent -->
                    <div>
                        <label for="time_spent_minutes" class="block text-sm font-medium text-gray-700 mb-2">
                            Time Spent (minutes) *
                        </label>
                        <input type="number" name="time_spent_minutes" id="time_spent_minutes" required
                            min="1" max="480"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            value="{{ old('time_spent_minutes') }}">
                        @error('time_spent_minutes')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Log Date -->
                    <div>
                        <label for="log_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Date of Work *
                        </label>
                        <input type="date" name="log_date" id="log_date" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            value="{{ old('log_date', date('Y-m-d')) }}">
                        @error('log_date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <!-- Completion Notes -->
                <div class="mb-6">
                    <label for="completion_notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Additional Notes (Optional)
                    </label>
                    <textarea name="completion_notes" id="completion_notes" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Any additional notes or observations...">{{ old('completion_notes') }}</textarea>
                    @error('completion_notes')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Buttons -->
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('work-logs.index') }}"
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Save Work Log
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
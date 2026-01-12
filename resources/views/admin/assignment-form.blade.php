@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Assign Request" />

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
                <i class="bi bi-person-plus me-2"></i>Assign Maintenance Request
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Assign technicians and schedule maintenance work
            </p>
        </div>
        
        <div class="flex gap-2">
            <a href=""
                class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                <i class="bi bi-arrow-left me-2"></i>Back to Request
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Left Column - Assignment Form -->
        <div class="lg:col-span-2">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="mb-6 text-lg font-semibold text-gray-800 dark:text-white/90">
                    <i class="bi bi-clipboard-check me-2"></i>Assignment Details
                </h3>
                <hr class="mb-6 border-gray-200 dark:border-gray-700">

                <!-- Request Summary -->
                <div class="mb-8 rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900/20">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="mb-2 flex items-center gap-3">
                                <span class="text-lg font-bold text-gray-800 dark:text-white/90">MTN-2024-0456</span>
                                <span class="rounded-full px-2 py-0.5 text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">
                                    High Priority
                                </span>
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                <div class="font-medium">PC-001 - Office Computer (Room 101)</div>
                                <div>Issue: Performance Problem - Computer running slow with frequent crashes</div>
                            </div>
                        </div>
                    </div>
                </div>

                <form method="POST" action="#">
                    @csrf
                    
                    <div class="space-y-6">
                        <!-- Technician Assignment -->
                        <div>
                            <h4 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                                <i class="bi bi-person-gear me-1"></i>Assign Technician
                            </h4>
                            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                <!-- Primary Technician -->
                                <div>
                                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Primary Technician <span class="text-red-500">*</span>
                                    </label>
                                    <select name="primary_technician" required
                                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
                                        <option value="" disabled selected>Select technician</option>
                                        <option value="1">Sarah Johnson - IT Support Specialist</option>
                                        <option value="2">Mike Wilson - Senior IT Technician</option>
                                        <option value="3">Alex Chen - Hardware Specialist</option>
                                        <option value="4">Lisa Brown - Network Engineer</option>
                                        <option value="5">David Miller - Software Support</option>
                                    </select>
                                    <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Will be responsible for the main repair work
                                    </div>
                                </div>

                                <!-- Supporting Technician -->
                                <div>
                                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Supporting Technician (Optional)
                                    </label>
                                    <select name="supporting_technician"
                                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
                                        <option value="" selected>Select supporting technician</option>
                                        <option value="2">Mike Wilson - Senior IT Technician</option>
                                        <option value="1">Sarah Johnson - IT Support Specialist</option>
                                        <option value="4">Lisa Brown - Network Engineer</option>
                                        <option value="5">David Miller - Software Support</option>
                                        <option value="3">Alex Chen - Hardware Specialist</option>
                                    </select>
                                    <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Will assist the primary technician
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Schedule Assignment -->
                        <div>
                            <h4 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                                <i class="bi bi-calendar-event me-1"></i>Schedule Assignment
                            </h4>
                            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                <!-- Start Date -->
                                <div>
                                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Start Date <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" name="start_date" required
                                        min="{{ date('Y-m-d') }}"
                                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800"
                                        value="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                </div>

                                <!-- Estimated Completion -->
                                <div>
                                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Estimated Completion <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" name="completion_date" required
                                        min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800"
                                        value="{{ date('Y-m-d', strtotime('+3 days')) }}">
                                </div>
                            </div>
                            
                            <!-- Time Slot -->
                            <div class="mt-4">
                                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Preferred Time Slot
                                </label>
                                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="time_slot" value="morning" 
                                               class="peer sr-only" checked>
                                        <div class="rounded-lg border-2 p-3 text-center transition-all peer-checked:border-blue-500 dark:border-gray-700 peer-checked:dark:border-blue-500 bg-gray-50 dark:bg-gray-800">
                                            <div class="text-sm font-medium">Morning</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">9 AM - 12 PM</div>
                                        </div>
                                    </label>
                                    
                                    <label class="cursor-pointer">
                                        <input type="radio" name="time_slot" value="afternoon"
                                               class="peer sr-only">
                                        <div class="rounded-lg border-2 p-3 text-center transition-all peer-checked:border-blue-500 dark:border-gray-700 peer-checked:dark:border-blue-500 bg-gray-50 dark:bg-gray-800">
                                            <div class="text-sm font-medium">Afternoon</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">1 PM - 4 PM</div>
                                        </div>
                                    </label>
                                    
                                    <label class="cursor-pointer">
                                        <input type="radio" name="time_slot" value="evening"
                                               class="peer sr-only">
                                        <div class="rounded-lg border-2 p-3 text-center transition-all peer-checked:border-blue-500 dark:border-gray-700 peer-checked:dark:border-blue-500 bg-gray-50 dark:bg-gray-800">
                                            <div class="text-sm font-medium">Evening</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">5 PM - 8 PM</div>
                                        </div>
                                    </label>
                                    
                                    <label class="cursor-pointer">
                                        <input type="radio" name="time_slot" value="anytime"
                                               class="peer sr-only">
                                        <div class="rounded-lg border-2 p-3 text-center transition-all peer-checked:border-blue-500 dark:border-gray-700 peer-checked:dark:border-blue-500 bg-gray-50 dark:bg-gray-800">
                                            <div class="text-sm font-medium">Anytime</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">Flexible</div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Estimated Effort & Resources -->
                        <div>
                            <h4 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                                <i class="bi bi-clock me-1"></i>Estimated Effort & Resources
                            </h4>
                            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                <!-- Estimated Hours -->
                                <div>
                                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Estimated Hours <span class="text-red-500">*</span>
                                    </label>
                                    <div class="flex items-center">
                                        <input type="number" name="estimated_hours" min="0.5" max="40" step="0.5" required
                                            value="3"
                                            class="h-11 w-full rounded-lg rounded-r-none border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
                                        <span class="h-11 rounded-r-lg border border-l-0 border-gray-300 bg-gray-50 px-4 py-2.5 text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                                            hours
                                        </span>
                                    </div>
                                </div>

                                <!-- Required Resources -->
                                <div>
                                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Required Resources
                                    </label>
                                    <select name="resources[]" multiple
                                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
                                        <option value="ram">RAM Modules</option>
                                        <option value="ssd">SSD Drive</option>
                                        <option value="tools">Basic Tool Kit</option>
                                        <option value="cables">Replacement Cables</option>
                                        <option value="os">OS Installation Media</option>
                                        <option value="diagnostic">Diagnostic Software</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Assignment Notes -->
                        <div>
                            <h4 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                                <i class="bi bi-journal-text me-1"></i>Assignment Notes
                            </h4>
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Instructions for Technician
                                </label>
                                <textarea name="instructions" rows="4"
                                    class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
                                    placeholder="Provide specific instructions for the technician. Include any special requirements, access information, or specific steps to follow..."></textarea>
                                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    These notes will be visible to the assigned technician(s)
                                </div>
                            </div>
                        </div>

                        <!-- Notifications -->
                        <div>
                            <h4 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                                <i class="bi bi-bell me-1"></i>Notifications
                            </h4>
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input type="checkbox" name="notify_technician" checked
                                        class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:focus:ring-blue-600">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                        Notify assigned technician(s) via email
                                    </span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="notify_requester" checked
                                        class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:focus:ring-blue-600">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                        Notify requester about assignment
                                    </span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="set_reminder"
                                        class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:focus:ring-blue-600">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                        Set reminder for start date
                                    </span>
                                </label>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="border-t border-gray-200 pt-6 dark:border-gray-700">
                            <div class="flex flex-col gap-3 sm:flex-row sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Assignment will update request status to "In Progress"
                                    </p>
                                </div>
                                <div class="flex gap-3">
                                    <button type="button"
                                        class="rounded-lg border border-gray-300 bg-white px-6 py-3 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                                        Save as Draft
                                    </button>
                                    <button type="submit"
                                        class="rounded-lg bg-blue-500 px-6 py-3 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                                        <i class="bi bi-check-lg me-2"></i>Assign Request
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right Column - Technician Info & Preview -->
        <div class="space-y-6">
            <!-- Technician Availability -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                    <i class="bi bi-calendar-check me-2"></i>Technician Availability
                </h3>
                
                <div class="space-y-4">
                    <!-- Sarah Johnson -->
                    <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center dark:bg-blue-900">
                                    <span class="text-xs font-medium text-blue-600 dark:text-blue-300">SJ</span>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-800 dark:text-white/90">Sarah Johnson</div>
                                </div>
                            </div>
                            <span class="rounded-full px-2 py-0.5 text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                Available
                            </span>
                        </div>
                        <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            <i class="bi bi-clock me-1"></i>2 open slots tomorrow
                        </div>
                    </div>
                    
                    <!-- Mike Wilson -->
                    <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center dark:bg-green-900">
                                    <span class="text-xs font-medium text-green-600 dark:text-green-300">MW</span>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-800 dark:text-white/90">Mike Wilson</div>
                                </div>
                            </div>
                            <span class="rounded-full px-2 py-0.5 text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                Available
                            </span>
                        </div>
                        <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            <i class="bi bi-clock me-1"></i>1 open slot this week
                        </div>
                    </div>
                    
                    <!-- Alex Chen -->
                    <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded-full bg-purple-100 flex items-center justify-center dark:bg-purple-900">
                                    <span class="text-xs font-medium text-purple-600 dark:text-purple-300">AC</span>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-800 dark:text-white/90">Alex Chen</div>
                                </div>
                            </div>
                            <span class="rounded-full px-2 py-0.5 text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                Busy
                            </span>
                        </div>
                        <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            <i class="bi bi-clock me-1"></i>Booked until Friday
                        </div>
                    </div>
                    
                    <!-- Lisa Brown -->
                    <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded-full bg-pink-100 flex items-center justify-center dark:bg-pink-900">
                                    <span class="text-xs font-medium text-pink-600 dark:text-pink-300">LB</span>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-800 dark:text-white/90">Lisa Brown</div>
                                </div>
                            </div>
                            <span class="rounded-full px-2 py-0.5 text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                Available
                            </span>
                        </div>
                        <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            <i class="bi bi-clock me-1"></i>3 open slots tomorrow
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <a href="#" class="flex items-center justify-center text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400">
                        <i class="bi bi-calendar-week me-1"></i>
                        View full availability calendar
                    </a>
                </div>
            </div>

            <!-- Assignment Preview -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                    <i class="bi bi-eye me-2"></i>Assignment Preview
                </h3>
                
                <div class="space-y-4">
                    <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-800">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Primary Technician</div>
                        <div class="mt-1 font-medium text-gray-800 dark:text-white/90">Sarah Johnson</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">IT Support Specialist</div>
                    </div>
                    
                    <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-800">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Timeline</div>
                        <div class="mt-1 font-medium text-gray-800 dark:text-white/90">Tomorrow - March 21</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Morning slot (9 AM - 12 PM)</div>
                    </div>
                    
                    <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-800">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Estimated Effort</div>
                        <div class="mt-1 font-medium text-gray-800 dark:text-white/90">3 hours</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Medium complexity</div>
                    </div>
                </div>
            </div>

            <!-- Recent Assignments -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                    <i class="bi bi-list-check me-2"></i>Recent Assignments
                </h3>
                
                <div class="space-y-3">
                    <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-medium text-gray-800 dark:text-white/90">MTN-2024-0453</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Assigned to Mike Wilson</div>
                            </div>
                            <div class="text-right">
                                <div class="text-xs text-gray-500 dark:text-gray-400">Mar 18</div>
                                <span class="text-xs font-medium text-green-600 dark:text-green-400">In Progress</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-medium text-gray-800 dark:text-white/90">MTN-2024-0451</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Assigned to Alex Chen</div>
                            </div>
                            <div class="text-right">
                                <div class="text-xs text-gray-500 dark:text-gray-400">Mar 17</div>
                                <span class="text-xs font-medium text-blue-600 dark:text-blue-400">Completed</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-medium text-gray-800 dark:text-white/90">MTN-2024-0448</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Assigned to Lisa Brown</div>
                            </div>
                            <div class="text-right">
                                <div class="text-xs text-gray-500 dark:text-gray-400">Mar 16</div>
                                <span class="text-xs font-medium text-green-600 dark:text-green-400">In Progress</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Tips -->
            <div class="rounded-2xl border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900/20">
                <h4 class="mb-2 text-sm font-medium text-blue-800 dark:text-blue-200">
                    <i class="bi bi-lightbulb me-1"></i>Assignment Tips
                </h4>
                <ul class="space-y-2 text-sm text-blue-700 dark:text-blue-300">
                    <li class="flex items-start">
                        <i class="bi bi-check-circle me-2 mt-0.5"></i>
                        Match technician skills to request complexity
                    </li>
                    <li class="flex items-start">
                        <i class="bi bi-check-circle me-2 mt-0.5"></i>
                        Consider current workload when assigning
                    </li>
                    <li class="flex items-start">
                        <i class="bi bi-check-circle me-2 mt-0.5"></i>
                        Include clear instructions for better results
                    </li>
                    <li class="flex items-start">
                        <i class="bi bi-check-circle me-2 mt-0.5"></i>
                        Set realistic timelines based on complexity
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update preview based on form inputs
    const form = document.querySelector('form');
    const previewElements = {
        primaryTechnician: document.querySelector('.assignment-preview .primary-technician'),
        timeline: document.querySelector('.assignment-preview .timeline'),
        effort: document.querySelector('.assignment-preview .effort')
    };

    // Listen for form changes to update preview
    if (form) {
        form.addEventListener('change', function(e) {
            if (e.target.name === 'primary_technician') {
                const selectedOption = e.target.options[e.target.selectedIndex];
                if (previewElements.primaryTechnician) {
                    previewElements.primaryTechnician.textContent = selectedOption.text;
                }
            }
            
            if (e.target.name === 'start_date' || e.target.name === 'completion_date' || e.target.name === 'time_slot') {
                const startDate = form.querySelector('[name="start_date"]').value;
                const timeSlot = form.querySelector('[name="time_slot"]:checked');
                if (previewElements.timeline && startDate) {
                    const date = new Date(startDate);
                    const options = { month: 'short', day: 'numeric' };
                    previewElements.timeline.textContent = `${date.toLocaleDateString('en-US', options)}`;
                    
                    if (timeSlot) {
                        const timeText = timeSlot.nextElementSibling.querySelector('.text-xs').textContent;
                        previewElements.timeline.nextElementSibling.textContent = `${timeSlot.value} slot (${timeText})`;
                    }
                }
            }
            
            if (e.target.name === 'estimated_hours') {
                if (previewElements.effort) {
                    previewElements.effort.textContent = `${e.target.value} hours`;
                    
                    // Set complexity based on hours
                    const hours = parseFloat(e.target.value);
                    let complexity = 'Low';
                    if (hours > 4) complexity = 'High';
                    else if (hours > 2) complexity = 'Medium';
                    
                    previewElements.effort.nextElementSibling.textContent = `${complexity} complexity`;
                }
            }
        });
    }

    // Initialize select2 for multiple resources select
    const resourceSelect = document.querySelector('[name="resources[]"]');
    if (resourceSelect) {
        // This would be replaced with Select2 or similar library
        resourceSelect.addEventListener('change', function() {
            console.log('Selected resources:', Array.from(this.selectedOptions).map(opt => opt.value));
        });
    }
});
</script>
@endpush
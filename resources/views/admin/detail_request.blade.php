@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Maintenance Request Details" />

<div class="space-y-6">
    <!-- Header with Actions -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
                <i class="bi bi-ticket-detailed me-2"></i>Maintenance Request Details
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                View and manage maintenance request information
            </p>
        </div>
        
        <div class="flex gap-2">
            <button type="button"
                class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                <i class="bi bi-printer me-2"></i>Print
            </button>
            <button type="button"
                class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                <i class="bi bi-share me-2"></i>Share
            </button>
            <button type="button"
                class="rounded-lg bg-blue-500 px-4 py-2 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600 dark:focus:ring-offset-gray-900">
                <i class="bi bi-pencil-square me-2"></i>Edit Request
            </button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Left Column - Request Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Request Summary Card -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="mb-6 flex items-start justify-between">
                    <div>
                        <div class="mb-2 flex items-center gap-3">
                            <span class="text-2xl font-bold text-gray-800 dark:text-white/90">MTN-2024-0456</span>
                            <span class="rounded-full px-3 py-1 text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">
                                High Priority
                            </span>
                            <span class="rounded-full px-3 py-1 text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                In Progress
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Created on March 15, 2024 • Last updated: March 18, 2024
                        </p>
                    </div>
                    
                    <div class="text-right">
                        <div class="text-sm font-medium text-gray-800 dark:text-white/90">Estimated Completion</div>
                        <div class="mt-1 text-lg font-semibold text-blue-600 dark:text-blue-400">March 22, 2024</div>
                    </div>
                </div>

                <hr class="mb-6 border-gray-200 dark:border-gray-700">

                <!-- Equipment Details -->
                <div class="mb-6">
                    <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                        <i class="bi bi-pc-display me-2"></i>Equipment Information
                    </h3>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-800">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Equipment</div>
                            <div class="mt-1 font-medium text-gray-800 dark:text-white/90">PC-001 - Office Computer</div>
                            <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">Dell OptiPlex 3080</div>
                        </div>
                        
                        <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-800">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Location</div>
                            <div class="mt-1 font-medium text-gray-800 dark:text-white/90">Room 101, Main Building</div>
                            <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">Department: Sales</div>
                        </div>
                    </div>
                </div>

                <!-- Problem Description -->
                <div class="mb-6">
                    <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                        <i class="bi bi-chat-text me-2"></i>Problem Description
                    </h3>
                    <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-800">
                        <div class="text-sm text-gray-500 dark:text-gray-400 mb-2">Description</div>
                        <p class="text-gray-800 dark:text-white/90">
                            Computer is running very slow, especially when opening multiple applications. Takes about 5 minutes to boot up completely. 
                            Frequent crashes when using Microsoft Office applications. Noticed the issue starting on Monday morning (March 11). 
                            Tried restarting multiple times and running Windows updates, but no improvement.
                        </p>
                        
                        <div class="mt-4 grid grid-cols-2 gap-4">
                            <div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Issue Type</div>
                                <div class="mt-1 font-medium text-gray-800 dark:text-white/90">Performance Problem</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Reported By</div>
                                <div class="mt-1 font-medium text-gray-800 dark:text-white/90">John Doe (Sales Team)</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Attached Files -->
                <div class="mb-6">
                    <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                        <i class="bi bi-paperclip me-2"></i>Attached Files
                    </h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                            <div class="flex items-center">
                                <i class="bi bi-file-image text-2xl text-blue-500"></i>
                                <div class="ml-3">
                                    <div class="font-medium text-gray-800 dark:text-white/90">error_screenshot.png</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">2.4 MB • Added on March 15, 2024</div>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <button class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                    <i class="bi bi-download"></i>
                                </button>
                                <button class="text-gray-400 hover:text-gray-600 dark:text-gray-500">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                            <div class="flex items-center">
                                <i class="bi bi-file-text text-2xl text-green-500"></i>
                                <div class="ml-3">
                                    <div class="font-medium text-gray-800 dark:text-white/90">system_specs.txt</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">15 KB • Added on March 15, 2024</div>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <button class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                    <i class="bi bi-download"></i>
                                </button>
                                <button class="text-gray-400 hover:text-gray-600 dark:text-gray-500">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Updates/Progress -->
                <div>
                    <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                        <i class="bi bi-clock-history me-2"></i>Updates & Progress
                    </h3>
                    <div class="space-y-4">
                        <!-- Update 1 -->
                        <div class="relative pl-8">
                            <div class="absolute left-0 top-0 h-full w-px bg-blue-200 dark:bg-blue-800"></div>
                            <div class="absolute left-0 top-0 -translate-x-1/2">
                                <div class="h-4 w-4 rounded-full bg-blue-500"></div>
                            </div>
                            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                                <div class="flex items-center justify-between">
                                    <div class="font-medium text-gray-800 dark:text-white/90">Diagnosis Started</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">March 16, 2024 • 10:30 AM</div>
                                </div>
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                    Initial diagnostics performed. Found high CPU usage from background processes.
                                </p>
                                <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                    Updated by: Sarah Johnson (IT Support)
                                </div>
                            </div>
                        </div>
                        
                        <!-- Update 2 -->
                        <div class="relative pl-8">
                            <div class="absolute left-0 top-0 h-full w-px bg-blue-200 dark:bg-blue-800"></div>
                            <div class="absolute left-0 top-0 -translate-x-1/2">
                                <div class="h-4 w-4 rounded-full bg-green-500"></div>
                            </div>
                            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                                <div class="flex items-center justify-between">
                                    <div class="font-medium text-gray-800 dark:text-white/90">Parts Ordered</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">March 17, 2024 • 2:15 PM</div>
                                </div>
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                    Ordered additional 8GB RAM and SSD replacement. Expected delivery on March 20.
                                </p>
                                <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                    Updated by: Mike Wilson (IT Support)
                                </div>
                            </div>
                        </div>
                        
                        <!-- Update 3 -->
                        <div class="relative pl-8">
                            <div class="absolute left-0 top-0 h-full w-px bg-blue-200 dark:bg-blue-800"></div>
                            <div class="absolute left-0 top-0 -translate-x-1/2">
                                <div class="h-4 w-4 rounded-full bg-yellow-500"></div>
                            </div>
                            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                                <div class="flex items-center justify-between">
                                    <div class="font-medium text-gray-800 dark:text-white/90">Scheduled for Repair</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">March 18, 2024 • 9:00 AM</div>
                                </div>
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                    Scheduled hardware upgrade for March 21, 2024. User has been notified.
                                </p>
                                <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                    Updated by: Sarah Johnson (IT Support)
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Update/Comment -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                    <i class="bi bi-plus-circle me-2"></i>Add Update or Comment
                </h3>
                <form>
                    <div class="mb-4">
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Update Type
                        </label>
                        <select class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
                            <option value="progress">Progress Update</option>
                            <option value="diagnosis">Diagnosis Update</option>
                            <option value="parts">Parts Update</option>
                            <option value="completion">Completion Update</option>
                            <option value="comment">General Comment</option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Description
                        </label>
                        <textarea rows="3" 
                            class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
                            placeholder="Add details about the update..."></textarea>
                    </div>
                    
                    <div class="mb-4">
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Status Update (Optional)
                        </label>
                        <div class="flex flex-wrap gap-2">
                            <label class="inline-flex items-center">
                                <input type="radio" name="status" value="pending" class="h-4 w-4 text-blue-600">
                                <span class="ml-2 text-sm">Pending</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="status" value="in_progress" checked class="h-4 w-4 text-blue-600">
                                <span class="ml-2 text-sm">In Progress</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="status" value="completed" class="h-4 w-4 text-blue-600">
                                <span class="ml-2 text-sm">Completed</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="status" value="cancelled" class="h-4 w-4 text-blue-600">
                                <span class="ml-2 text-sm">Cancelled</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit"
                            class="rounded-lg bg-blue-500 px-6 py-3 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                            <i class="bi bi-plus-lg me-2"></i>Add Update
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right Column - Side Information -->
        <div class="space-y-6">
            <!-- Assignment & Timeline -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                    <i class="bi bi-person-check me-2"></i>Assignment & Timeline
                </h3>
                
                <!-- Assigned To -->
                <div class="mb-6">
                    <div class="mb-2 text-sm text-gray-500 dark:text-gray-400">Assigned To</div>
                    <div class="flex items-center">
                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center dark:bg-blue-900">
                            <span class="font-medium text-blue-600 dark:text-blue-300">SJ</span>
                        </div>
                        <div class="ml-3">
                            <div class="font-medium text-gray-800 dark:text-white/90">Sarah Johnson</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">IT Support Specialist</div>
                        </div>
                    </div>
                </div>
                
                <!-- Timeline -->
                <div>
                    <div class="mb-4 text-sm text-gray-500 dark:text-gray-400">Timeline</div>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-600 dark:text-gray-400">Request Created</div>
                            <div class="text-sm font-medium text-gray-800 dark:text-white/90">Mar 15</div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-600 dark:text-gray-400">Diagnosis Started</div>
                            <div class="text-sm font-medium text-gray-800 dark:text-white/90">Mar 16</div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-600 dark:text-gray-400">Parts Ordered</div>
                            <div class="text-sm font-medium text-gray-800 dark:text-white/90">Mar 17</div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-600 dark:text-gray-400">Scheduled Repair</div>
                            <div class="text-sm font-medium text-gray-800 dark:text-white/90">Mar 21</div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-600 dark:text-gray-400">Estimated Completion</div>
                            <div class="text-sm font-medium text-gray-800 dark:text-white/90">Mar 22</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cost & Resources -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                    <i class="bi bi-currency-dollar me-2"></i>Cost & Resources
                </h3>
                
                <div class="space-y-4">
                    <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-800">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Estimated Cost</div>
                        <div class="mt-1 text-2xl font-bold text-gray-800 dark:text-white/90">$245.00</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Includes parts and labor</div>
                    </div>
                    
                    <div>
                        <div class="mb-2 text-sm text-gray-500 dark:text-gray-400">Required Parts</div>
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">8GB DDR4 RAM</span>
                                <span class="font-medium text-gray-800 dark:text-white/90">$45.00</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">512GB SSD</span>
                                <span class="font-medium text-gray-800 dark:text-white/90">$75.00</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Labor (2 hours)</span>
                                <span class="font-medium text-gray-800 dark:text-white/90">$125.00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                    <i class="bi bi-telephone me-2"></i>Contact Information
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <div class="mb-1 text-sm text-gray-500 dark:text-gray-400">Requested By</div>
                        <div class="font-medium text-gray-800 dark:text-white/90">John Doe</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Sales Department</div>
                    </div>
                    
                    <div>
                        <div class="mb-1 text-sm text-gray-500 dark:text-gray-400">Contact</div>
                        <div class="font-medium text-gray-800 dark:text-white/90">john.doe@company.com</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Ext. 2345</div>
                    </div>
                    
                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button class="flex w-full items-center justify-center gap-2 rounded-lg border border-blue-200 bg-blue-50 px-4 py-2.5 text-sm font-medium text-blue-700 hover:bg-blue-100 dark:border-blue-800 dark:bg-blue-900/20 dark:text-blue-300 dark:hover:bg-blue-900/30">
                            <i class="bi bi-envelope"></i>
                            Send Update Email
                        </button>
                    </div>
                </div>
            </div>

            <!-- Related Requests -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                    <i class="bi bi-link me-2"></i>Related Requests
                </h3>
                
                <div class="space-y-3">
                    <a href="#" class="block rounded-lg border border-gray-200 p-3 hover:border-blue-300 hover:bg-blue-50 dark:border-gray-700 dark:hover:border-blue-800 dark:hover:bg-blue-900/10">
                        <div class="flex items-center justify-between">
                            <div class="font-medium text-gray-800 dark:text-white/90">MTN-2024-0421</div>
                            <span class="rounded-full px-2 py-0.5 text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                Completed
                            </span>
                        </div>
                        <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">PC-001 - Previous RAM upgrade</div>
                        <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">Feb 28, 2024</div>
                    </a>
                    
                    <a href="#" class="block rounded-lg border border-gray-200 p-3 hover:border-blue-300 hover:bg-blue-50 dark:border-gray-700 dark:hover:border-blue-800 dark:hover:bg-blue-900/10">
                        <div class="flex items-center justify-between">
                            <div class="font-medium text-gray-800 dark:text-white/90">MTN-2024-0389</div>
                            <span class="rounded-full px-2 py-0.5 text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                In Progress
                            </span>
                        </div>
                        <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">PC-002 - Same department</div>
                        <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">Mar 10, 2024</div>
                    </a>
                    
                    <a href="#" class="block rounded-lg border border-gray-200 p-3 hover:border-blue-300 hover:bg-blue-50 dark:border-gray-700 dark:hover:border-blue-800 dark:hover:bg-blue-900/10">
                        <div class="flex items-center justify-between">
                            <div class="font-medium text-gray-800 dark:text-white/90">MTN-2024-0442</div>
                            <span class="rounded-full px-2 py-0.5 text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                Pending
                            </span>
                        </div>
                        <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">Laptop-001 - Similar issue</div>
                        <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">Mar 14, 2024</div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions Footer -->
<div class="mt-8 rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="text-sm text-gray-600 dark:text-gray-400">
            <i class="bi bi-info-circle me-1"></i>
            Need help? Contact IT Support at ext. 1234 or email support@company.com
        </div>
        <div class="flex gap-2">
            <button type="button"
                class="rounded-lg border border-red-300 bg-white px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-50 dark:border-red-700 dark:bg-red-900/20 dark:text-red-300 dark:hover:bg-red-900/30">
                <i class="bi bi-x-circle me-2"></i>Cancel Request
            </button>
            <button type="button"
                class="rounded-lg bg-green-500 px-4 py-2 text-sm font-medium text-white hover:bg-green-600">
                <i class="bi bi-check-circle me-2"></i>Mark as Completed
            </button>
        </div>
    </div>
</div>
@endsection
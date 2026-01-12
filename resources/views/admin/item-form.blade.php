@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Register Equipment" />

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
                <i class="bi bi-plus-circle me-2"></i>Add equipment for maintenance requests
            </h2>
   
        </div>
        
        <div class="flex gap-2">
            <a href="#"
                class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                <i class="bi bi-list-ul me-2"></i>View Equipment List
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Left Column - Registration Form -->
        <div class="lg:col-span-2">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="mb-6 text-lg font-semibold text-gray-800 dark:text-white/90">
                    <i class="bi bi-pc-display me-2"></i>Equipment Information
                </h3>
                <hr class="mb-6 border-gray-200 dark:border-gray-700">

                <form method="POST" action="#">
                    @csrf
                    
                    <div class="space-y-6">
                        <!-- Equipment Name -->
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Equipment Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" required
                                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800"
                                placeholder="E.g., Office Computer, Printer, Air Conditioner">
                            <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                Enter a clear and descriptive name for the equipment
                            </div>
                        </div>

                        <!-- Equipment Type -->
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Equipment Type <span class="text-red-500">*</span>
                            </label>
                            <select name="type" required
                                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
                                <option value="" disabled selected>Select equipment type</option>
                                <option value="computer">Computer</option>
                                <option value="printer">Printer</option>
                                <option value="aircon">Air Conditioner</option>
                                <option value="furniture">Furniture</option>
                                <option value="electrical">Electrical Equipment</option>
                                <option value="plumbing">Plumbing Fixture</option>
                                <option value="vehicle">Vehicle</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <!-- Unit of Measure -->
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Unit of Measure <span class="text-red-500">*</span>
                            </label>
                            <select name="unit" required
                                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
                                <option value="" disabled selected>Select unit</option>
                                <option value="unit">Unit</option>
                                <option value="piece">Piece</option>
                                <option value="set">Set</option>
                                <option value="system">System</option>
                                <option value="device">Device</option>
                            </select>
                            <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                How this equipment is measured or counted
                            </div>
                        </div>

<!-- Status -->
<div>
    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
        Status <span class="text-red-500">*</span>
    </label>
    <div class="grid grid-cols-3 gap-3">
        <label class="cursor-pointer">
            <input type="radio" name="status" value="active" checked required
                   class="peer sr-only">
            <div class="rounded-lg border-2 p-4 text-center transition-all peer-checked:border-green-500 peer-checked:bg-green-50 dark:border-gray-700 dark:peer-checked:border-green-500 dark:peer-checked:bg-green-900/20">
                <div class="flex items-center justify-center gap-2">
                    <div class="h-3 w-3 rounded-full bg-green-500"></div>
                    <div class="text-sm font-medium text-gray-800 dark:text-white/90">Active</div>
                </div>
                <div class="mt-1 text-xs text-gray-600 dark:text-gray-400">
                    Available for maintenance
                </div>
            </div>
        </label>
        
        <label class="cursor-pointer">
            <input type="radio" name="status" value="inactive"
                   class="peer sr-only">
            <div class="rounded-lg border-2 p-4 text-center transition-all peer-checked:border-gray-500 peer-checked:bg-gray-50 dark:border-gray-700 dark:peer-checked:border-gray-500 dark:peer-checked:bg-gray-800">
                <div class="flex items-center justify-center gap-2">
                    <div class="h-3 w-3 rounded-full bg-gray-500"></div>
                    <div class="text-sm font-medium text-gray-800 dark:text-white/90">Inactive</div>
                </div>
                <div class="mt-1 text-xs text-gray-600 dark:text-gray-400">
                    Not in use
                </div>
            </div>
        </label>
        
        <label class="cursor-pointer">
            <input type="radio" name="status" value="maintenance"
                   class="peer sr-only">
            <div class="rounded-lg border-2 p-4 text-center transition-all peer-checked:border-yellow-500 peer-checked:bg-yellow-50 dark:border-gray-700 dark:peer-checked:border-yellow-500 dark:peer-checked:bg-yellow-900/20">
                <div class="flex items-center justify-center gap-2">
                    <div class="h-3 w-3 rounded-full bg-yellow-500"></div>
                    <div class="text-sm font-medium text-gray-800 dark:text-white/90">Under Maintenance</div>
                </div>
                <div class="mt-1 text-xs text-gray-600 dark:text-gray-400">
                    Currently being repaired
                </div>
            </div>
        </label>
    </div>
</div>

                        <!-- Submit Buttons -->
                        <div class="border-t border-gray-200 pt-6 dark:border-gray-700">
                            <div class="flex justify-end gap-3">
                                <button type="reset"
                                    class="rounded-lg border border-gray-300 bg-white px-6 py-3 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                                    <i class="bi bi-x-lg me-2"></i>Reset
                                </button>
                                <button type="submit"
                                    class="rounded-lg bg-blue-500 px-6 py-3 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                                    <i class="bi bi-save me-2"></i>Register Equipment
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right Column - Equipment List Preview -->
        <div class="space-y-6">
            <!-- Recently Added Equipment -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                    <i class="bi bi-clock-history me-2"></i>Recently Added
                </h3>
                
                <div class="space-y-3">
                    <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-medium text-gray-800 dark:text-white/90">Office Computer</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Computer • Unit</div>
                            </div>
                            <span class="rounded-full px-2 py-0.5 text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                Active
                            </span>
                        </div>
                    </div>
                    
                    <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-medium text-gray-800 dark:text-white/90">Laser Printer</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Printer • Unit</div>
                            </div>
                            <span class="rounded-full px-2 py-0.5 text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                Active
                            </span>
                        </div>
                    </div>
                    
                    <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-medium text-gray-800 dark:text-white/90">Meeting Table</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Furniture • Set</div>
                            </div>
                            <span class="rounded-full px-2 py-0.5 text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                Active
                            </span>
                        </div>
                    </div>
                    
                    <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-medium text-gray-800 dark:text-white/90">Air Conditioner</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Aircon • Unit</div>
                            </div>
                            <span class="rounded-full px-2 py-0.5 text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                Active
                            </span>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Equipment Statistics -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                    <i class="bi bi-bar-chart me-2"></i>Equipment Summary
                </h3>
                
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div class="rounded-lg bg-gray-50 p-3 dark:bg-gray-800">
                            <div class="text-xs text-gray-500 dark:text-gray-400">Total Equipment</div>
                            <div class="mt-1 text-lg font-semibold text-gray-800 dark:text-white/90">24</div>
                        </div>
                        
                        <div class="rounded-lg bg-gray-50 p-3 dark:bg-gray-800">
                            <div class="text-xs text-gray-500 dark:text-gray-400">Active</div>
                            <div class="mt-1 text-lg font-semibold text-green-600 dark:text-green-400">22</div>
                        </div>
                    </div>
                    
                    <div>
                        <div class="mb-2 text-xs text-gray-500 dark:text-gray-400">By Type</div>
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Computers</span>
                                <span class="text-sm font-medium text-gray-800 dark:text-white/90">8</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Printers</span>
                                <span class="text-sm font-medium text-gray-800 dark:text-white/90">5</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Air Conditioners</span>
                                <span class="text-sm font-medium text-gray-800 dark:text-white/90">4</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Furniture</span>
                                <span class="text-sm font-medium text-gray-800 dark:text-white/90">7</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Purpose Notice -->
<div class="mt-6 rounded-lg border border-green-200 bg-green-50 p-4 dark:border-green-800 dark:bg-green-900/20">
    <div class="flex items-start">
        <i class="bi bi-shield-check me-2 mt-0.5 text-green-500"></i>
        <div>
            <h4 class="text-sm font-medium text-green-800 dark:text-green-200">Equipment Registration Purpose</h4>
            <p class="mt-1 text-sm text-green-700 dark:text-green-300">
                This system is designed for registering equipment that needs maintenance. 
                Employees can only request maintenance for registered equipment. 
                Keep equipment names clear and descriptive for easy identification.
            </p>
        </div>
    </div>
</div>
@endsection
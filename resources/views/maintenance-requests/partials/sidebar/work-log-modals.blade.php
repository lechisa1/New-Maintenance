 @include('maintenance-requests.partials.alerts')
 <!-- Work Log Submit Modal -->
 @if (auth()->user()->id == $maintenanceRequest->assigned_to &&
         in_array($maintenanceRequest->status, ['assigned', 'in_progress', 'approved', 'pending', 'not_fixed']))
     <div x-data="{
         showWorkLogModal: false,
         hours: 0,
         minutes: 0
     }" @open-worklog-submit-modal.window="showWorkLogModal = true">



         <div x-show="showWorkLogModal" x-cloak class="fixed inset-0 z-[999] overflow-y-auto" aria-labelledby="modal-title"
             role="dialog" aria-modal="true">

             <div x-show="showWorkLogModal" x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200" @click="showWorkLogModal = false"
                 class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm"></div>

             <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                 <div x-show="showWorkLogModal" x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     class="relative w-full max-w-2xl transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all dark:bg-gray-900">

                     <!-- Modal content remains the same -->
                     <div
                         class="flex items-center justify-between border-b border-gray-100 px-6 py-4 dark:border-gray-800 mt-15">
                         <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                             <i class="bi bi-file-earmark-medical me-2 text-blue-500"></i>Submit Work Progress
                         </h3>
                         <button @click="showWorkLogModal = false"
                             class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                             <i class="bi bi-x-lg"></i>
                         </button>
                     </div>

                     <form id="work-log-form" action="{{ route('work-logs.store') }}" method="POST"
                         class="max-h-[80vh] overflow-y-auto p-6">
                         @csrf
                         <input type="hidden" name="request_id" value="{{ $maintenanceRequest->id }}">

                         <div class="space-y-6">
                             <div>
                                 <label
                                     class="mb-3 block text-sm font-semibold text-gray-700 dark:text-gray-300">Updated
                                     Request Status *</label>
                                 <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                                     @foreach (['in_progress' => ['blue', 'tools', 'In Progress'], 'completed' => ['green', 'check-circle', 'Completed'], 'not_fixed' => ['red', 'x-circle', 'Not Fixed']] as $val => $meta)
                                         <label class="relative flex cursor-pointer group">
                                             <input type="radio" name="new_status" value="{{ $val }}"
                                                 class="peer sr-only"
                                                 {{ $maintenanceRequest->status === $val ? 'checked' : '' }} required>
                                             <div
                                                 class="flex w-full flex-col items-center rounded-xl border border-gray-200 p-3 transition-all peer-checked:border-{{ $meta[0] }}-500 peer-checked:bg-{{ $meta[0] }}-50/50 dark:border-gray-800 dark:peer-checked:bg-{{ $meta[0] }}-500/10">
                                                 <i
                                                     class="bi bi-{{ $meta[1] }} mb-1 text-{{ $meta[0] }}-500 text-xl"></i>
                                                 <span
                                                     class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-tight">{{ $meta[2] }}</span>
                                             </div>
                                         </label>
                                     @endforeach
                                 </div>
                             </div>

                             <div>
                                 <label
                                     class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">Detailed
                                     Work Description *</label>
                                 <textarea name="work_done" rows="3" required
                                     class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-gray-800 dark:bg-gray-800/50 dark:text-white"
                                     placeholder="Provide a professional summary of tasks completed..."></textarea>
                             </div>

                             <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                 <div>
                                     <label
                                         class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">Materials
                                         Used</label>
                                     <textarea name="materials_used" rows="2"
                                         class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm dark:border-gray-800 dark:bg-gray-800/50 dark:text-white"
                                         placeholder="Parts or tools used..."></textarea>
                                 </div>
                                 <div>
                                     <label
                                         class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">Additional
                                         Notes</label>
                                     <textarea name="completion_notes" rows="2"
                                         class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm dark:border-gray-800 dark:bg-gray-800/50 dark:text-white"
                                         placeholder="Challenges or observations..."></textarea>
                                 </div>
                             </div>

                             <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                 <div
                                     class="rounded-xl border border-gray-100 bg-gray-50/30 p-4 dark:border-gray-800 dark:bg-gray-800/30">
                                     <label
                                         class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">Time
                                         Spent</label>
                                     <div class="flex items-center gap-2">
                                         <input type="number" min="0" x-model.number="hours"
                                             class="w-20 rounded-lg border-gray-200 text-center dark:bg-gray-900 dark:text-white"
                                             placeholder="Hrs">
                                         <span class="font-bold">:</span>
                                         <select x-model.number="minutes"
                                             class="flex-1 rounded-lg border-gray-200 dark:bg-gray-900 dark:text-white">
                                             <option value="0">00 mins</option>
                                             <option value="15">15 mins</option>
                                             <option value="30">30 mins</option>
                                             <option value="45">45 mins</option>
                                         </select>
                                     </div>
                                     <input type="hidden" name="time_spent_minutes" :value="(hours * 60) + minutes">
                                 </div>

                                 <div
                                     class="rounded-xl border border-gray-100 bg-gray-50/30 p-4 dark:border-gray-800 dark:bg-gray-800/30">
                                     <label
                                         class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">Completion
                                         Date</label>
                                     <input type="date" name="log_date" required value="{{ date('Y-m-d') }}"
                                         class="w-full rounded-lg border-gray-200 py-2 dark:bg-gray-900 dark:text-white">
                                 </div>
                             </div>
                         </div>

                         <div
                             class="mt-8 flex items-center justify-end gap-3 border-t border-gray-100 pt-6 dark:border-gray-800">
                             <button type="button" @click="showWorkLogModal = false"
                                 class="text-sm font-medium text-gray-500 hover:text-gray-700">
                                 Cancel
                             </button>
                             <button type="submit"
                                 class="flex items-center rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-blue-500/20 transition-all hover:bg-blue-700 active:scale-95">
                                 <i class="bi bi-cloud-arrow-up me-2"></i> Save Progress
                             </button>
                         </div>
                     </form>
                 </div>
             </div>
         </div>
     </div>
 @endif




 </div>

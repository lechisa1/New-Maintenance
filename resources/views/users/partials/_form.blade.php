<form id="userForm" method="POST">
    @csrf
    <input type="hidden" name="_method" id="formMethod" value="POST">


    <div class="grid grid-cols-1 gap-6">
        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900/50">



            <div class="p-6 space-y-8">

                {{-- Section 1: Personal Info --}}
                <section>
                    <h4 class="mb-5 flex items-center text-sm font-bold uppercase tracking-wider text-gray-500">
                        <i class="bi bi-person-vcard mr-2 text-blue-500"></i> Personal Information
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">Full
                                Name <span class="text-red-500">*</span></label>
                            <input type="text" name="full_name" value="{{ old('full_name') }}"
                                class="w-full rounded-lg border-gray-300 bg-gray-50 px-4 py-2.5 text-sm transition focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 @error('full_name') border-red-500 @enderror"
                                placeholder="John Doe">
                            @error('full_name')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">Email
                                Address <span class="text-red-500">*</span></label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                class="w-full rounded-lg border-gray-300 bg-gray-50 px-4 py-2.5 text-sm transition focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 @error('email') border-red-500 @enderror"
                                placeholder="john@example.com">
                            @error('email')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">Phone
                                Number</label>
                            <input type="text" name="phone" value="{{ old('phone') }}"
                                class="w-full rounded-lg border-gray-300 bg-gray-50 px-4 py-2.5 text-sm transition focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800"
                                placeholder="+1 (555) 000-0000">
                        </div>
                    </div>
                </section>

                <hr class="border-gray-100 dark:border-gray-800">

                {{-- Section 2: Assignment & Role --}}
                <section>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <div>
                            <h4 class="mb-5 flex items-center text-sm font-bold uppercase tracking-wider text-gray-500">
                                <i class="bi bi-diagram-3 mr-2 text-blue-500"></i> Cluster/Division Assignment
                            </h4>

                            <div class="mb-6 grid grid-cols-2 gap-4">
                                <label
                                    class="group relative flex cursor-pointer rounded-xl border border-gray-200 p-4 transition hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-800">
                                    <input type="radio" name="assign_type" value="cluster" class="peer sr-only"
                                        {{ old('assign_type', 'cluster') == 'cluster' ? 'checked' : '' }}>
                                    <div class="flex flex-col peer-checked:text-blue-600">
                                        <span class="text-sm font-bold uppercase">Cluster</span>
                                        <span class="text-xs text-gray-500">Main Group Level</span>
                                    </div>
                                    <i
                                        class="bi bi-check-circle-fill absolute right-4 top-1/2 -translate-y-1/2 opacity-0 peer-checked:opacity-100 text-blue-600 transition"></i>
                                </label>

                                <label
                                    class="group relative flex cursor-pointer rounded-xl border border-gray-200 p-4 transition hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-800">
                                    <input type="radio" name="assign_type" value="division" class="peer sr-only"
                                        {{ old('assign_type') == 'division' ? 'checked' : '' }}>
                                    <div class="flex flex-col peer-checked:text-blue-600">
                                        <span class="text-sm font-bold uppercase">Division</span>
                                        <span class="text-xs text-gray-500">Specific Unit Level</span>
                                    </div>
                                    <i
                                        class="bi bi-check-circle-fill absolute right-4 top-1/2 -translate-y-1/2 opacity-0 peer-checked:opacity-100 text-blue-600 transition"></i>
                                </label>
                            </div>

                            <div id="cluster-wrapper">
                                <select name="cluster_id"
                                    class="w-full rounded-lg border-gray-300 bg-gray-50 px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800">
                                    <option value="">Select Cluster</option>
                                    @foreach ($clusters as $cluster)
                                        <option value="{{ $cluster->id }}"
                                            {{ old('cluster_id') == $cluster->id ? 'selected' : '' }}>
                                            {{ $cluster->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="division-wrapper" class="hidden">
                                <select name="division_id"
                                    class="w-full rounded-lg border-gray-300 bg-gray-50 px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800">
                                    <option value="">Select Division</option>
                                    @foreach ($divisions as $division)
                                        <option value="{{ $division->id }}"
                                            {{ old('division_id') == $division->id ? 'selected' : '' }}>
                                            {{ $division->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <h4 class="mb-5 flex items-center text-sm font-bold uppercase tracking-wider text-gray-500">
                                <i class="bi bi-shield-lock mr-2 text-blue-500"></i> Account Permissions
                            </h4>
                            <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">System
                                Role <span class="text-red-500">*</span></label>
                            <select name="roles"
                                class="w-full rounded-lg border-gray-300 bg-gray-50 px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 @error('roles') border-red-500 @enderror">
                                <option value="">Select a Role</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->name }}"
                                        {{ old('roles') == $role->name ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('roles')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </section>

                <hr class="border-gray-100 dark:border-gray-800">

                {{-- Section 3: Security --}}
                <section>
                    <h4 class="mb-5 flex items-center text-sm font-bold uppercase tracking-wider text-gray-500">
                        <i class="bi bi-key mr-2 text-blue-500"></i> Security Credentials
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div x-data="{ show: false }" class="relative">
                            <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">Password
                                <span class="text-red-500">*</span></label>
                            <input :type="show ? 'text' : 'password'" name="password"
                                class="w-full rounded-lg border-gray-300 bg-gray-50 px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 @error('password') border-red-500 @enderror">
                            <button type="button" @click="show = !show"
                                class="absolute right-3 top-[38px] text-gray-400 hover:text-gray-600 transition">
                                <i class="bi" :class="show ? 'bi-eye-slash' : 'bi-eye'"></i>
                            </button>
                            @error('password')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">Confirm
                                Password <span class="text-red-500">*</span></label>
                            <input type="password" name="password_confirmation"
                                class="w-full rounded-lg border-gray-300 bg-gray-50 px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800">
                        </div>
                    </div>
                </section>

            </div>

            {{-- Footer Actions --}}
            <div
                class="flex items-center justify-end gap-4 border-t border-gray-200 bg-gray-50/50 p-6 dark:border-gray-800 dark:bg-gray-900/50">
                <a href="{{ route('users.index') }}"
                    class="text-sm font-bold text-gray-500 hover:text-gray-700 transition dark:hover:text-gray-300">
                    Discard Changes
                </a>
                <button type="submit" id="submitBtn"
                    class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-8 py-2.5 text-sm font-bold text-white shadow-lg shadow-blue-200 transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:shadow-none">
                    <i id="btnIcon" class="bi bi-save mr-2"></i>
                    <span id="btnText">Create User</span>
                </button>
            </div>
        </div>
    </div>
</form>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const cluster = document.getElementById('cluster-wrapper');
        const division = document.getElementById('division-wrapper');
        const radios = document.querySelectorAll('input[name="assign_type"]');
        const form = document.getElementById('userForm');
        const submitBtn = document.getElementById('submitBtn');
        const btnIcon = document.getElementById('btnIcon');
        const btnText = document.getElementById('btnText');

        // Toggle Cluster/Division logic
        function toggle() {
            const type = document.querySelector('input[name="assign_type"]:checked').value;
            if (type === 'cluster') {
                cluster.classList.remove('hidden');
                division.classList.add('hidden');
            } else {
                cluster.classList.add('hidden');
                division.classList.remove('hidden');
            }
        }

        radios.forEach(r => r.addEventListener('change', toggle));
        toggle();

        // Professional Loading State on Submit
        form.addEventListener('submit', () => {
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
            btnIcon.classList.replace('bi-save', 'bi-hourglass-split');
            btnIcon.classList.add('animate-spin');
            btnText.innerText = 'Processing...';
        });

        // Auto-hide success alert after 5 seconds
        const successAlert = document.getElementById('alert-success');
        if (successAlert) {
            setTimeout(() => {
                successAlert.style.transition = 'opacity 0.5s ease';
                successAlert.style.opacity = '0';
                setTimeout(() => successAlert.remove(), 500);
            }, 5000);
        }
    });
</script>

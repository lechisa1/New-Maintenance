@extends('layouts.app')
@php
    $breadcrumbs = [
        ['label' => 'Home', 'url' => url('/')],
        ['label' => 'Maintenance Requests', 'url' => route('maintenance-requests.index')],
        ['label' => "Request #{$maintenanceRequest->ticket_number} Details"],
    ];
@endphp
@section('content')
    <x-common.page-breadcrumb :breadcrumbs="$breadcrumbs" />

    @include('maintenance-requests.partials.alerts')

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <!-- Request Details Card -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                @include('maintenance-requests.partials.header')

                <hr class="my-6 border-gray-200 dark:border-gray-700">

                <!-- Request Information -->
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    @include('maintenance-requests.partials.request-info.equipment-info')
                    @include('maintenance-requests.partials.request-info.request-details')
                </div>

                @include('maintenance-requests.partials.request-info.problem-description')
                @include('maintenance-requests.partials.request-info.attached-files')
                @include('maintenance-requests.partials.request-info.technician-notes')
                @include('maintenance-requests.partials.request-info.status-timeline')
            </div>
        </div>

        <!-- Sidebar - Actions & Info -->
        <div class="space-y-6">
            @include('maintenance-requests.partials.sidebar.quick-actions')
            @include('maintenance-requests.partials.sidebar.approval-section')
            @include('maintenance-requests.partials.sidebar.work-log-section')
            @include('maintenance-requests.partials.sidebar.similar-requests')
        </div>
    </div>

    @include('maintenance-requests.partials.modals.assign-technician')
    @include('maintenance-requests.partials.modals.update-status')
    @include('maintenance-requests.partials.modals.approve-request')
    @include('maintenance-requests.partials.modals.reject-request')
    @include('maintenance-requests.partials.modals.preview-modal')
    @include('maintenance-requests.partials.sidebar.work-log-modals')
    {{-- Add this near the bottom of your show.blade.php --}}
    <div x-data="{
        init() {
            // Listen for modal open events
            this.$el.addEventListener('open-update-status-modal', () => {
                this.$dispatch('open-modal', 'updateStatus');
            });
            this.$el.addEventListener('open-approve-modal', () => {
                this.$dispatch('open-modal', 'approve');
            });
            this.$el.addEventListener('open-reject-modal', () => {
                this.$dispatch('open-modal', 'reject');
            });
            this.$el.addEventListener('open-worklog-modal', () => {
                this.$dispatch('open-modal', 'worklog');
            });
        }
    }">
    </div>
    {{-- @if (session('success'))
        <div class="fixed bottom-4 right-4 z-50">
            <x-ui.alert variant="success" title="Success" :message="session('success')" />
        </div>
    @endif --}}
@endsection

@push('scripts')
    <script src="{{ asset('js/maintenance-requests/main.js') }}"></script>
    <script src="{{ asset('js/maintenance-requests/file-preview.js') }}"></script>
    <script src="{{ asset('js/maintenance-requests/work-logs.js') }}"></script>
@endpush

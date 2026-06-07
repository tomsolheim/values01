@extends('layouts.app')

@section('content')

    <div class="row g-3 mb-3 align-items-stretch" data-top-area>
        <div class="col-12 col-md-4" data-top-position="left" data-top-card="top01">
            <div class="card shadow-sm border-0" style="height: 150px;">
                <div class="card-body p-4">
                    <h1 class="h3 fw-bold text-dark mb-2">Values and assets</h1>
                    <p class="text-muted mb-0">Historic data</p>
                    <div class="mt-3" data-top01-purple-line style="width: 24mm; height: 3mm; background: #6f42c1;"></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-8" data-top-position="right">
            <div class="row g-3 justify-content-end h-100" data-top-right-group>
                <div class="col-12 col-md-6 ms-md-auto" data-card-toggle="top08">
                    <livewire:table-size-widget />
                </div>
                <div class="col-12 col-md-6" data-card-toggle="top09">
                    <livewire:instance-info />
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12 col-md-3">
            <livewire:card-selector />
            <livewire:time-widget />
            <livewire:git-status />
            <livewire:system-status />
        </div>

        <div class="col-12 col-md-9">
            <div data-card-toggle="workbench">
                <ul class="nav nav-tabs" id="workbenchTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tab01-tab" data-bs-toggle="tab" data-bs-target="#tab01" type="button" role="tab">Status</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab02-tab" data-bs-toggle="tab" data-bs-target="#tab02" type="button" role="tab">Assets</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab03-tab" data-bs-toggle="tab" data-bs-target="#tab03" type="button" role="tab">Bundles</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab04-tab" data-bs-toggle="tab" data-bs-target="#tab04" type="button" role="tab">Areas</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab05-tab" data-bs-toggle="tab" data-bs-target="#tab05" type="button" role="tab">Holdings</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab06-tab" data-bs-toggle="tab" data-bs-target="#tab06" type="button" role="tab">History</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab07-tab" data-bs-toggle="tab" data-bs-target="#tab07" type="button" role="tab">Import</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab08-tab" data-bs-toggle="tab" data-bs-target="#tab08" type="button" role="tab">Links</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab09-tab" data-bs-toggle="tab" data-bs-target="#tab09" type="button" role="tab">Variables</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab10-tab" data-bs-toggle="tab" data-bs-target="#tab10" type="button" role="tab">tab10</button>
                    </li>
                </ul>

                <div class="tab-content border border-top-0 rounded-bottom p-3 bg-white" id="workbenchTabContent">
                <div class="tab-pane fade show active" id="tab01" role="tabpanel">
                    <livewire:status-widget />
                </div>
                <div class="tab-pane fade" id="tab02" role="tabpanel">
                    <livewire:asset-widget />
                </div>
                <div class="tab-pane fade" id="tab03" role="tabpanel">
                    <livewire:bundle-widget />
                </div>
                <div class="tab-pane fade" id="tab04" role="tabpanel">
                    <livewire:area-widget />
                </div>
                <div class="tab-pane fade" id="tab05" role="tabpanel">
                    <p class="text-muted small mb-0">info05</p>
                </div>
                <div class="tab-pane fade" id="tab06" role="tabpanel">
                    <p class="text-muted small mb-0">info06</p>
                </div>
                <div class="tab-pane fade" id="tab07" role="tabpanel">
                    <livewire:transaction-import-widget />
                </div>
                <div class="tab-pane fade" id="tab08" role="tabpanel">
                    <livewire:links-widget />
                </div>
                <div class="tab-pane fade" id="tab09" role="tabpanel">
                    <livewire:variables-widget />
                </div>
                <div class="tab-pane fade" id="tab10" role="tabpanel">
                    <p class="text-muted small mb-0">info10</p>
                </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('tab-persistence')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var triggerEl = document.querySelector('#workbenchTabs .nav-link.active');
    var hash = window.location.hash;

    if (hash && document.querySelector('[data-bs-target="' + hash + '"]')) {
        triggerEl = document.querySelector('[data-bs-target="' + hash + '"]');
    }

    if (triggerEl) {
        window.bootstrap.Tab.getOrCreateInstance(triggerEl).show();
    }

    document.querySelectorAll('#workbenchTabs [data-bs-toggle="tab"]').forEach(function (tab) {
        tab.addEventListener('shown.bs.tab', function (e) {
            history.replaceState(null, '', e.target.getAttribute('data-bs-target'));
        });
    });

    window.values01OpenAssetsTabFromStatus = function () {
        var assetsTab = document.getElementById('tab02-tab');

        if (assetsTab && window.bootstrap) {
            window.bootstrap.Tab.getOrCreateInstance(assetsTab).show();
            history.replaceState(null, '', '#tab02');
        }

        window.setTimeout(function () {
            var bundleFilter = document.querySelector('[data-asset-bundle-filter]');

            if (bundleFilter) {
                bundleFilter.focus();
            }
        }, 0);
    };
});
</script>
@endpush

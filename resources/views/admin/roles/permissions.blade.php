@extends("layouts.admin")
@section("title", "Permissions - " . $role->name)
@section("content")
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Permission Matrix — <span class="badge bg-primary">{{ $role->name }}</span></h4>
    <a href="{{ route('admin.roles.index') }}" class="btn btn-light">Back</a>
</div>
<div class="card"><div class="card-body">
    <form action="{{ route('admin.roles.permissions.update', $role) }}" method="POST">
        @csrf
        <div class="row g-3">
        @foreach($permissions as $module => $perms)
            <div class="col-md-4">
                <div class="border rounded p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <b>{{ $module }}</b>
                        <a href="#" class="small select-all" data-module="{{ $module }}">Select all</a>
                    </div>
                    @foreach($perms as $perm)
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input perm-{{ $module }}" name="permissions[]" value="{{ $perm->id }}" id="perm{{ $perm->id }}" {{ in_array($perm->id, $assigned) ? 'checked' : '' }}>
                            <label class="form-check-label" for="perm{{ $perm->id }}">{{ $perm->name }}</label>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
        </div>
        <button class="btn btn-primary mt-4"><i class="bi bi-check2"></i> Save Permissions</button>
    </form>
</div></div>
@endsection
@push("scripts")
<script>
$(".select-all").on("click", function (e) {
    e.preventDefault();
    const mod = $(this).data("module");
    $(`.perm-${mod}`).prop("checked", true);
});
</script>
@endpush

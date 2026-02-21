{{--
    Shared form partial: resources/views/members/partials/form.blade.php
    Used by both create.blade.php and edit.blade.php
    $member is available in edit (old() values used first)
--}}

@push('styles')
<style>
:root { --navy:#0f1f3d; --gold:#c9a84c; }

.form-card {
    background:#fff;
    border:1px solid #e4e9f0;
    border-radius:16px;
    overflow:hidden;
    margin-bottom:20px;
    animation:fadeUp .3s ease both;
}
.form-card-header {
    padding:16px 24px;
    border-bottom:1px solid #f0f3f8;
    background:#fafbfd;
    display:flex; align-items:center; gap:10px;
}
.form-card-header i { font-size:18px; color:var(--gold); }
.form-card-header h4 {
    font-family:'Playfair Display',serif;
    font-size:15px; font-weight:700;
    color:var(--navy); margin:0;
}
.form-card-body { padding:22px 24px; }

.form-label {
    font-size:12px; font-weight:600;
    text-transform:uppercase; letter-spacing:.8px;
    color:#5a7194; margin-bottom:6px;
}
.required-star { color:#c0392b; margin-left:2px; }

.form-control, .form-select {
    border-radius:10px;
    border:1px solid #dde3ef;
    font-size:14px;
    padding:9px 14px;
    color:#1a2b44;
    transition:border-color .2s, box-shadow .2s;
}
.form-control:focus, .form-select:focus {
    border-color:var(--gold);
    box-shadow:0 0 0 3px rgba(201,168,76,.15);
    outline:none;
}
.form-control.is-invalid, .form-select.is-invalid {
    border-color:#dc3545;
}
.invalid-feedback { font-size:11.5px; }

.child-row {
    background:#f8f9fc;
    border:1px solid #e8ecf5;
    border-radius:10px;
    padding:12px 14px;
    margin-bottom:10px;
    position:relative;
}
.remove-child {
    position:absolute; top:10px; right:12px;
    background:#fdecea; border:none;
    color:#c0392b; border-radius:6px;
    font-size:11px; padding:3px 8px;
    cursor:pointer;
}
.remove-child:hover { background:#f5c0bc; }

.btn-add-child {
    background:#f4f6fb; color:#1e3a5f;
    border:1.5px dashed #c0cfe0; border-radius:10px;
    padding:8px 16px; font-size:13px; font-weight:600;
    cursor:pointer; width:100%;
    transition:all .2s;
}
.btn-add-child:hover { background:#ddf5f1; border-color:#0e9578; color:#0e9578; }

.btn-submit {
    background:var(--navy); color:#fff;
    border:none; border-radius:10px;
    padding:11px 28px; font-size:14px; font-weight:600;
    cursor:pointer; transition:background .2s;
    display:inline-flex; align-items:center; gap:7px;
}
.btn-submit:hover { background:#1e3a5f; }

.btn-cancel {
    background:#f4f6fb; color:#3d5270;
    border:1px solid #dde3ef; border-radius:10px;
    padding:11px 22px; font-size:14px; font-weight:600;
    text-decoration:none;
    display:inline-flex; align-items:center; gap:7px;
}
.btn-cancel:hover { background:#e4e9f0; color:#0f1f3d; }

@keyframes fadeUp {
    from{opacity:0;transform:translateY(14px)}
    to{opacity:1;transform:translateY(0)}
}
</style>
@endpush

{{-- Section helper --}}
@php
    $val = fn(string $field, $default = '') => old($field, $member->{$field} ?? $default);
    $err = fn(string $field) => $errors->first($field);
@endphp

{{-- ════ PERSONAL INFORMATION ═══════════════════════════ --}}
<div class="form-card" style="animation-delay:.05s">
    <div class="form-card-header">
        <i class="bi bi-person-badge-fill"></i>
        <h4>Personal Information</h4>
    </div>
    <div class="form-card-body">
        <div class="row g-3">
            <div class="row">
                @if($type === 'existing')
                    <div class="col-md-4">
                        <label class="form-label">Membership No. <span class="required-star">*</span></label>
                        <input type="number" name="membership_number" class="form-control @error('membership_number') is-invalid @enderror"
                            value="{{ $val('membership_number') }}" placeholder="Membership Number">
                        @error('membership_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                @endif

                <div class="col-md-8">
                    <label class="form-label">Full Name <span class="required-star">*</span></label>
                    <input type="text" name="name_with_initials" class="form-control @error('name_with_initials') is-invalid @enderror"
                        value="{{ $val('name_with_initials') }}" required placeholder="e.g. A.B.C. Perera">
                    @error('name_with_initials')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label">Birthday <span class="required-star">*</span></label>
                <input type="date" name="birthday" class="form-control @error('birthday') is-invalid @enderror"
                       value="{{ $val('birthday') }}" required max="{{ now()->subDay()->format('Y-m-d') }}">
                @error('birthday')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">Gender <span class="required-star">*</span></label>
                <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                    <option value="">— Select —</option>
                    <option value="Male"   {{ $val('gender') === 'Male'   ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ $val('gender') === 'Female' ? 'selected' : '' }}>Female</option>
                </select>
                @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">Marital Status <span class="required-star">*</span></label>
                <select name="married" id="marriedSelect" class="form-select @error('married') is-invalid @enderror" required>
                    <option value="">— Select —</option>
                    <option value="1" {{ $val('married', '') == '1' || $val('married', '') === true  ? 'selected' : '' }}>Married</option>
                    <option value="0" {{ $val('married', '') == '0' && $val('married', '') !== ''   ? 'selected' : '' }}>Unmarried</option>
                </select>
                @error('married')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">NIC Number <span class="required-star">*</span></label>
                <input type="text" name="nic_number" class="form-control @error('nic_number') is-invalid @enderror"
                       value="{{ $val('nic_number') }}" required placeholder="e.g. 199012345678 or 901234567V"
                       style="font-family:monospace;letter-spacing:.5px;">
                @error('nic_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">Phone Number <span class="required-star">*</span></label>
                <input type="text" name="phone_number" class="form-control @error('phone_number') is-invalid @enderror"
                       value="{{ $val('phone_number') }}" required placeholder="e.g. 0771234567">
                @error('phone_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                       value="{{ $val('email') }}" placeholder="optional">
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">Occupation <span class="required-star">*</span></label>
                <input type="text" name="occupation" class="form-control @error('occupation') is-invalid @enderror"
                       value="{{ $val('occupation') }}" required>
                @error('occupation')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">Address <span class="required-star">*</span></label>
                <textarea name="address" rows="2" class="form-control @error('address') is-invalid @enderror"
                          required>{{ $val('address') }}</textarea>
                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">Current City <span class="required-star">*</span></label>
                <input type="text" name="current_city" class="form-control @error('current_city') is-invalid @enderror"
                       value="{{ $val('current_city') }}" required>
                @error('current_city')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

        </div>
    </div>
</div>

{{-- ════ ELECTORAL / GN DIVISION ════════════════════════ --}}
<div class="form-card" style="animation-delay:.1s">
    <div class="form-card-header">
        <i class="bi bi-geo-alt-fill"></i>
        <h4>Electoral &amp; Administrative Division</h4>
    </div>
    <div class="form-card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">District <span class="required-star">*</span></label>
                <input type="text" name="district" class="form-control @error('district') is-invalid @enderror"
                       value="{{ $val('district') }}" required>
                @error('district')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Election Division <span class="required-star">*</span></label>
                <input type="text" name="election_division" class="form-control @error('election_division') is-invalid @enderror"
                       value="{{ $val('election_division') }}" required>
                @error('election_division')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Grama Niladhari Division <span class="required-star">*</span></label>
                <input type="text" name="grama_niladhari_division" class="form-control @error('grama_niladhari_division') is-invalid @enderror"
                       value="{{ $val('grama_niladhari_division') }}" required>
                @error('grama_niladhari_division')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>
</div>

{{-- ════ SCHOOL INFORMATION ══════════════════════════════ --}}
<div class="form-card" style="animation-delay:.15s">
    <div class="form-card-header">
        <i class="bi bi-mortarboard-fill"></i>
        <h4>School Information</h4>
    </div>
    <div class="form-card-body">
        <div class="row g-3">
            @if($type === 'existing')
                <div class="col-md-4">
                    <label class="form-label">Joined Date for Old Student Association</label><span class="required-star">*</span>
                    <input type="date" name="joined_date" class="form-control @error('joined_date') is-invalid @enderror"
                           value="{{ $val('school_name') }}" required>
                    @error('school_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            @endif
            <div class="col-md-4">
                <label class="form-label">School Register Year</label>
                <input type="number" name="school_register_year" class="form-control @error('school_register_year') is-invalid @enderror"
                       value="{{ $val('school_register_year') }}" min="1900" max="{{ now()->year }}" placeholder="e.g. 1995">
                @error('school_register_year')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Admission Number</label>
                <input type="text" name="admission_number" class="form-control @error('admission_number') is-invalid @enderror"
                       value="{{ $val('admission_number') }}" placeholder="optional">
                @error('admission_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Date Joined School</label>
                <input type="date" name="date_joined_school" class="form-control @error('date_joined_school') is-invalid @enderror"
                       value="{{ $val('date_joined_school') }}">
                @error('date_joined_school')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>
</div>

{{-- ════ FAMILY INFORMATION ══════════════════════════════ --}}
<div class="form-card" style="animation-delay:.2s">
    <div class="form-card-header">
        <i class="bi bi-house-heart-fill"></i>
        <h4>Family Information</h4>
    </div>
    <div class="form-card-body">

        {{-- Children --}}
        <div class="mb-4">
            <label class="form-label d-block mb-2">
                <i class="bi bi-people me-1"></i> Children &amp; Their Schools
                <span style="font-weight:400;color:#8494a9;">(if applicable)</span>
            </label>
            <div id="childrenContainer">
                @php
                    $existingChildren = old('children', isset($member) ? ($member->children_info ?? []) : []);
                    if (empty($existingChildren)) $existingChildren = []; // start empty
                @endphp
                @foreach($existingChildren as $i => $child)
                <div class="child-row" id="child-{{ $i }}">
                    <div class="row g-2">
                        <div class="col-md-5">
                            <input type="text" name="children[{{ $i }}][name]"
                                   class="form-control" placeholder="Child's Name"
                                   value="{{ is_array($child) ? ($child['name'] ?? '') : '' }}">
                        </div>
                        <div class="col-md-6">
                            <input type="text" name="children[{{ $i }}][school]"
                                   class="form-control" placeholder="School Name"
                                   value="{{ is_array($child) ? ($child['school'] ?? '') : '' }}">
                        </div>
                    </div>
                    <button type="button" class="remove-child" onclick="removeChild('child-{{ $i }}')">
                        <i class="bi bi-x"></i> Remove
                    </button>
                </div>
                @endforeach
            </div>
            <button type="button" class="btn-add-child mt-1" onclick="addChild()">
                <i class="bi bi-plus-circle me-1"></i> Add Child
            </button>
        </div>

        {{-- Siblings (only when unmarried) --}}
        <div id="siblingsSection">
            <label class="form-label">
                <i class="bi bi-person-lines-fill me-1"></i> Siblings Information
                <span style="font-weight:400;color:#8494a9;">(if unmarried)</span>
            </label>
            <textarea name="siblings_info" rows="3"
                      class="form-control @error('siblings_info') is-invalid @enderror"
                      placeholder="Names and details of siblings…">{{ $val('siblings_info') }}</textarea>
            @error('siblings_info')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

    </div>
</div>

{{-- ════ FORM ACTIONS ════════════════════════════════════ --}}
<div class="d-flex align-items-center gap-3 pb-2" style="animation:fadeUp .3s .25s ease both;">
    <button type="submit" class="btn-submit">
        <i class="bi bi-check-circle-fill"></i>
        {{ isset($member) ? 'Update Member' : 'Register Member' }}
    </button>
    <a href="{{ route('members.index') }}" class="btn-cancel">
        <i class="bi bi-arrow-left"></i> Cancel
    </a>
</div>

@push('scripts')
<script>
let childIndex = {{ count($existingChildren ?? []) }};

function addChild() {
    const container = document.getElementById('childrenContainer');
    const id = 'child-' + childIndex;
    container.insertAdjacentHTML('beforeend', `
        <div class="child-row" id="${id}">
            <div class="row g-2">
                <div class="col-md-5">
                    <input type="text" name="children[${childIndex}][name]"
                           class="form-control" placeholder="Child's Name">
                </div>
                <div class="col-md-6">
                    <input type="text" name="children[${childIndex}][school]"
                           class="form-control" placeholder="School Name">
                </div>
            </div>
            <button type="button" class="remove-child" onclick="removeChild('${id}')">
                <i class="bi bi-x"></i> Remove
            </button>
        </div>
    `);
    childIndex++;
}

function removeChild(id) {
    document.getElementById(id)?.remove();
}
</script>
@endpush

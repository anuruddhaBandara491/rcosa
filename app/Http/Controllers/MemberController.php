<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MemberController extends Controller
{
    // ── Shared validation rules ──────────────────────────────
    private function rules(int $memberId = 0): array
    {
        return [
            // Optional
            'membership_number'         => ['nullable', 'integer', Rule::unique('members')->ignore($memberId)],
            'school_register_year'      => ['nullable', 'digits:4', 'integer', 'min:1900', 'max:' . now()->year],
            'email'                     => ['nullable', 'email', 'max:255'],
            'admission_number'          => ['nullable', 'string', 'max:100'],
            'date_joined_school'        => ['nullable', 'date'],

            // Mandatory
            'name_with_initials'        => ['required', 'string', 'max:255'],
            'married'                   => ['required', 'boolean'],
            'phone_number'              => ['required', 'string', 'max:20'],
            'nic_number'                => ['required', 'string', 'max:20', Rule::unique('members')->ignore($memberId)],
            'birthday'                  => ['required', 'date', 'before:today'],
            'address'                   => ['required', 'string', 'max:500'],
            'occupation'                => ['required', 'string', 'max:255'],
            'current_city'              => ['required', 'string', 'max:255'],
            'gender'                    => ['required', 'in:Male,Female'],
            'district'                  => ['required', 'string', 'max:255'],
            'election_division'         => ['required', 'string', 'max:255'],
            'grama_niladhari_division'  => ['required', 'string', 'max:255'],

            // Children (array of objects)
            'children'                  => ['nullable', 'array'],
            'children.*.name'           => ['nullable', 'string', 'max:255'],
            'children.*.school'         => ['nullable', 'string', 'max:255'],

            // Siblings (free text)
            'siblings_info'             => ['nullable', 'string', 'max:1000'],
        ];
    }

    private function messages(): array
    {
        return [
            'name_with_initials.required'       => 'Member name is required.',
            'married.required'                  => 'Marital status is required.',
            'phone_number.required'             => 'Phone number is required.',
            'nic_number.required'               => 'NIC number is required.',
            'nic_number.unique'                 => 'This NIC number is already registered.',
            'birthday.required'                 => 'Birthday is required.',
            'birthday.before'                   => 'Birthday must be a past date.',
            'address.required'                  => 'Address is required.',
            'occupation.required'               => 'Occupation is required.',
            'current_city.required'             => 'Current city is required.',
            'gender.required'                   => 'Gender is required.',
            'district.required'                 => 'District is required.',
            'election_division.required'        => 'Election division is required.',
            'grama_niladhari_division.required' => 'Grama Niladhari division is required.',
        ];
    }

    // ── Prepare children array from request ──────────────────
    private function buildChildrenInfo(Request $request): ?array
    {
        $children = $request->input('children', []);
        $result   = [];

        foreach ($children as $child) {
            if (!empty($child['name']) || !empty($child['school'])) {
                $result[] = [
                    'name'   => $child['name']   ?? '',
                    'school' => $child['school'] ?? '',
                ];
            }
        }

        return empty($result) ? null : $result;
    }

    // ── INDEX ─────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Member::query();

        if ($search = $request->input('search')) {
            $query->search($search);
        }

        if ($gender = $request->input('gender')) {
            $query->where('gender', $gender);
        }

        if ($married = $request->input('married')) {
            $query->where('married', $married === 'yes');
        }

        $members = $query->latest()->paginate(15)->withQueryString();

        return view('members.index', compact('members'));
    }

    // ── CREATE ────────────────────────────────────────────────
    public function create(Request $request)
    {
        $type = $request->input('type', 'new');
        return view('members.create', compact('type'));
    }

    // ── STORE ─────────────────────────────────────────────────
    public function store(Request $request)
    {
        $isExisting = $request->input('type') === 'existing';

        $rules = $this->rules(); // your existing rules array

        if ($isExisting) {
            // Membership number is mandatory and must be unique
            $rules['membership_number'] = ['required', 'integer', 'unique:members,membership_number'];
        } else {
            // Auto-generate — not submitted by user
            unset($rules['membership_number']);
        }
        $validated = $request->validate($rules, $this->messages());
        $validated['children_info'] = $this->buildChildrenInfo($request);
        unset($validated['children']);

        //membership_number auto-generation for new members
        if (!$isExisting) {
            $latestMember = Member::where('type', 'new')->latest('membership_number')->first();
            if ($latestMember && $latestMember->membership_number) {
                $validated['membership_number'] = $latestMember->membership_number + 1;
            } else {
                $validated['membership_number'] = 1002; // Starting point for new members-Given by the client
            }
        }
        // Cast married to bool
        $validated['married'] = (bool) $request->input('married');

        $member = Member::create($validated);

        return redirect()
            ->route('members.show', $member)
            ->with('success', 'Member "' . $member->name_with_initials . '" registered successfully.');
    }

    // ── SHOW ──────────────────────────────────────────────────
    public function show(Member $member)
    {
        return view('members.show', compact('member'));
    }

    // ── EDIT ──────────────────────────────────────────────────
    public function edit(Member $member)
    {
        return view('members.edit', compact('member'));
    }

    // ── UPDATE ────────────────────────────────────────────────
    public function update(Request $request, Member $member)
    {
        $validated = $request->validate($this->rules($member->id), $this->messages());

        $validated['children_info'] = $this->buildChildrenInfo($request);
        unset($validated['children']);

        $validated['married'] = (bool) $request->input('married');

        $member->update($validated);

        return redirect()
            ->route('members.show', $member)
            ->with('success', 'Member "' . $member->name_with_initials . '" updated successfully.');
    }

    // ── DESTROY ───────────────────────────────────────────────
    public function destroy(Member $member)
    {
        $name = $member->name_with_initials;
        $member->delete();

        return redirect()
            ->route('members.index')
            ->with('success', 'Member "' . $name . '" removed successfully.');
    }
}

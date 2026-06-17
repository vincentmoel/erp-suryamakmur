{{-- $allActions, $modules, $granted (optional, for edit) --}}
@php
    $allActions = ['read','create','update','delete','restore','receive','cancel'];
    $currentGroup = null;
@endphp

<div class="overflow-x-auto">
    <table class="perm-table w-full text-sm" id="permission-table" data-no-sort>
        <thead>
            <tr class="perm-thead-row">
                <th class="perm-th perm-th-module">Module</th>
                @foreach ($allActions as $action)
                    <th class="perm-th perm-th-action">
                        <div class="flex flex-col items-center gap-1.5">
                            <span class="text-xs">{{ \App\Enums\Module::actionLabel($action) }}</span>
                            <button type="button" class="perm-toggle-btn" data-col-toggle="{{ $action }}" title="Toggle all {{ \App\Enums\Module::actionLabel($action) }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                            </button>
                        </div>
                    </th>
                @endforeach
                <th class="perm-th perm-th-action">
                    <div class="flex flex-col items-center gap-1.5">
                        <span class="text-xs">All</span>
                        <button type="button" class="perm-toggle-btn" id="btn-global-toggle" title="Toggle all">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                        </button>
                    </div>
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($modules as $module)
                @php
                    $group     = $module->group();
                    $available = $module->permissions();
                @endphp

                {{-- Group header row --}}
                @if ($group !== $currentGroup)
                    @php $currentGroup = $group; @endphp
                    <tr class="perm-group-row">
                        <td colspan="{{ count($allActions) + 2 }}" class="perm-group-label">
                            {{ $group ?? 'General' }}
                        </td>
                    </tr>
                @endif

                <tr class="perm-row" data-module-row="{{ $module->value }}">
                    <td class="perm-td-module">
                        <div class="flex items-center gap-2.5">
                            <div class="perm-module-icon">
                                <x-icon :name="$module->icon()" class="size-3.5" />
                            </div>
                            <span class="font-medium text-sm">{{ $module->label() }}</span>
                        </div>
                    </td>

                    @foreach ($allActions as $action)
                        <td class="perm-td-action">
                            @if (in_array($action, $available))
                                @php
                                    $checked = isset($granted)
                                        ? (old("permissions.{$module->value}.{$action}") !== null
                                            ? old("permissions.{$module->value}.{$action}")
                                            : in_array($action, $granted[$module->value] ?? []))
                                        : (bool) old("permissions.{$module->value}.{$action}");
                                @endphp
                                <input type="checkbox"
                                       name="permissions[{{ $module->value }}][{{ $action }}]"
                                       value="1"
                                       data-module="{{ $module->value }}"
                                       data-action="{{ $action }}"
                                       {{ $checked ? 'checked' : '' }}
                                       class="perm-cb">
                            @else
                                <span class="perm-na">—</span>
                            @endif
                        </td>
                    @endforeach

                    <td class="perm-td-action">
                        <button type="button" class="perm-toggle-btn" data-row-toggle="{{ $module->value }}" title="Toggle {{ $module->label() }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@extends('layouts.main', ['title' => __('general.edit_product')])

@section('content')
    <div class="page-content">

        <div class="page-header">
            <h1>@lang('general.edit_product')</h1>
            <p>@lang('general.edit_product_desc')</p>
        </div>

        <form action="{{ route("$route.update", ['encryptedId' => $encryptedId]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="flex flex-col gap-4">

                {{-- Product Information --}}
                <div class="rounded-lg border bg-card text-card-foreground shadow-xs">
                    <div class="flex items-center gap-3 border-b px-6 py-4">
                        <h3 class="text-sm font-semibold">@lang('general.product_information')</h3>
                    </div>
                    <div class="flex flex-col gap-6 p-6">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <x-form.field name="name" :label="__('general.name')" :required="true">
                                <input id="name"
                                       type="text"
                                       name="name"
                                       value="{{ old('name', $data->name) }}"
                                       placeholder="{{ __('general.product_name_placeholder') }}"
                                       class="input {{ $errors->has('name') ? 'border-destructive' : '' }}">
                            </x-form.field>

                            <x-form.field name="sku" :label="__('general.sku')">
                                <input id="sku"
                                       type="text"
                                       name="sku"
                                       value="{{ old('sku', $data->sku) }}"
                                       placeholder="{{ __('general.sku_placeholder') }}"
                                       class="input {{ $errors->has('sku') ? 'border-destructive' : '' }}">
                            </x-form.field>
                        </div>

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <x-form.field name="category_id" :label="__('general.category')">
                                <x-form.single-select
                                    name="category_id"
                                    :placeholder="__('general.select_category_placeholder')"
                                    :selected="old('category_id', $data->category_id)"
                                    :options="$categories->map(fn($c) => ['value' => $c->id, 'label' => $c->name])->toArray()" />
                            </x-form.field>

                            <x-form.field name="unit_id" :label="__('general.unit')" :required="true">
                                <x-form.single-select
                                    name="unit_id"
                                    :placeholder="__('general.select_unit_placeholder')"
                                    :selected="old('unit_id', $data->unit_id)"
                                    :options="$units->map(fn($u) => ['value' => $u->id, 'label' => $u->name])->toArray()" />
                            </x-form.field>
                        </div>
                    </div>
                </div>

                {{-- Pricing & Inventory --}}
                <div class="rounded-lg border bg-card text-card-foreground shadow-xs">
                    <div class="flex items-center gap-3 border-b px-6 py-4">
                        <h3 class="text-sm font-semibold">@lang('general.pricing_and_inventory')</h3>
                    </div>
                    <div class="flex flex-col gap-6 p-6">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <x-form.field name="selling_price" :label="__('general.selling_price')" :required="true">
                                <div data-slot="input-group" role="group"
                                     class="group/input-group relative flex w-full items-center rounded-md border border-input shadow-xs h-9 min-w-0 has-[[data-slot=input-group-control]:focus-visible]:border-ring has-[[data-slot=input-group-control]:focus-visible]:ring-[3px] has-[[data-slot=input-group-control]:focus-visible]:ring-ring/50 {{ $errors->has('selling_price') ? 'border-destructive' : '' }}">
                                    <div role="group" data-slot="input-group-addon" data-align="inline-start"
                                         class="order-first pl-3 flex h-auto cursor-text items-center justify-center gap-2 py-1.5 text-sm font-medium text-muted-foreground select-none">
                                        Rp
                                    </div>
                                    <input data-slot="input-group-control"
                                           data-money-display="selling_price"
                                           type="text"
                                           inputmode="numeric"
                                           placeholder="0"
                                           class="flex-1 rounded-none border-0 bg-transparent shadow-none focus-visible:ring-0 dark:bg-transparent h-full px-2 text-sm outline-none">
                                    <input type="hidden" name="selling_price" id="selling_price"
                                           value="{{ old('selling_price', $data->selling_price) }}">
                                </div>
                            </x-form.field>

                            <x-form.field name="stock_minimum" :label="__('general.stock_minimum')" :required="true">
                                <input id="stock_minimum"
                                       type="number"
                                       name="stock_minimum"
                                       value="{{ old('stock_minimum', $data->stock_minimum) }}"
                                       min="0"
                                       placeholder="{{ __('general.stock_minimum_placeholder') }}"
                                       class="input {{ $errors->has('stock_minimum') ? 'border-destructive' : '' }}">
                            </x-form.field>
                        </div>
                    </div>
                </div>

                {{-- Media & Notes --}}
                <div class="rounded-lg border bg-card text-card-foreground shadow-xs">
                    <div class="flex items-center gap-3 border-b px-6 py-4">
                        <h3 class="text-sm font-semibold">@lang('general.media_and_notes')</h3>
                    </div>
                    <div class="flex flex-col gap-6 p-6">
                        <x-form.field name="description" :label="__('general.description')">
                            <textarea id="description"
                                      name="description"
                                      rows="4"
                                      placeholder="{{ __('general.description_placeholder') }}"
                                      style="height: auto; padding-top: 0.5rem; padding-bottom: 0.5rem;"
                                      class="input {{ $errors->has('description') ? 'border-destructive' : '' }}">{{ old('description', $data->description) }}</textarea>
                        </x-form.field>

                        <x-form.field name="image" :label="__('general.product_image')">
                            <x-form.file-upload
                                name="image"
                                :max-size-mb="2"
                                :preview="$data->image ? asset('storage/' . $data->image) : null" />
                        </x-form.field>

                        {!! \App\Helpers\HtmlBuilder::toggle((bool) old('is_active', $data->is_active ? '1' : '0'), inputId: 'is_active_hidden') !!}
                    </div>
                </div>

                <div class="rounded-lg border bg-card text-card-foreground shadow-xs">
                    @include('partials.form-actions-edit')
                </div>

            </div>
        </form>

    </div>
@endsection

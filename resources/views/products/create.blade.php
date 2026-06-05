@extends('layouts.main', ['title' => "Add $title"])

@section('content')
    <div class="page-content">

        <div class="page-header">
            <h1>Add Product</h1>
            <p>Create a new product.</p>
        </div>

        <form action="{{ route("$route.store") }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="rounded-lg border bg-card text-card-foreground shadow-xs">

                <div class="flex flex-col gap-6 p-6">

                    {{-- Name & SKU --}}
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <x-form.field name="name" label="Name" :required="true">
                            <input id="name"
                                   type="text"
                                   name="name"
                                   value="{{ old('name') }}"
                                   placeholder="Product name"
                                   class="input {{ $errors->has('name') ? 'border-destructive' : '' }}">
                        </x-form.field>

                        <x-form.field name="sku" label="SKU">
                            <input id="sku"
                                   type="text"
                                   name="sku"
                                   value="{{ old('sku') }}"
                                   placeholder="Stock keeping unit"
                                   class="input {{ $errors->has('sku') ? 'border-destructive' : '' }}">
                        </x-form.field>
                    </div>

                    {{-- Category & Unit --}}
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <x-form.field name="category_id" label="Category">
                            <x-form.single-select
                                name="category_id"
                                placeholder="Select category..."
                                :options="$categories->map(fn($c) => ['value' => $c->id, 'label' => $c->name])->toArray()" />
                        </x-form.field>

                        <x-form.field name="unit_id" label="Unit" :required="true">
                            <x-form.single-select
                                name="unit_id"
                                placeholder="Select unit..."
                                :options="$units->map(fn($u) => ['value' => $u->id, 'label' => $u->name])->toArray()" />
                        </x-form.field>
                    </div>

                    {{-- Selling Price & Stock Minimum --}}
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <x-form.field name="selling_price" label="Selling Price" :required="true">
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
                                <input type="hidden" name="selling_price" id="selling_price" value="{{ old('selling_price', 0) }}">
                            </div>
                        </x-form.field>

                        <x-form.field name="stock_minimum" label="Stock Minimum" :required="true">
                            <input id="stock_minimum"
                                   type="number"
                                   name="stock_minimum"
                                   value="{{ old('stock_minimum', 0) }}"
                                   min="0"
                                   class="input {{ $errors->has('stock_minimum') ? 'border-destructive' : '' }}">
                        </x-form.field>
                    </div>

                    {{-- Description --}}
                    <x-form.field name="description" label="Description">
                        <textarea id="description"
                                  name="description"
                                  rows="4"
                                  placeholder="Product description"
                                  style="height: auto; padding-top: 0.5rem; padding-bottom: 0.5rem;"
                                  class="input {{ $errors->has('description') ? 'border-destructive' : '' }}">{{ old('description') }}</textarea>
                    </x-form.field>

                    {{-- Image --}}
                    <x-form.field name="image" label="Image">
                        <x-form.file-upload name="image" :max-size-mb="2" />
                    </x-form.field>

                    {{-- Status --}}
                    {!! \App\Helpers\HtmlBuilder::toggle(old('is_active', '1') === '1', inputId: 'is_active_hidden') !!}

                </div>

                @include('partials.form-actions-create')

            </div>
        </form>

    </div>
@endsection

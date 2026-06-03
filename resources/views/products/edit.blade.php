@extends('layouts.main', ['title' => "Edit $title"])

@section('content')
    <div class="page-content">

        <div class="page-header">
            <h1>Edit Product</h1>
            <p>Update product information.</p>
        </div>

        <form action="{{ route("$route.update", ['encryptedId' => $encryptedId]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="rounded-lg border bg-card text-card-foreground shadow-xs">

                <div class="flex flex-col gap-6 p-6">

                    {{-- Name & SKU --}}
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <x-form.field name="name" label="Name" :required="true">
                            <input id="name"
                                   type="text"
                                   name="name"
                                   value="{{ old('name', $data->name) }}"
                                   placeholder="Product name"
                                   class="input {{ $errors->has('name') ? 'border-destructive' : '' }}">
                        </x-form.field>

                        <x-form.field name="sku" label="SKU">
                            <input id="sku"
                                   type="text"
                                   name="sku"
                                   value="{{ old('sku', $data->sku) }}"
                                   placeholder="Stock keeping unit"
                                   class="input {{ $errors->has('sku') ? 'border-destructive' : '' }}">
                        </x-form.field>
                    </div>

                    {{-- Category --}}
                    <x-form.field name="category_id" label="Category">
                        <x-form.single-select
                            name="category_id"
                            placeholder="Select category..."
                            :selected="old('category_id', $data->category_id)"
                            :options="$categories->map(fn($c) => ['value' => $c->id, 'label' => $c->name])->toArray()" />
                    </x-form.field>

                    {{-- Unit & Stock Minimum --}}
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <x-form.field name="unit_id" label="Unit" :required="true">
                            <x-form.single-select
                                name="unit_id"
                                placeholder="Select unit..."
                                :selected="old('unit_id', $data->unit_id)"
                                :options="$units->map(fn($u) => ['value' => $u->id, 'label' => $u->name])->toArray()" />
                        </x-form.field>

                        <x-form.field name="stock_minimum" label="Stock Minimum" :required="true">
                            <input id="stock_minimum"
                                   type="number"
                                   name="stock_minimum"
                                   value="{{ old('stock_minimum', $data->stock_minimum) }}"
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
                                  class="input {{ $errors->has('description') ? 'border-destructive' : '' }}">{{ old('description', $data->description) }}</textarea>
                    </x-form.field>

                    {{-- Image --}}
                    <x-form.field name="image" label="Image">
                        <x-form.file-upload
                            name="image"
                            :max-size-mb="2"
                            :preview="$data->image ? asset('storage/' . $data->image) : null" />
                    </x-form.field>

                </div>

                @include('partials.form-actions-edit')

            </div>
        </form>

    </div>
@endsection

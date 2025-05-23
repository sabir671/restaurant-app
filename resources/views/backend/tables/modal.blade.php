@php
    $isEdit = isset($table) ? true : false;
    $url = $isEdit ? route('tables.update', $table->id) : route('tables.store');

@endphp
<form action="{{$url}}" method="post" data-form="ajax-form" data-modal="#ajax_model" data-datatable="#tables_datatable">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif
    <div class="row">
        <div class="form-group col-lg-6">
            <label for="name">Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="name" id="name" value="{{$isEdit ? $table->name : ''}}">
        </div>
        <div class="form-group col-lg-6">
            <label for="guest_number">Guest Number <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="guest_number" id="guest_number" value="{{$isEdit ? $table->guest_number : ''}}">
        </div>


        <div class="form-group col-lg-6">
            <label for="location">Location</label>
            <input type="text" class="form-control" name="location" id="location" value="{{$isEdit ? $table->location : ''}}">
        </div>
        <div class="form-group col-lg-6">
            <label for="status">Status</label>
            <select class="form-control select2 form-select form-select-modal" name="status" id="status">
                <option value="pending" @if ($isEdit && $table->status == 'pending') selected @endif>Pending</option>
                <option value="available" @if ($isEdit && $table->status == 'available') selected @endif>Available</option>
            </select>
        </div>
    </div>
    <div class="col-lg-12 px-0">
        <button type="submit" class="btn btn-primary" data-button="submit">Submit</button>
    </div>
</form>

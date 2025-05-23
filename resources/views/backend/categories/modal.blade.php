@php
$isEdit = isset($categories) ? true : false;
$url = $isEdit ? route('categories.update', $categories->id) : route('categories.store');

@endphp
<form action="{{$url}}" method="post" data-form="ajax-form" data-modal="#ajax_model" data-datatable="#categories_datatable">
    @csrf
    @if ($isEdit)
    @method('PUT')
    @endif
    <div class="row">
        <div class="form-group col-lg-6">
            <label for="name">Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="name" id="name" value="{{$isEdit ? $categories->name : ''}}">
        </div>
        <div class="form-group col-lg-6">
            <label for="description">Description <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="description" id="description" value="{{$isEdit ? $categories->description : ''}}">
        </div>

        <div class="form-group col-lg-6">
            <label for="image">Image <span class="text-danger">*</span></label>
            <input type="file" class="form-control" name="image" id="image" value="{{$isEdit ? $categories->image : ''}}">
        </div>
        <div class="form-group col-lg-6">
            <label for="status">Status</label>
            <select class="form-control select2 form-select form-select-modal" name="status" id="status">
                <option value="active" @if ($isEdit && $categories->status == 'active') selected @endif>Active</option>
                <option value="inactive" @if ($isEdit && $categories->status == 'inactive') selected @endif>Inactive</option>
            </select>
        </div>
    </div>
    <div class="col-lg-12 px-0">
        <button type="submit" class="btn btn-primary" data-button="submit">Submit</button>
    </div>
</form>

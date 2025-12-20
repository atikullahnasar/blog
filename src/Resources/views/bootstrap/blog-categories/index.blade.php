<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Categories</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">

<div class="container">
    <div class="d-flex justify-content-between">
        <h2>All Categories</h2>
        <button type="button" class="btn btn-primary mb-3" id="showAddEditBlogCategories">+ Add New Category</button>
    </div>
    <table id="blog-category-table" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Status</th>
                <th class="text-center">Action</th>
            </tr>
        </thead>
    </table>
</div>

<!-- Add/Edit Category Modal -->
<div class="modal fade" id="addEditBlogCategories" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="modalTitle">Create New Category</h5>
            <button type="button" class="btn-close" id="closeAddEditBlogCategories"></button>
        </div>
        <form id="categoryForm">
            @csrf
            <input type="hidden" id="id">
            <input type="hidden" id="_method" value="POST">
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6 ">
                        <label for="name" class="form-label">Category Name *</label>
                        <input type="text" class="form-control" id="name" name="name">
                    </div>
                    <div class="col-md-6 ">
                        <label for="meta_title" class="form-label">Meta Title</label>
                        <input type="text" class="form-control" id="meta_title" name="meta_title">
                    </div>
                    <div class="">
                        <label for="description" class="form-label">Category Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="">
                        <label for="meta_description" class="form-label">Meta Description</label>
                        <textarea class="form-control" id="meta_description" name="meta_description" rows="2"></textarea>
                    </div>

                    <div class="col-md-6 ">
                        <label for="order" class="form-label">Display Order</label>
                        <input type="number" class="form-control" id="order" name="order" value="0" min="0">
                    </div>
                    <div class="col-md-6 d-flex justify-content-start align-items-end">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="status" name="status" value="1" checked>
                            <label class="form-check-label" for="status">Active</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save Category</button>
                <button type="button" class="btn btn-secondary" id="cancelModal">Cancel</button>
            </div>
        </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="confirmationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content text-center p-3">
            <p>Are you sure you want to delete this category?</p>
            <button type="button" class="btn btn-danger m-2" id="confirmDeleteBtn">Yes, delete</button>
            <button type="button" class="btn btn-secondary m-2" data-bs-dismiss="modal">Cancel</button>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    const addEditModal = new bootstrap.Modal(document.getElementById('addEditBlogCategories'));
    const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
    let deleteId = null;
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    const table = $('#blog-category-table').DataTable({
        ajax: '/beft/blog-categories', // replace with your API endpoint
        columns: [
            { data: 'name' },
            { data: 'status', render: data => data ? 'Active' : 'Inactive' },
            { data: 'id', render: data => `
                <button class="btn btn-sm btn-success edit-btn" data-id="${data}">Edit</button>
                <button class="btn btn-sm btn-danger delete-btn" data-id="${data}">Delete</button>
            ` }
        ]
    });

    $('#showAddEditBlogCategories').click(function() {
        $('#categoryForm')[0].reset();
        $('#_method').val('POST');
        $('#id').val('');
        $('#modalTitle').text("Create New Category");
        addEditModal.show();
    });

    $('#closeAddEditBlogCategories, #cancelModal').click(() => addEditModal.hide());

    $('#blog-category-table').on('click', '.edit-btn', function() {
        const id = $(this).data('id');
        $.get(`/beft/blog-categories/${id}/edit`, function(data) {
            // alert(id);
            // console.log(data);
            $('#name').val(data.name);
            $('#description').val(data.description);
            $('#meta_title').val(data.meta_title);
            $('#meta_description').val(data.meta_description);
            $('#order').val(data.order);
            $('#status').prop('checked', data.status);
            $('#id').val(data.id);
            $('#_method').val('PUT');
            $('#modalTitle').text("Edit Category");
            addEditModal.show();
        });
    });

    $('#blog-category-table').on('click', '.delete-btn', function() {
        deleteId = $(this).data('id');
        confirmationModal.show();
    });

    $('#confirmDeleteBtn').click(function() {
        if (!deleteId) return;

        $.ajax({
            url: `/beft/blog-categories/${deleteId}`,
            method: 'DELETE', // DELETE method
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: () => {
                table.ajax.reload();
                confirmationModal.hide();
                deleteId = null;
            },
            error: (xhr) => alert('Failed to delete: ' + (xhr.responseJSON?.message || xhr.statusText))
        });
    });



    $('#categoryForm').submit(function(e) {
        e.preventDefault();
        const id = $('#id').val();
        const method = $('#_method').val();
        const url = id ? `/beft/blog-categories/${id}` : '/beft/blog-categories';

        $.ajax({
            url: url,
            method: method,
            data: $(this).serialize(),
            success: () => {
                table.ajax.reload();
                addEditModal.hide();
            }
        });
    });
});
</script>

</body>
</html>

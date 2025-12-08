<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Blog Manager — All Blogs</title>
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css" rel="stylesheet">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

    <style>/* Make modal-body scrollable */
        .modal-body {
            max-height: 70vh;   /* maximum 70% of viewport height */
            overflow-y: auto;
        }

        /* Optional: make Quill editor fill available height */

        #editor { height: 250px; }
        .img-thumb { width:56px; height:56px; object-fit:cover; border-radius:6px; }
        .dt-center { text-align:center; }
    </style>
</head>
<body class="bg-light">

<div class="container py-4">
    <div class="d-flex align-items-center mb-3">
        <h3 class="me-auto">Blogs</h3>
        <button id="addNewModal" class="btn btn-primary">+ Add New Blog</button>
    </div>

    <div class="card">
        <div class="card-body">
        <table id="blogs-table" class="table table-striped table-bordered w-100">
            <thead>
            <tr>
                <th>Image</th>
                <th>Title</th>
                <th>Category</th>
                <th>Publish Date</th>
                <th>Status</th>
                <th class="dt-center">Action</th>
            </tr>
            </thead>
        </table>
        </div>
    </div>
</div>

<!-- Add / Edit Modal -->
<div class="modal fade" id="addEditBlogModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
        <div class="modal-header">
            <h5 id="modal-title" class="modal-title">Add New Blog</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="addEditClose"></button>
        </div>

        <form id="blog-form" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="blog-id" name="id" value="">
            <input type="hidden" id="published_at" name="published_at" value="">
            <input type="hidden" id="content_hidden" name="content">

            <div class="modal-body ">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label for="title" class="form-label">Blog Title *</label>
                        <input id="title" name="title" class="form-control" required>
                    </div>

                    <div class="col-md-4 d-flex align-items-end">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="show_home" name="show_home" value="1" checked>
                        <label class="form-check-label" for="show_home">Show on Home</label>
                    </div>
                    </div>
                    <div class="col-md-6">
                        <label for="category_id" class="form-label">Category *</label>
                        <select id="category_id" name="category_id" class="form-select" required>
                            <option value="">Loading categories...</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                    <label for="status" class="form-label">Status</label>
                    <select id="status" name="status" class="form-select">
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                    </select>
                    </div>


                    <div class="col-12">
                    <label class="form-label">Content *</label>
                    <div id="editor"></div>
                    </div>

                    <div class="col-md-6 mb-5">
                    <label for="featured_image" class="form-label">Featured Image</label>
                    <input id="featured_image" name="featured_image" type="file" class="form-control">
                    </div>

                    <div class="col-md-6">
                    <div class="mt-2"><img id="imagePreview" src="" alt="preview" class="img-thumb d-none"></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnCancel">Cancel</button>
                </div>
            </div>
        </form>

        </div>
    </div>
</div>

<!-- Confirm Delete Modal -->
<div class="modal fade" id="confirmationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content text-center p-3">
        <div class="modal-body">
            <p class="mb-3">Are you sure you want to delete this blog?</p>
            <button id="confirmDeleteBtn" class="btn btn-danger">Yes, delete</button>
            <button class="btn btn-secondary ms-2" data-bs-dismiss="modal">Cancel</button>
        </div>
        </div>
    </div>
</div>

<!-- JS libs -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

<script>
(function($){
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    const addEditModal = new bootstrap.Modal(document.getElementById('addEditBlogModal'));
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmationModal'));

    // Quill editor
    const quill = new Quill('#editor', { theme: 'snow' });
    quill.on('text-change', () => $('#content_hidden').val(quill.root.innerHTML));

    function resetForm(){
        $('#blog-form')[0].reset();
        $('#blog-id,#published_at,#content_hidden').val('');
        quill.root.innerHTML = '';
        $('#imagePreview').attr('src','').addClass('d-none');
        $('#status').val('draft');
        $('#category_id').val('');
    }

    function loadCategories(){
        return $.ajax({
            url:'/beft/blog-categories',
            method:'GET'
        }).done(resp => {
            const list = resp.data ?? resp;
            const select = $('#category_id').empty().append('<option value="">Choose a category</option>');
            list.forEach(c => select.append(`<option value="${c.id}">${c.name}</option>`));
        }).fail(()=>$('#category_id').html('<option value="">Failed to load categories</option>'));
    }

    const table = $('#blogs-table').DataTable({
        processing:true, serverSide:true,
        ajax:{ url:'/beft/blogs', type:'GET' },
        columns:[
            { data:'thumbnail_image', orderable:false, searchable:false, render:data => `<img src="${data??'https://via.placeholder.com/56'}" class="img-thumb">` },
            { data:'title' },
            { data:'category.name', defaultContent:'' },
            { data:'published_at', defaultContent:'' },
            { data:'status', render:d=>d==='published'?'<span class="badge bg-success">Published</span>':'<span class="badge bg-secondary">Draft</span>' },
            { data:'id', orderable:false, searchable:false, className:'dt-center',
              render:id=>`
                <button class="btn btn-sm btn-success me-1 edit-btn" data-id="${id}">Edit</button>
                <button class="btn btn-sm btn-warning me-1 toggle-btn" data-id="${id}">Toggle</button>
                <button class="btn btn-sm btn-danger delete-btn" data-id="${id}">Delete</button>
              `
            }
        ]
    });

    $('#addNewModal').on('click', ()=>{ resetForm(); $('#modal-title').text('Create New Blog'); loadCategories().always(()=>addEditModal.show()); });

    $('#featured_image').on('change',function(){
        const f = this.files[0];
        if(!f) return $('#imagePreview').attr('src','').addClass('d-none');
        $('#imagePreview').attr('src',URL.createObjectURL(f)).removeClass('d-none');
    });

    $('#blogs-table').on('click','.edit-btn',function(){
        const id=$(this).data('id'); resetForm();
        $.get(`/beft/blogs/${id}/edit`,data=>{
            $('#blog-id').val(data.id); $('#title').val(data.title); $('#category_id').val(data.category?.id??'');
            $('#status').val(data.status??'draft'); $('#show_home').prop('checked',data.show_home==1);
            $('#published_at').val(data.published_at??''); quill.root.innerHTML=data.content??'';
            $('#content_hidden').val(data.content??''); if(data.thumbnail_image) $('#imagePreview').attr('src',data.thumbnail_image).removeClass('d-none');
            $('#modal-title').text('Edit Blog'); addEditModal.show();
        }).fail(()=>alert('Failed to load blog'));
    });

    $('#blogs-table').on('click','.toggle-btn',function(){
        const id=$(this).data('id');
        $.post(`/beft/blogs/${id}/toggle-status`,{}).done(()=>table.ajax.reload(null,false)).fail(()=>alert('Failed to toggle'));
    });

    let pendingDeleteId=null;
    $('#blogs-table').on('click','.delete-btn',function(){
        pendingDeleteId=$(this).data('id');
        confirmModal.show();
    });

    $('#confirmDeleteBtn').on('click',function(){
        if(!pendingDeleteId) return;
        $.ajax({url:`/beft/blogs/${pendingDeleteId}`, method:'DELETE'})
        .done(()=>{table.ajax.reload(); confirmModal.hide(); pendingDeleteId=null;})
        .fail(()=>alert('Failed to delete'));
    });

    $('#blog-form').on('submit',function(e){
        e.preventDefault(); $('#content_hidden').val(quill.root.innerHTML);
        if($('#status').val()==='published' && !$('#published_at').val()) $('#published_at').val(new Date().toISOString().slice(0,19).replace('T',' '));

        const id=$('#blog-id').val();
        const url=id?`/beft/blogs/${id}`:'/beft/blogs';
        const fd=new FormData(this); if(id) fd.append('_method','PUT');

        $.ajax({url:url, method:'POST', data:fd, processData:false, contentType:false})
        .done(()=>{ addEditModal.hide(); table.ajax.reload(); resetForm(); })
        .fail(xhr=>{
            if(xhr.responseJSON?.errors){ alert(Object.values(xhr.responseJSON.errors)[0][0]); }
            else alert('Failed to save. Check console.');
        });
    });

    loadCategories();
})(jQuery);
</script>
</body>
</html>

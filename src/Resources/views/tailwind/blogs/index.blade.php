<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Blog Manager — All Blogs</title>
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css" rel="stylesheet">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

    <style>
        /* Custom overrides */
        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #e2e8f0;
            border-radius: 0.375rem;
            padding: 0.25rem 0.5rem;
        }
        #editor { height: 250px; }
        .img-thumb { width:56px; height:56px; object-fit:cover; border-radius:0.375rem; }
    </style>
</head>
<body class="bg-gray-50 p-6">

<div class="container mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-2xl font-bold text-gray-800">Blogs</h3>
        <button id="addNewModal" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">+ Add New Blog</button>
    </div>

    <div class="bg-white p-6 rounded-lg shadow">
        <table id="blogs-table" class="w-full text-left border-collapse">
            <thead>
            <tr class="bg-gray-100 border-b">
                <th class="p-3 font-semibold text-gray-700">Image</th>
                <th class="p-3 font-semibold text-gray-700">Title</th>
                <th class="p-3 font-semibold text-gray-700">Category</th>
                <th class="p-3 font-semibold text-gray-700">Publish Date</th>
                <th class="p-3 font-semibold text-gray-700">Status</th>
                <th class="p-3 font-semibold text-center text-gray-700">Action</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Add / Edit Modal -->
<div id="addEditBlogModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true" id="modalBackdrop"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 max-h-[85vh] overflow-y-auto">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                        <div class="flex justify-between items-center mb-4">
                             <h5 id="modal-title" class="text-xl font-bold text-gray-900">Add New Blog</h5>
                             <button type="button" class="text-gray-400 hover:text-gray-500" id="addEditClose">
                                 <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                 </svg>
                             </button>
                        </div>

                        <form id="blog-form" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" id="blog-id" name="id" value="">
                            <input type="hidden" id="published_at" name="published_at" value="">
                            <input type="hidden" id="content_hidden" name="content">

                            <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                                <div class="md:col-span-8">
                                    <label for="title" class="block text-sm font-medium text-gray-700">Blog Title *</label>
                                    <input id="title" name="title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 border p-2" required>
                                </div>

                                <div class="md:col-span-4 flex items-end">
                                    <div class="flex items-center">
                                        <input class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" type="checkbox" id="show_home" name="show_home" value="1" checked>
                                        <label class="ml-2 block text-sm text-gray-900" for="show_home">Show on Home</label>
                                    </div>
                                </div>

                                <div class="md:col-span-6">
                                    <label for="category_id" class="block text-sm font-medium text-gray-700">Category *</label>
                                    <select id="category_id" name="category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 border p-2 bg-white" required>
                                        <option value="">Loading categories...</option>
                                    </select>
                                </div>

                                <div class="md:col-span-6">
                                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                    <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 border p-2 bg-white">
                                        <option value="draft">Draft</option>
                                        <option value="published">Published</option>
                                    </select>
                                </div>

                                <div class="md:col-span-12">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Content *</label>
                                    <div id="editor" class="bg-white"></div>
                                </div>

                                <div class="md:col-span-6">
                                    <label for="featured_image" class="block text-sm font-medium text-gray-700">Featured Image</label>
                                    <input id="featured_image" name="featured_image" type="file" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                </div>

                                <div class="md:col-span-6">
                                    <div class="mt-2"><img id="imagePreview" src="" alt="preview" class="img-thumb hidden"></div>
                                </div>
                            </div>
                        
                            <div class="mt-6 flex justify-end space-x-3">
                                <button type="button" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50" id="btnCancel">Cancel</button>
                                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Confirm Delete Modal -->
<div id="confirmationModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true" id="confirmBackdrop"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-sm sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                     <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                     <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Delete Blog</h3>
                        <div class="mt-2">
                             <p class="mb-3 text-sm text-gray-500">Are you sure you want to delete this blog?</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button id="confirmDeleteBtn" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">Yes, delete</button>
                <button class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" id="cancelConfirmBtn">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- JS libs -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

<script>
(function($){
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    // Vanilla JS Modal Logic
    const toggleModal = (modalId, show = true) => {
        const modal = document.getElementById(modalId);
        if(!modal) return;
        if (show) {
            modal.classList.remove('hidden');
        } else {
            modal.classList.add('hidden');
        }
    };

    // Quill editor
    const quill = new Quill('#editor', { theme: 'snow' });
    quill.on('text-change', () => $('#content_hidden').val(quill.root.innerHTML));

    function resetForm(){
        $('#blog-form')[0].reset();
        $('#blog-id,#published_at,#content_hidden').val('');
        quill.root.innerHTML = '';
        $('#imagePreview').attr('src','').addClass('hidden').removeClass('block');
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
        createdRow: function(row, data, dataIndex) {
            $(row).addClass('border-b hover:bg-gray-50');
            $('td', row).addClass('p-3 text-gray-700 align-middle');
        },
        columns:[
            { data:'thumbnail_image', orderable:false, searchable:false, render:data => `<img src="${data??'https://via.placeholder.com/56'}" class="img-thumb">` },
            { data:'title' },
            { data:'category.name', defaultContent:'' },
            { data:'published_at', defaultContent:'' },
            { data:'status', render:d=>d==='published'?'<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Published</span>':'<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Draft</span>' },
            { data:'id', orderable:false, searchable:false, className:'text-center',
              render:id=>`
                <button class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-green-600 hover:bg-green-700 mr-1 edit-btn" data-id="${id}">Edit</button>
                <button class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-yellow-500 hover:bg-yellow-600 mr-1 toggle-btn" data-id="${id}">Toggle</button>
                <button class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-red-600 hover:bg-red-700 delete-btn" data-id="${id}">Delete</button>
              `
            }
        ]
    });

    $('#addNewModal').on('click', ()=>{ resetForm(); $('#modal-title').text('Create New Blog'); loadCategories().always(()=>toggleModal('addEditBlogModal', true)); });
    
    // Close modal handlers
    $('#addEditClose, #btnCancel, #modalBackdrop').on('click', ()=>toggleModal('addEditBlogModal', false));

    $('#featured_image').on('change',function(){
        const f = this.files[0];
        if(!f) return $('#imagePreview').attr('src','').addClass('hidden').removeClass('block');
        $('#imagePreview').attr('src',URL.createObjectURL(f)).removeClass('hidden').addClass('block');
    });

    $('#blogs-table').on('click','.edit-btn',function(){
        const id=$(this).data('id'); resetForm();
        $.get(`/beft/blogs/${id}/edit`,data=>{
            $('#blog-id').val(data.id); $('#title').val(data.title); $('#category_id').val(data.category?.id??'');
            $('#status').val(data.status??'draft'); $('#show_home').prop('checked',data.show_home==1);
            $('#published_at').val(data.published_at??''); quill.root.innerHTML=data.content??'';
            $('#content_hidden').val(data.content??''); 
            if(data.thumbnail_image) $('#imagePreview').attr('src',data.thumbnail_image).removeClass('hidden').addClass('block');
            $('#modal-title').text('Edit Blog'); toggleModal('addEditBlogModal', true);
        }).fail(()=>alert('Failed to load blog'));
    });

    $('#blogs-table').on('click','.toggle-btn',function(){
        const id=$(this).data('id');
        $.post(`/beft/blogs/${id}/toggle-status`,{}).done(()=>table.ajax.reload(null,false)).fail(()=>alert('Failed to toggle'));
    });

    let pendingDeleteId=null;
    $('#blogs-table').on('click','.delete-btn',function(){
        pendingDeleteId=$(this).data('id');
        toggleModal('confirmationModal', true);
    });
    
    $('#cancelConfirmBtn, #confirmBackdrop').on('click', ()=>toggleModal('confirmationModal', false));

    $('#confirmDeleteBtn').on('click',function(){
        if(!pendingDeleteId) return;
        $.ajax({url:`/beft/blogs/${pendingDeleteId}`, method:'DELETE'})
        .done(()=>{table.ajax.reload(); toggleModal('confirmationModal', false); pendingDeleteId=null;})
        .fail(()=>alert('Failed to delete'));
    });

    $('#blog-form').on('submit',function(e){
        e.preventDefault(); $('#content_hidden').val(quill.root.innerHTML);
        if($('#status').val()==='published' && !$('#published_at').val()) $('#published_at').val(new Date().toISOString().slice(0,19).replace('T',' '));

        const id=$('#blog-id').val();
        const url=id?`/beft/blogs/${id}`:'/beft/blogs';
        const fd=new FormData(this); if(id) fd.append('_method','PUT');

        $.ajax({url:url, method:'POST', data:fd, processData:false, contentType:false})
        .done(()=>{ toggleModal('addEditBlogModal', false); table.ajax.reload(); resetForm(); })
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

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>All Categories</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css" />
    <style>
        /* Custom overrides for DataTables to match Tailwind */
        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #e2e8f0;
            border-radius: 0.375rem;
            padding: 0.25rem 0.5rem;
        }
    </style>
</head>

<body class="bg-gray-50 p-6">

    <div class="container mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">All Categories</h2>
            <button type="button" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition"
                id="showAddEditBlogCategories">+ Add New Category</button>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <table id="blog-category-table" class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 border-b">
                        <th class="p-3 font-semibold text-center text-gray-700">Name</th>
                        <th class="p-3 font-semibold text-center text-gray-700">Status</th>
                        <th class="p-3 font-semibold text-center text-gray-700">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <!-- Add/Edit Category Modal -->
    <div id="addEditBlogCategories" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"
                id="modalBackdrop"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modalTitle">Create New Category
                            </h3>
                            <form id="categoryForm" class="mt-4">
                                @csrf
                                <input type="hidden" id="id">
                                <input type="hidden" id="_method" value="POST">
                                <div class="grid grid-cols-1 gap-4">
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-gray-700">Category Name
                                            *</label>
                                        <input type="text" id="name" name="name"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 border p-2">
                                    </div>
                                    <div>
                                        <label for="meta_title" class="block text-sm font-medium text-gray-700">Meta
                                            Title</label>
                                        <input type="text" id="meta_title" name="meta_title"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 border p-2">
                                    </div>
                                    <div>
                                        <label for="description"
                                            class="block text-sm font-medium text-gray-700">Category Description</label>
                                        <textarea id="description" name="description" rows="3"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 border p-2"></textarea>
                                    </div>
                                    <div>
                                        <label for="meta_description"
                                            class="block text-sm font-medium text-gray-700">Meta Description</label>
                                        <textarea id="meta_description" name="meta_description" rows="2"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 border p-2"></textarea>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label for="order" class="block text-sm font-medium text-gray-700">Display
                                                Order</label>
                                            <input type="number" id="order" name="order" value="0" min="0"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 border p-2">
                                        </div>
                                        <div class="flex items-end">
                                            <div class="flex items-center">
                                                <input id="status" name="status" type="checkbox" value="1" checked
                                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                                <label for="status"
                                                    class="ml-2 block text-sm text-gray-900">Active</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" form="categoryForm"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">Save
                        Category</button>
                    <button type="button" id="cancelModal"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="confirmationModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"
                id="confirmBackdrop"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Delete Category
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">Are you sure you want to delete this category? This
                                    action cannot be undone.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" id="confirmDeleteBtn"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">Yes,
                        delete</button>
                    <button type="button" id="cancelConfirmModal"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>

    <script>
        $(document).ready(function () {
            // Simple Modal Logic
            const toggleModal = (modalId, show = true) => {
                const modal = document.getElementById(modalId);
                if (show) {
                    modal.classList.remove('hidden');
                } else {
                    modal.classList.add('hidden');
                }
            };

            $('#showAddEditBlogCategories').click(() => {
                $('#categoryForm')[0].reset();
                $('#_method').val('POST');
                $('#id').val('');
                $('#modalTitle').text("Create New Category");
                toggleModal('addEditBlogCategories', true);
            });

            $('#closeAddEditBlogCategories, #cancelModal, #modalBackdrop').click(() => toggleModal('addEditBlogCategories', false));
            $('#cancelConfirmModal, #confirmBackdrop').click(() => toggleModal('confirmationModal', false));

            let deleteId = null;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            const table = $('#blog-category-table').DataTable({
                ajax: '/beft/blog-categories',
                createdRow: function (row, data, dataIndex) {
                    $(row).addClass('border-b hover:bg-gray-50');
                    $('td', row).addClass('p-3 text-gray-700');
                },
                columns: [
                    { data: 'name' },
                    { data: 'status', render: data => data ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>' : '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Inactive</span>' },
                    {
                        data: 'id', className: "text-center", render: data => `
                <button class="edit-btn inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none mr-1" data-id="${data}">Edit</button>
                <button class="delete-btn inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none" data-id="${data}">Delete</button>
            ` }
                ]
            });

            $('#blog-category-table').on('click', '.edit-btn', function () {
                const id = $(this).data('id');
                $.get(`/beft/blog-categories/${id}/edit`, function (data) {
                    $('#name').val(data.name);
                    $('#description').val(data.description);
                    $('#meta_title').val(data.meta_title);
                    $('#meta_description').val(data.meta_description);
                    $('#order').val(data.order);
                    $('#status').prop('checked', data.status);
                    $('#id').val(data.id);
                    $('#_method').val('PUT');
                    $('#modalTitle').text("Edit Category");
                    toggleModal('addEditBlogCategories', true);
                });
            });

            $('#blog-category-table').on('click', '.delete-btn', function () {
                deleteId = $(this).data('id');
                toggleModal('confirmationModal', true);
            });

            $('#confirmDeleteBtn').click(function () {
                if (!deleteId) return;

                $.ajax({
                    url: `/beft/blog-categories/${deleteId}`,
                    method: 'DELETE',
                    success: () => {
                        table.ajax.reload();
                        toggleModal('confirmationModal', false);
                        deleteId = null;
                    },
                    error: (xhr) => alert('Failed to delete: ' + (xhr.responseJSON?.message || xhr.statusText))
                });
            });

            $('#categoryForm').submit(function (e) {
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
                        toggleModal('addEditBlogCategories', false);
                    }
                });
            });
        });
    </script>

</body>

</html>
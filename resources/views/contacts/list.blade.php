<?php

use App\Enums\LifeCycleStagesEnum;

?>

<!doctype html>
<html>

<head>
    <title>Hubspot Integration</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <!-- Font Awesome Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" integrity="sha512-vKMx8UnXk60zUwyUnUPM3HbQo8QfmNx7+ltw8Pm5zLusl1XIfwcxo8DbWCqMGKaWeNxWA8yrx5v3SaVpMvR3CA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Jquery Include -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- Popper JS -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <!-- Datatable Assets-->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    </script>
</head>

<body>
    @if(session()->has('hubspot_access_token'))
    <div>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="/">Hubspot Integration</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
        </nav>
    </div>




    </div>
    <div class="container mt-3">

        <button type="button" class="btn btn-primary m-2" data-toggle="modal" data-target="#newContactModal">
            Add new Contact
        </button>

        @include('includes.contacts_edit_modal')
        @include('includes.contacts_add_modal')

        <button type="button" class="btn btn-dark m-2" id="sync-btn" data-toggle="tooltip" data-placement="top" title="Only needed, when failed to sync due webhook or api fails to communicate">
            <i class="fa fa-refresh"></i> &nbsp;Sync with Hubspot
        </button>
        @if(session()->has('hubspot_access_token'))
        <a href="{{ route('hubspot.logout') }}" class="btn btn-danger m-2">
            <i class="fa fa-sign-out"></i> Disconnect HubSpot
        </a>
        @endif

        <table width="100%" class="table table-hover contacts-datatable" style="width: 100%">
            <thead style="background-color: #f8f9fa;">
                <tr>
                    <th>ID</th>
                    <th>First name</th>
                    <th>Last name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Website</th>
                    <th>Lifecycle Stage</th>
                    <th>Job Title</th>
                    <th>Created at</th>
                    <th>Last modified at</th>
                    <th>Actions</th>
                    <th class="d-none">lifecyclestage_value</th>
                </tr>
            </thead>
        </table>

        <script type="text/javascript">
            // For Enabling tooltip 
            $(function() {
                $('[data-toggle="tooltip"]').tooltip()
            })

            // Loading Datatable
            $(function() {
                var table = $('.contacts-datatable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('contacts.list') }}",
                    columns: [{
                            data: 'id',
                            name: 'id'
                        },
                        {
                            data: 'firstname',
                            name: 'firstname'
                        },
                        {
                            data: 'lastname',
                            name: 'lastname'
                        },
                        {
                            data: 'email',
                            name: 'email'
                        },
                        {
                            data: 'phone',
                            name: 'phone'
                        },
                        {
                            data: 'website',
                            name: 'website'
                        },
                        {
                            data: 'lifecyclestage_label',
                            name: 'lifecyclestage_label'
                        },
                        {
                            data: 'jobtitle',
                            name: 'jobtitle'
                        },
                        {
                            data: 'created_at',
                            name: 'created_at'
                        },
                        {
                            data: 'updated_at',
                            name: 'updated_at'
                        },
                        {
                            data: 'actions',
                            name: 'actions'
                        },
                        {
                            data: 'lifecyclestage',
                            name: 'lifecyclestage_value'
                        },
                    ],
                    columnDefs: [{
                        targets: -1,
                        className: 'd-none'
                    }]
                });



                // Add ajax delete request when user clicked #contact-delete-btn
                $('body').on('click', '.contact-delete-btn', function(e) {
                    e.preventDefault();
                    var href = $(this).attr('action-href');
                    console.log(href);
                    $.ajax({
                        url: href,
                        type: "DELETE",
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(result) {
                            // Reload the DataTable
                            $('.contacts-datatable').DataTable().ajax.reload();
                        },
                    });
                });

            });


            // on click sync-btn 
            $('#sync-btn').click(function() {
                var btn = $(this);
                var btnText = btn.html();
                btn.html('Syncing ....');
                $.ajax({
                    url: "{{ route('contacts.sync') }}",
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(result) {
                        // Reload the DataTable
                        $('.contacts-datatable').DataTable().ajax.reload();
                        btn.html(btnText);
                    },
                });
            });


            // add-contact-submit-btn on click , call ajax post to data-action
            $('#add-contact-submit-btn').click(function() {
                var btn = $(this);
                var btnText = btn.html();
                btn.html('Saving ....');
                $.ajax({
                    url: "{{ route('contacts.store') }}",
                    type: "POST",
                    data: $('#contact-add-form').serialize(),
                    success: function(result) {
                        $('.contacts-datatable').DataTable().ajax.reload();
                        btn.html(btnText);

                        $('#newContactModal').modal('hide');

                        toastr.success(result.message);
                    },
                    error: function(xhr, status, error) {
                        // if xhr status is 422 then show error
                        if (xhr.status == 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#' + key + 'Help').removeClass('d-none');
                                $('#' + key + 'Help').text(value[0]);
                            });
                        }
                        btn.html(btnText);
                    }
                });
            });

            // On Modal Close, Reset it's form
            $('#newContactModal').on('hidden.bs.modal', function() {
                $('#contact-add-form')[0].reset();
            });

            // add-contact-submit-btn on click , call ajax post to data-action
            $('#edit-contact-submit-btn').click(function(e) {
                e.preventDefault();
                var btn = $(this);
                var btnText = btn.html();
                var contactId = $(this).data('contact-id');
                btn.html('Updating ....');
                $.ajax({
                    url: "{{ route('contacts.update', ['contact' => ':id']) }}".replace(':id', contactId),
                    type: "PUT",
                    data: $('#contact-edit-form').serialize() + "&_token=" + '{{ csrf_token() }}',
                    success: function(result) {
                        $('.contacts-datatable').DataTable().ajax.reload();
                        console.log(result);
                        btn.html(btnText);
                        // modal hide
                        $('#editContactModal').modal('hide');

                        // toastr show succes with response.message
                        toastr.success(result.message);

                        // table.draw();
                    },
                    error: function(xhr, status, error) {
                        // if xhr status is 422 then show error
                        if (xhr.status == 422) {
                            var errors = xhr.responseJSON.errors;
                            // show error message
                            $.each(errors, function(key, value) {
                                $('#' + key + 'HelpEdit').removeClass('d-none');
                                $('#' + key + 'HelpEdit').text(value[0]);
                            });
                        }
                        btn.html(btnText);
                    }
                });
            });


            // On Edit Button Click, populate data to Edit Form 
            $('body').on('click', '.contact-edit-btn', function(e) {
                var row = $(this).closest('tr');

                $('#firstnameEdit').val(row.find('td').eq(1).text());
                $('#lastnameEdit').val(row.find('td').eq(2).text());
                $('#emailEdit').val(row.find('td').eq(3).text());
                $('#phoneEdit').val(row.find('td').eq(4).text());
                $('#websiteEdit').val(row.find('td').eq(5).text());
                $('#lifecyclestageEdit').val(row.find('td').eq(11).text());
                $('#jobtitleEdit').val(row.find('td').eq(7).text());

                $('#edit-contact-submit-btn').data('contact-id', $(this).data('contact-id'));
            });
        </script>
        @else
        <div>
            <h3>Connect to HubSpot</h3>
            <a target="_blank" href="{{ route('hubspot.authorize') }}" class="btn btn-primary">
                Authorize HubSpot
            </a>
        </div>
        @endif
</body>

</html>
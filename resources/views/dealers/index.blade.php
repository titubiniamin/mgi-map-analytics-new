@extends('layouts.main')
@section('title', 'Items')
@section('content')
    <!-- push external head elements to head -->
    @push('head')
        <link rel="stylesheet" href="{{ asset('plugins/DataTables/datatables.min.css') }}">
        <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">
    @endpush


    <div class="container-fluid">
        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="fa fa-box bg-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('Items')}}</h5>
                            <span>{{ __('List of users')}}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <nav class="breadcrumb-container" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{route('dashboard')}}"><i class="ik ik-home"></i></a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="#">{{ __('Items')}}</a>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="row">
            <!-- start message area-->
            @include('include.message')
            <!-- end message area-->
            <div class="col-md-12">
                <div class="card p-3">
                    <div class="card-header"><h3>{{ __('Items')}}</h3></div>
                    <div class="card-body">
                        <table id="item_table" class="table">
                            <thead>
                            <tr>
                                <th>{{__('Serial')}}</th>
                                <th>{{__('Photo')}}</th>
                                <th>{{ __('Name')}}</th>
                                <th>{{ __('Description')}}</th>
                                <th>{{ __('Item Type')}}</th>
                                <th>{{ __('Brands')}}</th>
                                <th>{{ __('Item Stock')}}</th>
                                <th>{{ __('Action')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- push external js -->
    @push('script')
        <script src="{{ asset('plugins/DataTables/datatables.min.js') }}"></script>
        <script src="{{ asset('plugins/select2/dist/js/select2.min.js') }}"></script>
        <!--server side users table script-->
        <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
        <script src="{{ asset('js/item-list.js') }}"></script>
    @endpush
    <!---------------Modal Start---------------->
    <style>
        #stock-history {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 16px;
            text-align: left;
        }

        #stock-history thead tr {
            background-color: #f2f2f2;
            color: #333;
            text-transform: uppercase;
            font-weight: bold;
        }

        #stock-history th, #stock-history td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        #stock-history tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        #stock-history tbody tr:hover {
            background-color: #f1f1f1;
        }

        #stock-history th {
            padding-top: 12px;
            padding-bottom: 12px;
            background-color: #007bff;
            color: white;
        }

        #stock-history td {
            vertical-align: middle;
        }
    </style>

    <div class="modal fade edit-layout-modal pr-0" id="productView" tabindex="-1" role="dialog" aria-labelledby="productViewLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productViewLabel">Iphone 6</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-4 item-image">
                            <img src="" class="img-fluid" alt="Item Image">
                        </div>
                        <div class="col-8">
                            <p></p>
                            <div class="badge badge-pill badge-dark"></div>
                            <div class="badge badge-pill badge-dark"></div>
                            <p></p>
                            <h3 class="text-danger">
                                <del class="text-muted f-16"></del>
                            </h3>
                            <p class="text-green"></p>
                        </div>
                    </div>
                    <h5><strong>Stock History</strong></h5>
                    <div>
                        <table id="stock-history">
                            <thead>
                            <tr>
                                <th>Quantity</th>
                                <th>Note</th>
                                <th>Added By</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>10</td>
                                <td>Restocked</td>
                                <td>Admin</td>
                            </tr>
                            <tr>
                                <td>-5</td>
                                <td>Sold</td>
                                <td>John</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div id="line_chart" class="chart-shadow"></div>
                </div>
            </div>
        </div>
    </div>


    <script type="text/javascript">
        // Event listener for view item button
        $(document).on('click', '.view-item', function() {
            var itemId = $(this).data('id');

            // Fetch item data using AJAX
            $.ajax({
                url: '{{ url('item') }}/' + itemId,
                method: 'GET',
                success: function(response) {
                    // Log the response to the console (optional)
                    console.log(response);

                    if (response) {
                        // Populate modal with item details
                        $('#productViewLabel').text(response.name);  // Set the item name in the modal header

                        // Set the image source
                        $('#productView .item-image img').attr('src', '{{ asset('storage/') }}/' + response.image);

                        // Display the item type
                        $('#productView .modal-body .badge').first().text(response.item_type ? response.item_type.name : 'No Item Type');

                        // Display the brands
                        $('#productView .modal-body .badge').last().text(response.brands && response.brands.length ? response.brands.map(brand => brand.name).join(', ') : 'No Brands');

                        // Display price information
                        $('#productView .modal-body h3.text-danger').html('$' + response.price + ' <del class="text-muted f-16"> $' + response.original_price + '</del>');
                        $('#productView .modal-body p.text-green').text('Purchase Price: $' + response.purchase_price);
                        $('#productView .modal-body p').last().text('In Stock: ' + response.total_stock);
                        $('#productView .modal-body p').last().after('<p>Supplier: ' + response.supplier + '</p>');
                        $('#productView .modal-body p').last().after('<p>' + response.description + '</p>');

                        // Populate the stock history table
                        let stockHistoryHtml = '';
                        response.item_stock.forEach(function(stock) {
                            stockHistoryHtml += `
                        <tr>
                            <td>${stock.quantity}</td>
                            <td>${stock.note}</td>
                            <td>${stock.created_by}</td>
                        </tr>
                    `;
                        });
                        $('#stock-history tbody').html(stockHistoryHtml); // Insert stock history rows into the table

                        // Show the modal
                        $('#productView').modal('show');
                    }
                },
                error: function() {
                    alert('Error fetching item data');
                }
            });
        });

    </script>

    <!---------------Modal End---------------->

@endsection

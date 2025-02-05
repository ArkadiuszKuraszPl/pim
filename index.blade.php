@extends('layouts.admin')

@section('content')
        @include('helpers.flash-messages')

        <!-- Wyświetlanie błędów -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Produkty</h4>
                </div>

                @role('admin|dyrektor')
                    <a href="{{ route('products.create') }}" class="btn btn-info mb-3">+ Nowy produkt</a>
                @endrole
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        @role('admin|dyrektor')
                            <div class="mb-3">
                                <button type="button" class="btn btn-secondary btn-sm waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#myModal"><i class="mdi mdi-import"></i> Importuj</button>
                            </div>
                        @endrole

                        <!-- sample modal content -->
                        <div id="myModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="myModalLabel">Importowanie danych</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>

                                    <form action="{{ route('products.import') }}" method="post" enctype="multipart/form-data">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label for="file">Wybierz plik z danymi (.csv)</label>
                                                <input type="file" name="file" id="file" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-info waves-effect waves-light">Importuj dane</button>
                                        </div>
                                    </form>
                                </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div><!-- /.modal -->

                        <h4 class="card-title">Lista produktów</h4>

                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th></th>
                                    <th>SKU</th>
                                    <th>EAN</th>
                                    <th>Nazwa</th>
                                    <th>Kategoria</th>
                                    <!-- <th>Postęp</th> -->
                                    <th>Warianty</th>
                                    <th style="text-align: right;">Akcja</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($products as $product)
                                    <tr style="vertical-align: middle;">
                                        <td>{{ $product->id }}</td>
                                        <td><img src="{{ asset($product->product_main_image) }}" alt="-" width="55px"></td>
                                        <td>{{ $product->sku }} ({{ strlen($product->sku) }} znaków)</td>
                                        <td>{{ $product->ean }}</td>
                                        <td>
                                            @role('admin|dyrektor')
                                                <a href="{{ route('products.edit', $product->id) }}">{{ $product->name }}</a>
                                            @else
                                                <a href="{{ route('products.show', $product->id) }}">{{ $product->name }}</a>
                                            @endrole
                                        </td>
                                        <td>{{ $product->category ? $product->category->name : '-' }}</td>
                                        <!-- <td>
                                            <div class="progress">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </td> -->
                                        <td>
                                            @if(isset($productTypes[$product->type_id]) && $productTypes[$product->type_id] === "tak")
                                                <span>{{ $product->variants->count() }}</span>
                                                <a href="{{ route('variants.index', $product->id) }}" class="btn" style="margin-left: 10px;">
                                                    <i class="ri-play-list-add-line"></i>
                                                </a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td style="text-align: right;">
                                            <a href="{{ route('products.show', $product->id) }}">
                                                <button type="button" class="btn btn-sm"><i class="ri-computer-line"></i></button></a>

                                            @role('admin|dyrektor')
                                                <a href="{{ route('products.edit', $product->id) }}">
                                                    <button type="button" class="btn btn-sm"><i class="ri-pencil-line"></i></button></a>
                                            @endrole
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div> <!-- end col -->
        </div> <!-- end row -->
@endsection
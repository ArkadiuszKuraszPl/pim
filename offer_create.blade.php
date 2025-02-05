@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Tworzenie nowej oferty</h4>

                    <form method="post" action="{{ route('offers.store') }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-2" style="border: 1px solid #eee; padding: 15px; padding-top: 10px">
                                    <label for="expiration_date" class="mb-2">Wybierz czas obowiązywania oferty</label>
                                    <input id="expiration_date" type="date" class="form-control @error('expiration_date') is-invalid @enderror" name="expiration_date" value="{{ old('expiration_date') }}" autocomplete="expiration_date" required autofocus style="background-color: #ff000011;">

                                    @error('expiration_date')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <h5>Dane klienta</h5>

                            <div class="col-md-12">
                                <div class="mb-2">
                                    <label for="client_company" class="mb-2">Nazwa firmy</label>
                                    <input id="client_company" maxlength="250" type="text" class="form-control @error('client_company') is-invalid @enderror" name="client_company" value="{{ old('client_company') }}" required autocomplete="client_company" autofocus>

                                    @error('client_company')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-2">
                                    <label for="client_name" class="mb-2">Nazwa klienta</label>
                                    <input id="client_name" maxlength="250" type="text" class="form-control @error('client_name') is-invalid @enderror" name="client_name" value="{{ old('client_name') }}" required autocomplete="client_name" autofocus>

                                    @error('client_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label for="client_street" class="mb-2">Ulica</label>
                                    <input id="client_street" maxlength="250" type="text" class="form-control @error('client_street') is-invalid @enderror" name="client_street" value="{{ old('client_street') }}" autocomplete="client_street" autofocus>

                                    @error('client_street')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label for="client_city" class="mb-2">Miasto</label>
                                    <input id="client_city" maxlength="250" type="text" class="form-control @error('client_city') is-invalid @enderror" name="client_city" value="{{ old('client_city') }}" autocomplete="client_city" autofocus>

                                    @error('client_city')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label for="client_post_code" class="mb-2">Kod pocztowy</label>
                                    <input id="client_post_code" maxlength="250" type="text" class="form-control @error('client_post_code') is-invalid @enderror" name="client_post_code" value="{{ old('client_post_code') }}" autocomplete="client_post_code" autofocus>

                                    @error('client_post_code')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label for="client_country" class="mb-2">Kraj</label>
                                    <select name="client_country" id="client_country" class="form-select">
                                        @foreach ($countries as $country)
                                            @if(!($country->name === 'Świat') && !($country->name === 'Europa'))
                                                <option value="{{ $country->name }}" @if($country->id == 1) selected @endif>{{ $country->name }}</option>         
                                            @endif                                   
                                        @endforeach
                                    </select>

                                    @error('client_country')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label for="client_nip" class="mb-2">Numer NIP</label>
                                    <input id="client_nip" maxlength="20" type="text" class="form-control @error('client_nip') is-invalid @enderror" name="client_nip" value="{{ old('client_nip') }}" autocomplete="client_nip" autofocus>

                                    @error('client_nip')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <h5 class="mt-2">Dodatkowe informacje</h5>

                            <div class="col-md-12">
                                <div class="mb-2">
                                    <label for="description" class="mb-2">Opis (ten tekst będzie widoczny nad produktami)</label>
                                    <textarea name="description" id="elm1" class="form-control"></textarea>

                                    @error('description')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label for="price_country" class="mb-2">Wybierz kraj/obszar dla którego pobrać ceny</label>
                                    <select name="price_country" id="price_country" class="form-select">
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                                        @endforeach
                                    </select>

                                    @error('price_country')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <style>
                            .dataTables_scrollBody {
                                max-height: 550px !important;
                            }
                        </style>

                        <div class="products">
                            <h5 class="mt-3">Produkty</h5>

                            <div class="table-responsive">
                                <table id="scroll-vertical-datatable" class="table dt-responsive nowrap w-100">
                                    <thead>
                                        <tr>
                                            <th>
                                                <!-- <input type="checkbox" id="selectAllProducts"> -->
                                            </th>
                                            <th></th>
                                            <th>Kod EAN</th>
                                            <th>SKU</th>
                                            <th>Nazwa produktu</th>
                                            <th>Ilość</th>
                                            <th>Rabat (%)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($products as $product)
                                            <tr style="vertical-align: middle;">
                                                <td>
                                                    <input type="checkbox" name="selected_products[{{ $product->id }}][id]"
                                                        class="product-checkbox"
                                                        value="{{ $product->id }}">
                                                </td>
                                                <td><img src="{{ asset($product->product_main_image) }}" alt="" width="50px"></td>
                                                <td>{{ $product->ean }}</td>
                                                <td>{{ $product->sku }}</td>
                                                <td>{{ $product->name }}</td>
                                                <td>
                                                    <input type="number"
                                                        name="selected_products[{{ $product->id }}][quantity]"
                                                        class="form-control"
                                                        min="0"
                                                        value="0"
                                                        disabled>
                                                </td>
                                                <td>
                                                    <input type="number"
                                                        name="selected_products[{{ $product->id }}][discount]"
                                                        class="form-control"
                                                        min="0"
                                                        max="100"
                                                        value="0"
                                                        disabled>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-md-6 mt-3">
                            <div>
                                <label for="offer_lead_time" class="mb-2">Ile czasu potrzeba na realizację oferty - wprowadź ilość pełnych dni</label>
                                <input id="offer_lead_time" min="0" type="number" class="form-control @error('offer_lead_time') is-invalid @enderror" name="offer_lead_time" value="{{ old('offer_lead_time') }}" autocomplete="offer_lead_time" required autofocus>

                                @error('offer_lead_time')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <button type="submit" class="btn btn-info waves-effect waves-light mt-4">
                            Zakończ przygotowywanie oferty
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // const selectAllCheckbox = document.getElementById('selectAllProducts');
            const productCheckboxes = document.querySelectorAll('.product-checkbox');

            /* selectAllCheckbox.addEventListener('change', function () {
                productCheckboxes.forEach(checkbox => {
                    checkbox.checked = selectAllCheckbox.checked;

                    const row = checkbox.closest('tr');
                    const discountInput = row.querySelector('input[name*="[discount]"]');
                    const quantityInput = row.querySelector('input[name*="[quantity]"]');

                    discountInput.disabled = !checkbox.checked;
                    quantityInput.disabled = !checkbox.checked;
                });
            });*/

            productCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function () {
                    const row = this.closest('tr');
                    const discountInput = row.querySelector('input[name*="[discount]"]');
                    const quantityInput = row.querySelector('input[name*="[quantity]"]');

                    // Włącz/wyłącz pola rabatu i ilości
                    discountInput.disabled = !this.checked;
                    quantityInput.disabled = !this.checked;

                    // Dodaj event listenery do pól tylko raz
                    if (!discountInput.dataset.listenerAdded) {
                        discountInput.addEventListener('input', recalculateRowTotal);
                        quantityInput.addEventListener('input', recalculateRowTotal);
                        discountInput.dataset.listenerAdded = true; // Zapobiegnij wielokrotnemu dodawaniu listenerów
                    }
                });
            });
        });
    </script>

    
@endsection

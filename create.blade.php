@extends('layouts.admin')

@section('content')
<style>
    .nav-pills .nav-link.active, .nav-pills .show > .nav-link {
        background-color: #252b3b;
    }
</style>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <button onclick="goBack()" class="btn btn-light">
                    <i class="mdi mdi-arrow-left"></i> Opuść formularz
                </button>

                <script>
                    function goBack() {
                        window.history.back();
                    }
                </script>

                <h4 class="card-title my-4">Dodaj produkt</h4>

                <form method="post" action="{{ route('products.store') }}" enctype="multipart/form-data">
                    @csrf

                    <button type="submit" class="btn btn-info waves-effect waves-light" style="position: absolute; top: 1.25rem; right: 1.25rem;">Dodaj produkt</button>

                    <div class="row mt-4">
                        <div class="col-md-2">
                            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical" style="background-color: #fafafa; border-radius: .25rem;">
                                <a class="nav-link mb-2 active" id="v-pills-basicdata-tab" data-bs-toggle="pill" href="#v-pills-basicdata" role="tab" aria-controls="v-pills-basicdata" aria-selected="true">Dane podstawowe</a>
                                <a class="nav-link mb-2" id="v-pills-gpsr-tab" data-bs-toggle="pill" href="#v-pills-gpsr" role="tab" aria-controls="v-pills-gpsr" aria-selected="true">Bezpieczeństwo produktu</a>
                                <a class="nav-link mb-2" id="v-pills-customs-information-tab" data-bs-toggle="pill" href="#v-pills-customs-information" role="tab" aria-controls="v-pills-customs-information" aria-selected="true">Informacje celne</a>
                                <a class="nav-link mb-2" id="v-pills-size-tab" data-bs-toggle="pill" href="#v-pills-size" role="tab" aria-controls="v-pills-size" aria-selected="false">Wielkości</a>
                                <a class="nav-link mb-2" id="v-pills-specification-tab" data-bs-toggle="pill" href="#v-pills-specification" role="tab" aria-controls="v-pills-specification" aria-selected="false">Specyfikacja</a>
                                <a class="nav-link mb-2" id="v-pills-decorations-tab" data-bs-toggle="pill" href="#v-pills-decorations" role="tab" aria-controls="v-pills-decorations" aria-selected="false">Dekoracja</a>
                                <a class="nav-link mb-2" id="v-pills-finish-tab" data-bs-toggle="pill" href="#v-pills-finish" role="tab" aria-controls="v-pills-finish" aria-selected="false">Wykończenie</a>
                                <a class="nav-link mb-2" id="v-pills-stand-tab" data-bs-toggle="pill" href="#v-pills-stand" role="tab" aria-controls="v-pills-stand" aria-selected="false">Elementy</a>
                                <a class="nav-link mb-2" id="v-pills-characteristics-tab" data-bs-toggle="pill" href="#v-pills-characteristics" role="tab" aria-controls="v-pills-characteristics" aria-selected="false">Cecha produktu</a>
                                <a class="nav-link mb-2" id="v-pills-seo-tab" data-bs-toggle="pill" href="#v-pills-seo" role="tab" aria-controls="v-pills-seo" aria-selected="false">Opisy (SEO)</a>
                                <a class="nav-link mb-2" id="v-pills-price-tab" data-bs-toggle="pill" href="#v-pills-price" role="tab" aria-controls="v-pills-price" aria-selected="false">Ceny</a>
                                <a class="nav-link mb-2" id="v-pills-packing-tab" data-bs-toggle="pill" href="#v-pills-packing" role="tab" aria-controls="v-pills-packing" aria-selected="false">Pakowanie</a>
                                <a class="nav-link mb-2" id="v-pills-product-on-platform-tab" data-bs-toggle="pill" href="#v-pills-product-on-platform" role="tab" aria-controls="v-pills-product-on-platform" aria-selected="false">Platformy</a>
                                <a class="nav-link" id="v-pills-attachments-tab" data-bs-toggle="pill" href="#v-pills-attachments" role="tab" aria-controls="v-pills-attachments" aria-selected="false">Załączniki</a>
                            </div>
                        </div>
                        <div class="col-md-10 mt-2">
                            <div class="tab-content text-muted mt-4 mt-md-0" id="v-pills-tabContent">
                                <div class="tab-pane fade show active" id="v-pills-basicdata" role="tabpanel" aria-labelledby="v-pills-basicdata-tab">
                                    <h5>Dane podstawowe</h5>

                                    <div class="mb-2">
                                        <label for="name" class="mb-2">Nazwa</label>
                                        <input id="name" maxlength="50" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}"red autocomplete="name" autofocus>

                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="ean" class="form-label">Numer EAN</label>
                                        <input type="text" id="ean" name="ean" class="form-control" 
                                            value="{{ $availableEan->ean ?? '' }}" readonly>

                                        @error('ean')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="mb-2">
                                        <label for="sku" class="mb-2">Numer SKU</label>
                                        <input id="sku" maxlength="20" type="text" class="form-control @error('sku') is-invalid @enderror" name="sku" value="{{ old('sku') }}"red autocomplete="sku" autofocus>

                                        @error('sku')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="mb-2">
                                        <label for="type_id" class="mb-2">Typ produktu</label>
                                        <select class="form-select" name="type_id" id="type_id">
                                            <option value="">Wybierz typ produktu</option>
                                            @foreach ($productTypes as $productType)
                                                <option value="{{ $productType->id }}" data-has-variants="{{ $productType->product_variant }}">
                                                    {{ $productType->name }}
                                                    @if($productType->product_variant == 'tak')
                                                        (posiada warianty)
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>

                                        @error('type_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="mb-2">
                                        <label for="line_id" class="mb-2">Linia produktu</label>
                                        <select class="form-select" name="line_id">
                                            <option value="">Wybierz linię produktu</option>
                                            @foreach ($productLines as $productLine)
                                                <option value="{{ $productLine->id }}">{{ $productLine->name }}</option>
                                            @endforeach
                                        </select>

                                        @error('line_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="mb-2">
                                        <label for="position_no" class="mb-2">Pozycja na liście</label>
                                        <input id="position_no" maxlength="250" type="text" class="form-control @error('position_no') is-invalid @enderror" name="position_no" value="{{ old('position_no') }}" autocomplete="position_no" autofocus>

                                        @error('position_no')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <h5 class="mt-3">Statusy</h5>

                                    <div class="mb-2">
                                        <label for="status" class="mb-2">Czy produkt jest aktywny?</label>
                                        <select class="form-select" name="status">
                                            <option value="">Wybierz status</option>
                                            <option value="tak">Tak</option>
                                            <option value="nie">Nie</option>
                                        </select>

                                        @error('status')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="mb-2">
                                        <label for="manufactured_product" class="mb-2">Czy produkt jest produkowany?</label>
                                        <select class="form-select" name="manufactured_product">
                                            <option value="">Wybierz status</option>
                                            <option value="tak">Tak</option>
                                            <option value="nie">Nie</option>
                                        </select>

                                        @error('manufactured_product')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="mb-2">
                                        <label for="product_sold" class="mb-2">Czy produkt jest w sprzedaży detalicznej?</label>
                                        <select class="form-select" name="product_sold">
                                            <option value="">Wybierz status</option>
                                            <option value="tak">Tak</option>
                                            <option value="nie">Nie</option>
                                        </select>

                                        @error('product_sold')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="v-pills-gpsr" role="tabpanel" aria-labelledby="v-pills-gpsr-tab">
                                    <h5>Informacje dodatkowe na potrzeby GPSR - Bezpieczeństwo produktu</h5>

                                    <div class="mb-2">
                                        <label for="producer_id" class="mb-2">Producent</label>
                                        <select class="form-select" name="producer_id" id="producer_id">
                                            <option value="">Wybierz producenta</option>
                                            @foreach ($producers as $producer)
                                                <option value="{{ $producer->id }}">{{ $producer->name }}</option>
                                            @endforeach
                                        </select>

                                        @error('producer_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="mb-2">
                                        <label for="product_safety" class="mb-2">
                                            Informacje dotyczące bezpieczeństwa

                                            <a role="button" class="text-reset" title="" data-bs-placement="top" data-bs-toggle="tooltip" data-bs-original-title="W przypadku gdy produkt nie wymaga informacji dot. bezpieczeństwa pozostaw poniższe pole puste." aria-label="message"><i class="ri-error-warning-line text-info" style="font-weight: 400;"></i></a>
                                        </label>
                                        <textarea id="elm1" maxlength="1500" class="form-control @error('product_safety') is-invalid @enderror" name="product_safety" autofocus>{{ old('product_safety') }}</textarea>

                                        @error('product_safety')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="v-pills-customs-information" role="tabpanel" aria-labelledby="v-pills-customs-information-tab">
                                    <h5>Informacje celne</h5>

                                    <div class="mb-2">
                                        <label for="country_id" class="mb-2">Kraj pochodzenia</label>
                                        <select class="form-select" name="country_id">
                                            @foreach ($countries as $country)
                                                <option value="{{ $country->id }}" @if($country->id == 1) selected @endif>{{ $country->name }}</option>
                                            @endforeach
                                        </select>

                                        @error('country_origin')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="mb-2">
                                        <label for="customs_code" class="mb-2">Kod celny</label>
                                        <input id="customs_code" maxlength="15" type="text" class="form-control @error('customs_code') is-invalid @enderror" name="customs_code" value="{{ old('customs_code') }}" autocomplete="customs_code" autofocus>

                                        @error('customs_code')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="mb-2">
                                        <label for="customs_code_description" class="mb-2">Krótki opis kodu celnego</label>
                                        <textarea id="customs_code_description" maxlength="1500" class="form-control @error('customs_code_description') is-invalid @enderror" name="customs_code_description" autofocus>{{ old('customs_code_description') }}</textarea>

                                        @error('customs_code_description')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <!-- Tabela kodów dodatkowych -->
                                    <h5 class="mt-3">Kody dodatkowe</h5>

                                    <table id="additional_codes_table">
                                        <thead>
                                            <tr>
                                                <th>Typ kodu</th>
                                                <th>Kod produktu</th>
                                                <th>Nazwa załącznika</th>
                                                <th>Załącznik</th>
                                                <th>Rozszerzenie pliku</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Przykładowy wiersz początkowy -->
                                            <tr>
                                                <td>
                                                    <select class="form-select" name="additional_codes[0][code_type]"red>
                                                        <option value="EAN-13">EAN-13</option>
                                                        <option value="ISBN-13">ISBN-13</option>
                                                        <option value="GTIN-14">GTIN-14</option>
                                                        <option value="JAN-13">JAN-13</option>
                                                        <option value="MPN">MPN</option>
                                                        <option value="UNSPSC">UNSPSC</option>
                                                        <option value="ITEM NO">ITEM NO</option>
                                                        <option value="Amazon ASIN">Amazon ASIN</option>
                                                    </select>
                                                </td>
                                                <td><input class="form-control" type="text" name="additional_codes[0][product_code]"red></td>
                                                <td><input class="form-control" type="text" name="additional_codes[0][file_code]"></td>
                                                <td><input class="form-control" type="file" name="additional_codes[0][file]"></td>
                                                <td><input class="form-control" type="text" name="additional_codes[0][file_extension]" placeholder="np. .pdf"></td>
                                                <td>
                                                    <button class="btn" type="button" onclick="removeAdditionalCodeRow(this)"><i class="ri-delete-bin-line"></i></button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <!-- Przycisk dodawania nowego rekordu -->
                                    <button class="btn btn-sm btn-secondary mt-1 mb-3" type="button" onclick="addAdditionalCodeRow()" class="mb-3">Dodaj kod dodatkowy</button>

                                    <script>
                                        let additionalCodeIndex = 1;

                                        // Funkcja dodająca nowy wiersz do tabeli kodów dodatkowych
                                        function addAdditionalCodeRow() {
                                            const table = document.getElementById('additional_codes_table').getElementsByTagName('tbody')[0];
                                            const newRow = table.insertRow();

                                            newRow.innerHTML = `
                                                <td>
                                                    <select class="form-select" name="additional_codes[${additionalCodeIndex}][code_type]"red>
                                                        <option value="EAN-13">EAN-13</option>
                                                        <option value="ISBN-13">ISBN-13</option>
                                                        <option value="GTIN-14">GTIN-14</option>
                                                        <option value="JAN-13">JAN-13</option>
                                                        <option value="MPN">MPN</option>
                                                        <option value="UNSPSC">UNSPSC</option>
                                                        <option value="ITEM NO">ITEM NO</option>
                                                        <option value="Amazon ASIN">Amazon ASIN</option>
                                                    </select>
                                                </td>
                                                <td><input class="form-control" type="text" name="additional_codes[${additionalCodeIndex}][product_code]"red></td>
                                                <td><input class="form-control" type="text" name="additional_codes[${additionalCodeIndex}][file_code]"></td>
                                                <td><input class="form-control" type="file" name="additional_codes[${additionalCodeIndex}][file]"></td>
                                                <td><input class="form-control" type="text" name="additional_codes[${additionalCodeIndex}][file_extension]" placeholder="np. .pdf"></td>
                                                <td><button class="btn" type="button" onclick="removeAdditionalCodeRow(this)"><i class="ri-delete-bin-line"></i></button></td>
                                            `;

                                            additionalCodeIndex++;
                                        }

                                        // Funkcja usuwająca wiersz z tabeli kodów dodatkowych
                                        function removeAdditionalCodeRow(button) {
                                            const row = button.closest('tr');
                                            row.remove();
                                        }
                                    </script>
                                </div>
                                <div class="tab-pane fade" id="v-pills-size" role="tabpanel" aria-labelledby="v-pills-size-tab">
                                    <h5>Wielkości</h5>

                                    <div class="mb-2">
                                        <label for="height" class="mb-2">Wysokość</label>
                                        <input id="height" maxlength="150" type="text" class="form-control @error('height') is-invalid @enderror" name="height" value="{{ old('height') }}"red autocomplete="height" autofocus>

                                        @error('height')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="mb-2">
                                        <label for="width" class="mb-2">Szerokość</label>
                                        <input id="width" maxlength="150" type="text" class="form-control @error('width') is-invalid @enderror" name="width" value="{{ old('width') }}"red autocomplete="width" autofocus>

                                        @error('width')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="mb-2">
                                        <label for="depth" class="mb-2">Głębokość</label>
                                        <input id="depth" maxlength="150" type="text" class="form-control @error('depth') is-invalid @enderror" name="depth" value="{{ old('depth') }}"red autocomplete="depth" autofocus>

                                        @error('depth')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="mb-2">
                                        <label for="capacity" class="mb-2">Pojemność</label>
                                        <input id="capacity" maxlength="150" type="text" class="form-control @error('capacity') is-invalid @enderror" name="capacity" value="{{ old('capacity') }}"red autocomplete="capacity" autofocus>

                                        @error('capacity')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="mb-2">
                                        <label for="volume" class="mb-2">Objętość</label>
                                        <input id="volume" maxlength="150" type="text" class="form-control @error('volume') is-invalid @enderror" name="volume" value="{{ old('volume') }}"red autocomplete="volume" autofocus>

                                        @error('volume')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="v-pills-specification" role="tabpanel" aria-labelledby="v-pills-specification-tab">
                                    <h5>Specyfikacja</h5>

                                    <div class="mb-2">
                                        <label for="product_sales_season" class="mb-2">Sezon sprzedaży produktu</label>
                                        <input id="product_sales_season" maxlength="250" type="text" class="form-control @error('product_sales_season') is-invalid @enderror" name="product_sales_season" value="{{ old('product_sales_season') }}"red autocomplete="product_sales_season" autofocus>

                                        @error('product_sales_season')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="mb-2">
                                        <label for="glass_type_id" class="mb-2">Typ szkła</label>
                                        <select class="form-select" name="glass_type_id">
                                            <option value="">Wybierz typ szkła</option>
                                            @foreach ($glassTypes as $glassType)
                                                <option value="{{ $glassType->id }}">{{ $glassType->name }}</option>
                                            @endforeach
                                        </select>

                                        @error('glass_type_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="mb-2">
                                        <label for="shapes_id" class="mb-2">Kształt</label>
                                        <select class="form-select" name="shapes_id">
                                            <option value="">Wybierz kształt</option>
                                            @foreach ($shapes as $shape)
                                                <option value="{{ $shape->id }}">{{ $shape->name }}</option>
                                            @endforeach
                                        </select>

                                        @error('shapes_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="v-pills-decorations" role="tabpanel" aria-labelledby="v-pills-decorations-tab">
                                    <h5>Dekoracja</h5>

                                    <div class="mb-2">
                                        <label for="color_id" class="mb-2">Kolor</label>
                                        <select class="form-select" name="color_id">
                                            <option value="">Wybierz kolor</option>
                                            @foreach ($colors as $color)
                                                <option value="{{ $color->id }}">{{ $color->name }}</option>
                                            @endforeach
                                        </select>

                                        @error('color_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="mb-2">
                                        <label for="decoration_number_id" class="mb-2">Numer dekoracji</label>
                                        <select class="form-select" id="decoration_number_id" name="decoration_number_id">
                                            <option value="">Wybierz numer dekoracji</option>
                                            @foreach ($decorationNos as $decorationNo)
                                                <option value="{{ $decorationNo->id }}">{{ $decorationNo->symbol }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-2">
                                        <label for="decoration_name_id" class="mb-2">Nazwa dekoracji</label>
                                        <select class="form-select" id="decoration_name_id" name="decoration_name_id">
                                            <option value="">Wybierz nazwę dekoracji</option>
                                            @foreach ($decorationNames as $decorationName)
                                                <option value="{{ $decorationName->id }}">{{ $decorationName->symbol . ' - ' . $decorationName->name }}</option>
                                            @endforeach
                                        </select>

                                        <input type="hidden" id="hidden_decoration_name_id" name="decoration_name_id">
                                    </div>

                                    <script>
                                        const decorationMappings = @json($decorationNos->mapWithKeys(function ($decorationNo) {
                                            return [$decorationNo->id => $decorationNo->decorationName ? ['id' => $decorationNo->decorationName->id, 'symbol' => $decorationNo->decorationName->symbol] : null];
                                        }));

                                        document.addEventListener('DOMContentLoaded', function () {
                                            const decorationNumberSelect = document.getElementById('decoration_number_id');
                                            const decorationNameSelect = document.getElementById('decoration_name_id');
                                            const hiddenDecorationNameInput = document.getElementById('hidden_decoration_name_id');

                                            // Obsługa zmiany numeru dekoracji
                                            decorationNumberSelect.addEventListener('change', function () {
                                                const selectedNumberId = this.value;

                                                // Resetuj pole nazwy dekoracji
                                                decorationNameSelect.value = '';
                                                decorationNameSelect.disabled = false; // Umożliw wybór
                                                hiddenDecorationNameInput.value = ''; // Reset ukrytego pola

                                                // Jeśli wybrano numer dekoracji, ustaw nazwę dekoracji
                                                if (selectedNumberId && decorationMappings[selectedNumberId]) {
                                                    const mapping = decorationMappings[selectedNumberId];
                                                    if (mapping) {
                                                        decorationNameSelect.value = mapping.id; // Automatyczne wybranie
                                                        decorationNameSelect.disabled = true; // Zablokuj edycję
                                                        hiddenDecorationNameInput.value = mapping.id; // Ustaw wartość ukrytego pola
                                                    }
                                                }
                                            });

                                            // Obsługa ręcznego wyboru nazwy dekoracji
                                            decorationNameSelect.addEventListener('change', function () {
                                                hiddenDecorationNameInput.value = this.value; // Aktualizacja ukrytego pola
                                            });
                                        });
                                    </script>

                                    <div class="mb-2">
                                        <label for="decoration_type_id" class="mb-2">Typ dekoracji (m.in. malowania)</label>
                                        <select class="form-select" name="decoration_type_id">
                                            <option value="">Wybierz typ dekoracji</option>
                                            @foreach ($decorationTypes as $decorationType)
                                                <option value="{{ $decorationType->id }}">{{ $decorationType->symbol }}</option>
                                            @endforeach
                                        </select>

                                        @error('decoration_type_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="mb-2">
                                        <label for="decoration_description" class="mb-2">Opis dekoracji</label>
                                        <textarea id="decoration_description" maxlength="1500" class="form-control @error('decoration_description') is-invalid @enderror" name="decoration_description" autofocus>{{ old('decoration_description') }}</textarea>

                                        @error('decoration_description')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="v-pills-finish" role="tabpanel" aria-labelledby="v-pills-finish-tab">
                                    <h5>Wykończenia</h5>

                                    <table id="finish_table">
                                        <thead>
                                            <tr>
                                                <th>Nazwa wykończenia</th>
                                                <th>Typ wykończenia</th>
                                                <th>Opis wykończenia</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <select class="form-select" name="finishes[0][finish_name_id]">
                                                        <option value="">Wybierz nazwę wykończenia</option>
                                                        @foreach ($finishNames as $finishName)
                                                            <option value="{{ $finishName->id }}">{{ $finishName->symbol }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-select" name="finishes[0][finish_type_id]">
                                                        <option value="">Wybierz typ wykończenia</option>
                                                        @foreach ($finishTypes as $finishType)
                                                            <option value="{{ $finishType->id }}">{{ $finishType->symbol }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <textarea class="form-control" name="finishes[0][finish_description]" maxlength="1500"></textarea>
                                                </td>
                                                <td>
                                                    <button class="btn btn-danger btn-sm" type="button" onclick="removeRow(this)">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <!-- Przycisk dodawania nowego wiersza -->
                                    <button class="btn btn-sm btn-secondary mt-3" type="button" onclick="addFinishRow()">Dodaj wykończenie</button>

                                    <script>
                                        let finishIndex = 1;

                                        // Funkcja dodająca nowy wiersz do tabeli wykończeń
                                        function addFinishRow() {
                                            const table = document.getElementById('finish_table').getElementsByTagName('tbody')[0];
                                            const newRow = table.insertRow();

                                            newRow.innerHTML = `
                                                <td>
                                                    <select class="form-select" name="finishes[${finishIndex}][finish_name_id]">
                                                        <option value="">Wybierz nazwę wykończenia</option>
                                                        @foreach ($finishNames as $finishName)
                                                            <option value="{{ $finishName->id }}">{{ $finishName->symbol }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-select" name="finishes[${finishIndex}][finish_type_id]">
                                                        <option value="">Wybierz typ wykończenia</option>
                                                        @foreach ($finishTypes as $finishType)
                                                            <option value="{{ $finishType->id }}">{{ $finishType->symbol }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <textarea class="form-control" name="finishes[${finishIndex}][finish_description]" maxlength="1500"></textarea>
                                                </td>
                                                <td>
                                                    <button class="btn btn-danger btn-sm" type="button" onclick="removeRow(this)">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                </td>
                                            `;

                                            finishIndex++;
                                        }

                                        // Funkcja usuwająca wiersz z tabeli
                                        function removeRow(button) {
                                            const row = button.closest('tr');
                                            row.remove();
                                        }
                                    </script>
                                </div>
                                <div class="tab-pane fade" id="v-pills-stand" role="tabpanel" aria-labelledby="v-pills-stand-tab">
                                    <h5>Elementy</h5>

                                    <div class="mb-2">
                                        <label for="stand_name_id" class="mb-2">Nazwa elementu</label>
                                        <select class="form-select" name="stand_name_id">
                                            <option value="">Wybierz nazwę elementu</option>
                                            @foreach ($standNames as $standName)
                                                <option value="{{ $standName->id }}">{{ $standName->symbol . ' - ' . $standName->name  }}</option>
                                            @endforeach
                                        </select>

                                        @error('stand_name_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="mb-2">
                                        <label for="stand_type_id" class="mb-2">Typ elementu</label>
                                        <select class="form-select" name="stand_type_id">
                                            <option value="">Wybierz typ elementu</option>
                                            @foreach ($standTypes as $standType)
                                                <option value="{{ $standType->id }}">{{ $standType->symbol }}</option>
                                            @endforeach
                                        </select>

                                        @error('stand_type_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="mb-2">
                                        <label for="stand_description" class="mb-2">Opis elementu</label>
                                        <textarea id="stand_description" maxlength="1500" class="form-control @error('stand_description') is-invalid @enderror" name="stand_description" autofocus>{{ old('stand_description') }}</textarea>

                                        @error('stand_description')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="v-pills-characteristics" role="tabpanel" aria-labelledby="v-pills-characteristics-tab">
                                    <h5>Cechy produktu</h5>

                                    <div class="mb-2">
                                        <label for="hand_made" class="mb-2">Wykonane ręcznie</label>
                                        <select class="form-select" name="hand_made">
                                            <option value="">Wybierz opcję</option>
                                            <option value="tak">Tak</option>
                                            <option value="nie">Nie</option>
                                        </select>

                                        @error('hand_made')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="mb-2">
                                        <label for="machine_made" class="mb-2">Wykonane maszynowo</label>
                                        <select class="form-select" name="machine_made">
                                            <option value="">Wybierz opcję</option>
                                            <option value="tak">Tak</option>
                                            <option value="nie">Nie</option>
                                        </select>

                                        @error('machine_made')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="mb-2">
                                        <label for="artist_cuts" class="mb-2">Artystyczne cięcie? (Artist cuts)</label>
                                        <select class="form-select" name="artist_cuts">
                                            <option value="">Wybierz opcję</option>
                                            <option value="tak">Tak</option>
                                            <option value="nie">Nie</option>
                                        </select>

                                        @error('artist_cuts')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="mb-2">
                                        <label for="decal" class="mb-2">Naklejka</label>
                                        <select class="form-select" name="decal">
                                            <option value="">Wybierz opcję</option>
                                            <option value="tak">Tak</option>
                                            <option value="nie">Nie</option>
                                        </select>

                                        @error('decal')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="mb-2">
                                        <label for="gold" class="mb-2">Złoto</label>
                                        <select class="form-select" name="gold">
                                            <option value="">Wybierz opcję</option>
                                            <option value="tak">Tak</option>
                                            <option value="nie">Nie</option>
                                        </select>

                                        @error('gold')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="mb-2">
                                        <label for="platinum" class="mb-2">Platyna</label>
                                        <select class="form-select" name="platinum">
                                            <option value="">Wybierz opcję</option>
                                            <option value="tak">Tak</option>
                                            <option value="nie">Nie</option>
                                        </select>

                                        @error('platinum')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="mb-2">
                                        <label for="metalized" class="mb-2">Czy produkt jest metalizowany?</label>
                                        <select class="form-select" name="metalized">
                                            <option value="">Wybierz status</option>
                                            <option value="tak">Tak</option>
                                            <option value="nie">Nie</option>
                                        </select>

                                        @error('metalized')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="mb-2">
                                        <label for="sandblasted_partial_painted" class="mb-2">Piaskowane / Częściowo malowane</label>
                                        <select class="form-select" name="sandblasted_partial_painted">
                                            <option value="">Wybierz opcję</option>
                                            <option value="tak">Tak</option>
                                            <option value="nie">Nie</option>
                                        </select>

                                        @error('sandblasted_partial_painted')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="mb-2">
                                        <label for="gift_wrapping" class="mb-2">Możliwość zapakowania na prezent</label>
                                        <select class="form-select" name="gift_wrapping">
                                            <option value="">Wybierz opcję</option>
                                            <option value="tak">Tak</option>
                                            <option value="nie">Nie</option>
                                        </select>

                                        @error('gift_wrapping')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="mb-2">
                                        <label for="possibility_of_customization" class="mb-2">Możliwość customizacji</label>
                                        <select class="form-select" name="possibility_of_customization">
                                            <option value="">Wybierz opcję</option>
                                            <option value="tak">Tak</option>
                                            <option value="nie">Nie</option>
                                        </select>

                                        @error('possibility_of_customization')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="v-pills-seo" role="tabpanel" aria-labelledby="v-pills-seo-tab">
                                    <h5>SEO</h5>

                                    <div class="mb-2">
                                        <label for="category_id" class="mb-2">Kategoria handlowa - SEO</label>
                                        <select class="form-select" name="category_id">
                                            <option value="">Wybierz kategorię</option>
                                            @foreach ($productCategories as $productCategory)
                                                <option value="{{ $productCategory->id }}">{{ $productCategory->name }}</option>
                                            @endforeach
                                        </select>

                                        @error('category_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="mb-2">
                                        <label for="parent_category_id" class="mb-2">Nadrzędna kategoria handlowa - SEO</label>
                                        <select class="form-select" name="parent_category_id">
                                            <option value="">Wybierz kategorię nadrzędną</option>
                                            @foreach ($productCategories as $productCategory)
                                                <option value="{{ $productCategory->id }}">{{ $productCategory->name }}</option>
                                            @endforeach
                                        </select>

                                        @error('parent_category_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="d-flex mt-4">
                                        <div class="nav flex-column nav-pills me-3" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                            @foreach ($descriptionLanguages as $index => $language)
                                                <button class="nav-link @if($index === 0) active @endif" id="v-pills-{{ $language->id }}-tab" data-bs-toggle="pill" data-bs-target="#v-pills-{{ $language->id }}" type="button" role="tab" aria-controls="v-pills-{{ $language->id }}" aria-selected="@if($index === 0) true @else false @endif">
                                                    <img src="{{ asset('assets/images/flags/' . strtolower($language->code) . '.jpg') }}" alt="{{ $language->name }}" class="me-2" style="width: 24px;">
                                                    <!-- {{ $language->name }} -->
                                                </button>
                                            @endforeach
                                        </div>

                                        <!-- Treść zakładek -->
                                        <div class="tab-content" id="v-pills-tabContent">
                                            @foreach ($descriptionLanguages as $index => $language)
                                                <div class="tab-pane fade @if($index === 0) show active @endif" id="v-pills-{{ $language->id }}" role="tabpanel" aria-labelledby="v-pills-{{ $language->id }}-tab">
                                                    <div class="mb-2">
                                                        <img src="{{ asset('assets/images/flags/' . strtolower($language->code) . '.jpg') }}" alt="{{ $language->name }}">
                                                    </div>

                                                    <div class="mb-2">
                                                        <label for="name_desc_{{ $language->id }}" class="mb-2">Nazwa (dla produktu w {{ $language->name }})</label>
                                                        <input id="name_desc_{{ $language->id }}" maxlength="150" type="text" class="form-control @error('descriptions.' . $language->id . '.name_desc') is-invalid @enderror" name="descriptions[{{ $language->id }}][name_desc]" value="{{ old('descriptions.' . $language->id . '.name_desc') }}" autocomplete="name_desc_{{ $language->id }}" autofocus>

                                                        @error('descriptions.' . $language->id . '.name_desc')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>

                                                    <div class="mb-2">
                                                        <label for="short_description_{{ $language->id }}" class="mb-2">Krótki opis</label>
                                                        <textarea id="short_description_{{ $language->id }}" maxlength="1500" class="form-control @error('descriptions.' . $language->id . '.short_description') is-invalid @enderror" name="descriptions[{{ $language->id }}][short_description]" autofocus>{{ old('descriptions.' . $language->id . '.short_description') }}</textarea>

                                                        @error('descriptions.' . $language->id . '.short_description')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>

                                                    <div class="mb-2">
                                                        <label for="description_{{ $language->id }}" class="mb-2">Opis produktu</label>
                                                        <textarea id="description_{{ $language->id }}" maxlength="2500" class="form-control @error('descriptions.' . $language->id . '.description') is-invalid @enderror" name="descriptions[{{ $language->id }}][description]" autofocus>{{ old('descriptions.' . $language->id . '.description') }}</textarea>

                                                        @error('descriptions.' . $language->id . '.description')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>

                                                    <div class="mb-2">
                                                        <label for="tags_{{ $language->id }}" class="mb-2">Tagi (dla produktu w {{ $language->name }})</label>
                                                        <input id="tags_{{ $language->id }}" maxlength="150" type="text" class="form-control @error('descriptions.' . $language->id . '.tags') is-invalid @enderror" name="descriptions[{{ $language->id }}][tags]" value="{{ old('descriptions.' . $language->id . '.tags') }}" autocomplete="tags_{{ $language->id }}" autofocus>

                                                        @error('descriptions.' . $language->id . '.tags')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="v-pills-price" role="tabpanel" aria-labelledby="v-pills-price-tab">
                                    <!-- Tabela cennika -->
                                    <h5>Ceny</h5>

                                    <table id="price_table">
                                        <thead>
                                            <tr>
                                                <th>Nazwa ceny</th>
                                                <th>Kwota</th>
                                                <th>Waluta</th>
                                                <th>Typ kwoty</th>
                                                <th>Kraj/Obszar</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><input class="form-control" type="text" name="prices[0][price_id]"></td>
                                                <td><input class="form-control" type="number" name="prices[0][amount]" step="0.01"red></td>
                                                <td>
                                                    <select class="form-select" name="prices[0][currency_id]"red>
                                                        <option value=""></option>
                                                        @foreach ($currencies as $currency)
                                                            <option value="{{ $currency->id }}">{{ $currency->symbol }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-select" name="prices[0][price_type]"red>
                                                        <option value="netto">Netto</option>
                                                        <option value="brutto">Brutto</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-select" name="prices[0][country_id]"red>
                                                        <option value="{{ $countries[0]->id }}">{{ $countries[0]->name }}</option>
                                                    </select>
                                                </td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <!-- Przycisk dodawania nowego rekordu -->
                                    <button class="btn btn-sm btn-secondary mt-1 mb-3" type="button" onclick="addRow()" class="mb-3">Dodaj kraj/obszar</button>


                                    <script>
                                        let priceIndex = 1;

                                        // Funkcja dodająca nowy wiersz do tabeli cennika
                                        function addRow() {
                                            const table = document.getElementById('price_table').getElementsByTagName('tbody')[0];
                                            const newRow = table.insertRow();

                                            newRow.innerHTML = `
                                                <td><input class="form-control" type="text" name="prices[${priceIndex}][price_id]"></td>
                                                <td><input class="form-control" type="number" name="prices[${priceIndex}][amount]" step="0.01"red></td>
                                                <td>
                                                    <select class="form-select" name="prices[${priceIndex}][currency_id]"red>
                                                        <option value=""></option>
                                                        @foreach ($currencies as $currency)
                                                            <option value="{{ $currency->id }}">{{ $currency->symbol }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-select" name="prices[${priceIndex}][price_type]"red>
                                                        <option value="netto">Netto</option>
                                                        <option value="brutto">Brutto</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-select" name="prices[${priceIndex}][country_id]"red>
                                                        <option value="">Wybierz kraj/obszar</option>
                                                        @foreach ($countries as $country)
                                                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td><button class="btn" type="button" onclick="removeRow(this)"><i class="ri-delete-bin-line"></i></button></td>
                                            `;

                                            priceIndex++;
                                        }

                                        // Funkcja usuwająca wiersz z tabeli
                                        function removeRow(button) {
                                            const row = button.closest('tr');
                                            row.remove();
                                        }
                                    </script>
                                </div>
                                <div class="tab-pane fade" id="v-pills-packing" role="tabpanel" aria-labelledby="v-pills-packing-tab">
                                    <h5>Opakowania</h5>

                                    <div class="mb-2">
                                        <label for="packaging_select" class="mb-2">Wybierz rodzaj pakowania</label>
                                        <select id="packaging_select" class="form-select">
                                            <option value="">Wybierz opcje</option>
                                            @foreach ($packagingTypes as $packagingType)
                                                <option value="{{ $packagingType->id }}">{{ $packagingType->name }}</option>
                                            @endforeach
                                            
                                        </select>

                                        @error('packaging')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <!-- Tabela opakowań -->
                                    <h5 class="mt-3">Opakowania które zostały wybrane dla produktu</h5>

                                    <table id="packaging_table" class="table">
                                        <thead>
                                            <tr>
                                                <th>Rodzaj opakowania</th>
                                                <th>Akcje</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Tutaj będą dodawane wiersze dynamicznie -->
                                        </tbody>
                                    </table>

                                    <!-- Skrypt JavaScript -->
                                    <script>
                                        const packagingSelect = document.getElementById('packaging_select');
                                        const packagingTable = document.getElementById('packaging_table').getElementsByTagName('tbody')[0];
                                        let packagingIndex = 0;

                                        // Funkcja dodająca wybrane opakowanie do tabeli
                                        packagingSelect.addEventListener('change', function() {
                                            const selectedValue = packagingSelect.value;
                                            const selectedText = packagingSelect.options[packagingSelect.selectedIndex].text;

                                            if (selectedValue !== "") {
                                                // Dodajemy nowy wiersz do tabeli
                                                const newRow = packagingTable.insertRow();
                                                newRow.innerHTML = `
                                                    <td>
                                                        <input type="hidden" name="packaging[${packagingIndex}][type]" value="${selectedValue}">
                                                        ${selectedText}
                                                    </td>
                                                    <td><button class="btn" type="button" onclick="removePackagingRow(this)"><i class="ri-delete-bin-line"></i></button></td>
                                                `;

                                                // Zwiększamy indeks
                                                packagingIndex++;

                                                // Resetujemy select, aby użytkownik mógł dodać kolejne opakowanie
                                                packagingSelect.selectedIndex = 0;
                                            }
                                        });

                                        // Funkcja usuwająca wiersz z tabeli
                                        function removePackagingRow(button) {
                                            const row = button.closest('tr');
                                            row.remove();
                                        }
                                    </script>
                                </div>
                                <div class="tab-pane fade" id="v-pills-product-on-platform" role="tabpanel" aria-labelledby="v-pills-product-on-platform-tab">
                                    <h5>Platformy</h5>

                                    <div class="mb-2">
                                        <label for="platform_account_id" class="mb-2">Wybierz miejsce w którym produkt jest lub będzie sprzedawany</label>
                                        <select id="platform_account_id" class="form-select">
                                            <option value="">Wybierz konto</option>
                                            @foreach ($platformAccounts as $account)
                                                <option value="{{ $account->id }}">
                                                    {{ $account->account_name }} ({{ $account->platform->name }})
                                                </option>
                                            @endforeach
                                        </select>

                                        @error('platform_account_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <!-- Tabela kont platform -->
                                    <h5 class="mt-3">Wybrane konta platform</h5>

                                    <table id="platform_account_table" class="table">
                                        <thead>
                                            <tr>
                                                <th>Konto</th>
                                                <th>URL</th>
                                                <th>Akcje</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Wiersze dodawane dynamicznie -->
                                        </tbody>
                                    </table>

                                    <!-- Skrypt JavaScript -->
                                    <script>
                                        const accountSelect = document.getElementById('platform_account_id');
                                        const accountTable = document.getElementById('platform_account_table').getElementsByTagName('tbody')[0];
                                        let accountIndex = 0;

                                        // Funkcja dodająca wybrane konto platformy do tabeli
                                        accountSelect.addEventListener('change', function () {
                                            const selectedValue = accountSelect.value;
                                            const selectedText = accountSelect.options[accountSelect.selectedIndex].text;

                                            if (selectedValue !== "") {
                                                // Sprawdź, czy konto już istnieje w tabeli
                                                const existingRow = Array.from(accountTable.rows).find(
                                                    row => row.querySelector('input[name*="[platform_account_id]"]').value == selectedValue
                                                );
                                                if (existingRow) {
                                                    alert('To konto platformy zostało już dodane!');
                                                    accountSelect.selectedIndex = 0;
                                                    return;
                                                }

                                                // Dodaj nowy wiersz do tabeli
                                                const newRow = accountTable.insertRow();
                                                newRow.innerHTML = `
                                                    <td>
                                                        <input type="hidden" name="accounts[${accountIndex}][platform_account_id]" value="${selectedValue}">
                                                        ${selectedText}
                                                    </td>
                                                    <td>
                                                        <input type="text" name="accounts[${accountIndex}][url]" class="form-control" placeholder="Wprowadź URL">
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-danger" type="button" onclick="removeAccountRow(this)">
                                                            <i class="ri-delete-bin-line"></i>
                                                        </button>
                                                    </td>
                                                `;

                                                // Zwiększ indeks
                                                accountIndex++;

                                                // Resetuj select
                                                accountSelect.selectedIndex = 0;
                                            }
                                        });

                                        // Funkcja usuwająca wiersz z tabeli
                                        function removeAccountRow(button) {
                                            const row = button.closest('tr');
                                            row.remove();
                                        }
                                    </script>
                                </div>
                                <div class="tab-pane fade" id="v-pills-attachments" role="tabpanel" aria-labelledby="v-pills-attachments-tab">
                                    <h5>Załączniki</h5>

                                    <!-- Główne zdjęcie -->
                                    <div class="mb-2">
                                        <label for="product_main_image" class="mb-2">Główne zdjęcie produktu</label>
                                        <input type="file" class="form-control" name="product_main_image">

                                        @error('product_main_image')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <!-- Tabela zdjęć dodatkowych -->
                                    <h5 class="mt-3">Zdjęcia dodatkowe</h5>

                                    <table id="additional_images_table">
                                        <thead>
                                            <tr>
                                                <th>Plik</th>
                                                <th>Nazwa</th>
                                                <th>Rozszerzenie</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><input class="form-control" type="file" name="additional_images[0][file]"></td>
                                                <td><input class="form-control" type="text" name="additional_images[0][file_name]" placeholder="Nazwa zdjęcia"></td>
                                                <td><input class="form-control" type="text" name="additional_images[0][file_extension]" placeholder="np. .jpg"></td>
                                                <td><button class="btn" type="button" onclick="removeImageRow(this)"><i class="ri-delete-bin-line"></i></button></td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <!-- Przycisk dodawania nowego zdjęcia -->
                                    <button type="button" onclick="addImageRow()" class="btn btn-sm btn-secondary">Dodaj zdjęcie</button>

                                    <h5 class="mt-3">Pliki dodatkowe</h5>

                                    <!-- Dodawanie załącznika 3D -->
                                    <div class="mb-2">
                                        <label for="product_three_d" class="mb-2">Załącznik 3D</label>
                                        <input type="file" class="form-control" name="product_three_d" accept=".obj,.stl,.gltf">

                                        @error('product_three_d')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <script>
                                        let imageIndex = 1;

                                        // Funkcja dodająca nowy wiersz do tabeli zdjęć dodatkowych
                                        function addImageRow() {
                                            const table = document.getElementById('additional_images_table').getElementsByTagName('tbody')[0];
                                            const newRow = table.insertRow();

                                            newRow.innerHTML = `
                                                <td><input class="form-control" type="file" name="additional_images[${imageIndex}][file]" accept="image/*"></td>
                                                <td><input class="form-control" type="text" name="additional_images[${imageIndex}][file_name]" placeholder="Nazwa zdjęcia"></td>
                                                <td><input class="form-control" type="text" name="additional_images[${imageIndex}][file_extension]" placeholder="np. .jpg"></td>
                                                <td><button class="btn" type="button" onclick="removeImageRow(this)"><i class="ri-delete-bin-line"></i></button></td>
                                            `;

                                            imageIndex++;
                                        }

                                        // Funkcja usuwająca wiersz z tabeli zdjęć dodatkowych
                                        function removeImageRow(button) {
                                            const row = button.closest('tr');
                                            row.remove();
                                        }
                                    </script>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- end card -->
    </div>
</div>
@endsection
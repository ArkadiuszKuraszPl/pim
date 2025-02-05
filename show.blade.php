@extends('layouts.admin')

@section('content')
    <button onclick="goBack()" class="btn btn-light">
        <i class="mdi mdi-arrow-left"></i> Poprzednia strona
    </button>

    <script>
        function goBack() {
            window.history.back();
        }
    </script>

    <style>
        .product-image {
            width: 200px;
            height: 200px;
            object-fit: cover;
        }
    </style>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-4"><b>{{ $product->name }}</b></h4>

                    <img src="{{ asset($product->product_main_image) }}" alt="-" class="product-image" style="display: inline-block;">

                    @foreach ($product->additionalImages as $index => $image)
                        <img src="{{ asset($image->img_src) }}" alt="-" class="product-image" style="display: inline-block;">
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Główne style kontenera */
        .info-container {
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            font-family: Arial, sans-serif;
            border: 1px solid #f1f1f1; /* delikatna ramka kontenera */
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 20px;
            background-color: #fff;
        }

        /* Nagłówek */
        .info-header {
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 10px;
        }

        /* Style dla linii oddzielających */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            border-top: 1px solid #ddd; /* Linia o kolorze #ddd */
        }

        .info-table,
        .info-table tr {
            border-bottom: 1px solid #ddd; /* Linia o kolorze #ddd */
        }

        .info-table tr:last-child {
            border-bottom: none; /* Usuń linię pod ostatnim elementem */
        }

        .info-table td {
            padding: 10px 0;
            font-size: 14px;
            color: #333;
        }

        /* Styl dla etykiet */
        .info-label {
            color: #777;
            font-weight: normal;
            width: 350px;
        }

        /* Styl dla wartości */
        .info-value {
            font-weight: bold;
        }

        h5 {
            font-weight: bold;
            margin-bottom: 20px;
        }

        .head td {
            font-weight: bold;
            color: #222;
        }
    </style>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mt-1">
                            <h5>Dane podstawowe</h5>

                            <table class="info-table">
                                <tr>
                                    <td class="info-label">Kod EAN</td>
                                    <td class="info-value">{{ $product->ean }}</td>
                                </tr>
                                <tr>
                                    <td class="info-label">Numer SKU</td>
                                    <td class="info-value">{{ $product->sku }}</td>
                                </tr>
                                <tr>
                                    <td class="info-label">Linia produktu</td>
                                    <td class="info-value">@if($product->line_id) {{ $product->line->name }} @endif</td>
                                </tr>
                                <tr>
                                    <td class="info-label">Typ produktu</td>
                                    <td class="info-value">@if($product->type_id) {{ $product->type->name }} @endif</td>
                                </tr>
                            </table>
                        </div>

                        @role('admin|dyrektor')
                            <style>
                                .ri-check-line {
                                    color: green;
                                }

                                .ri-close-line {
                                    color: red;
                                }
                            </style>

                            <div class="col-md-12 mt-4 pt-2">
                                <h5>Statusy</h5>

                                <table class="info-table">
                                    <tr>
                                        <td class="info-label">Aktywny</td>
                                        <td class="info-value">@if($product->status == 'tak') <i class="ri-check-line"></i> @else <i class="ri-close-line"></i> @endif</td>
                                    </tr>
                                    <tr>
                                        <td class="info-label">Produkowany</td>
                                        <td class="info-value">@if($product->manufactured_product == 'tak') <i class="ri-check-line"></i> @else <i class="ri-close-line"></i> @endif</td>
                                    </tr>
                                    <tr>
                                        <td class="info-label">W sprzedaży</td>
                                        <td class="info-value">@if($product->product_sold == 'tak') <i class="ri-check-line"></i> @else <i class="ri-close-line"></i> @endif</td>
                                    </tr>
                                </table>
                            </div>
                        @endrole
                            
                        <div class="col-md-12 mt-4 pt-2">
                            <h5>Ceny</h5>

                            <table class="info-table">
                                <tr class="head">
                                    <td>Nazwa ceny</td>
                                    <td>Kwota</td>
                                    <td>Waluta</td>
                                    <td>Typ kwoty</td>
                                    <td>Kraj/Obszar</td>
                                </tr>

                                @foreach ($product->prices as $index => $price)
                                    <tr style="vertical-align: middle;">
                                        <td>{{ $price->price_id }}</td>
                                        <td>{{ $price->amount }}</td>
                                        <td>
                                            @php
                                                $selectedCurrency = $currencies->firstWhere('id', $price->currency_id);
                                            @endphp
                                            {{ $selectedCurrency ? $selectedCurrency->symbol : '-' }}
                                        </td>
                                        <td>
                                            {{ $price->price_type == 'netto' ? 'Netto' : ($price->price_type == 'brutto' ? 'Brutto' : '-') }}
                                        </td>
                                        <td>
                                            @php
                                                $selectedCountry = $countries->firstWhere('id', $price->country_id);
                                            @endphp
                                            {{ $selectedCountry ? $selectedCountry->name : '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>

                        <div class="col-md-12 mt-4 pt-2">
                            <h5>Stany magazynowe</h5>

                            <table class="info-table">
                                <tr class="head">
                                    <td>Magazyn</td>
                                    <td>Stan magazynowy</td>
                                </tr>
                                <tr style="vertical-align: middle;">
                                    <!-- <td></td>
                                    <td></td> -->
                                </tr>
                            </table>
                        </div>

                        @role('admin|dyrektor')
                            <div class="col-md-12 mt-4 pt-2">
                                <h5>Informacje celne</h5>

                                <table class="info-table">
                                    <tr>
                                        <td class="info-label">Kraj pochodzenia</td>
                                        <td class="info-value">@if($product->country_id) {{ $product->country->name }} @endif</td>
                                    </tr>
                                    <tr>
                                        <td class="info-label">Kod celny</td>
                                        <td class="info-value">{{ $product->customs_code }}</td>
                                    </tr>
                                    <tr>
                                        <td class="info-label">Krótki opis kodu celnego</td>
                                        <td class="info-value">{{ $product->customs_code_description }}</td>
                                    </tr>
                                </table>

                                <h5 class="mt-4 pt-2">Informacje celne (Kody dodatkowe)</h5>

                                <table class="info-table">
                                    <tr class="head">
                                        <td>Typ kodu</td>
                                        <td>Kod produktu</td>
                                        <td>Nazwa załącznika</td>
                                        <td>Załącznik</td>
                                        <td>Rozszerzenie pliku</td>
                                    </tr>

                                    @foreach ($product->additionalCodes as $index => $code)
                                        <tr style="vertical-align: middle;">
                                            <input type="hidden" name="additional_codes[{{ $index }}][id]" value="{{ $code->id }}">
                                            <td>{{ $code->code_type }}</td>
                                            <td>{{ $code->product_code }}</td>
                                            <td>{{ $code->file_code }}</td>
                                            <td>
                                                @if ($code->file)
                                                    <a href="{{ asset($code->file) }}" target="_blank">Obecny plik</a>
                                                @endif
                                            </td>
                                            <td>{{ $code->file_extension }}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>

                            <div class="col-md-12 mt-4 pt-2">
                                <h5>Wielkości</h5>

                                <table class="info-table">
                                    <tr>
                                        <td class="info-label">Wysokość</td>
                                        <td class="info-value">{{ $product->height }}</td>
                                    </tr>
                                    <tr>
                                        <td class="info-label">Szerokość</td>
                                        <td class="info-value">{{ $product->width }}</td>
                                    </tr>
                                    <tr>
                                        <td class="info-label">Głębokość</td>
                                        <td class="info-value">{{ $product->depth }}</td>
                                    </tr>
                                    <tr>
                                        <td class="info-label">Pojemność</td>
                                        <td class="info-value">{{ $product->capacity }}</td>
                                    </tr>
                                    <tr>
                                        <td class="info-label">Objętość</td>
                                        <td class="info-value">{{ $product->volume }}</td>
                                    </tr>
                                </table>
                            </div>

                            <div class="col-md-12 mt-4 pt-2">
                                <h5>Specyfikacja</h5>

                                <table class="info-table">
                                    <tr>
                                        <td class="info-label">Sezon sprzedaży produktu</td>
                                        <td class="info-value">{{ $product->product_sales_season }}</td>
                                    </tr>
                                    <tr>
                                        <td class="info-label">Typ szkła</td>
                                        <td class="info-value">@if($product->glassType) {{ $product->glassType->name }} @endif</td>
                                    </tr>
                                    <tr>
                                        <td class="info-label">Kształt</td>
                                        <td class="info-value">@if($product->shapes_id) {{ $product->shape->name }} @endif</td>
                                    </tr>
                                </table>
                            </div>

                            <div class="col-md-12 mt-4 pt-2">
                                <h5>Dekoracja</h5>

                                <table class="info-table">
                                    <tr>
                                        <td class="info-label">Kolor</td>
                                        <td class="info-value">@if($product->color_id) {{ $product->color->name }} @endif</td>
                                    </tr>
                                    <tr>
                                        <td class="info-label">Numer dekoracji</td>
                                        <td class="info-value">@if($product->decorationNumber) {{ $product->decorationNumber->symbol }} @endif</td>
                                    </tr>
                                    <tr>
                                        <td class="info-label">Nazwa dekoracji</td>
                                        <td class="info-value">@if($product->decorationName) {{ $product->decorationName->symbol . ' - ' . $product->decorationName->name }}  @endif</td>
                                    </tr>
                                    <tr>
                                        <td class="info-label">Typ dekoracji (m.in. malowania)</td>
                                        <td class="info-value">@if($product->decorationType) {{ $product->decorationType->symbol }}  @endif</td>
                                    </tr>
                                    <tr>
                                        <td class="info-label">Opis dekoracji</td>
                                        <td class="info-value">{{ $product->decoration_description }}</td>
                                    </tr>
                                </table>
                            </div>

                            <div class="col-md-12 mt-4 pt-2">
                                <h5>Wykończenie</h5>

                                <table class="info-table">
                                    <tr class="head">
                                        <td>Nazwa wykończenia</td>
                                        <td>Typ wykończenia</td>
                                        <td>Opis wykończenia</td>
                                    </tr>

                                    @foreach ($product->finishes as $index => $finish)
                                        <tr style="vertical-align: middle;">
                                            <td>
                                                @php
                                                    $selectedFinishName = $finishNames->firstWhere('id', $finish->finish_name_id);
                                                @endphp
                                                {{ $selectedFinishName ? $selectedFinishName->symbol : '' }}
                                            </td>
                                            <td>
                                                @php
                                                    $selectedFinishType = $finishTypes->firstWhere('id', $finish->finish_type_id);
                                                @endphp
                                                {{ $selectedFinishType ? $selectedFinishType->symbol : '' }}
                                            </td>
                                            <td>
                                                {{ $finish->finish_description }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>

                            <div class="col-md-12 mt-4 pt-2">
                                <h5>Elementy</h5>

                                <table class="info-table">
                                    <tr>
                                        <td class="info-label">Numer elementu</td>
                                        <td class="info-value"></td>
                                    </tr>
                                    <tr>
                                        <td class="info-label">Nazwa elementu</td>
                                        <td class="info-value">@php $selectedStandName = $standNames->firstWhere('id', $product->stand_name_id); @endphp {{ $selectedStandName ? $selectedStandName->symbol : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="info-label">Typ elementu</td>
                                        <td class="info-value">@php $selectedStandType = $standTypes->firstWhere('id', $product->stand_type_id); @endphp {{ $selectedStandType ? $selectedStandType->symbol : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="info-label">Opis elementu</td>
                                        <td class="info-value">{{ $product->stand_description }}</td>
                                    </tr>
                                </table>
                            </div>
                            
                            <div class="col-md-12 mt-4 pt-2">
                                <h5>Cecha produktu</h5>
                        
                                <table class="info-table">
                                    <tr>
                                        <td class="info-label">Wykonane ręcznie</td>
                                        <td class="info-value">@if($product->hand_made == 'tak') <i class="ri-check-line"></i> @else <i class="ri-close-line"></i> @endif</td>
                                    </tr>
                                    <tr>
                                        <td class="info-label">Wykonane maszynowo</td>
                                        <td class="info-value">@if($product->machine_made == 'tak') <i class="ri-check-line"></i> @else <i class="ri-close-line"></i> @endif</td>
                                    </tr>
                                    <tr>
                                        <td class="info-label">Artystyczne cięcie (Artist cuts)</td>
                                        <td class="info-value">@if($product->artist_cuts == 'tak') <i class="ri-check-line"></i> @else <i class="ri-close-line"></i> @endif</td>
                                    </tr>
                                    <tr>
                                        <td class="info-label">Naklejka</td>
                                        <td class="info-value">@if($product->decal == 'tak') <i class="ri-check-line"></i> @else <i class="ri-close-line"></i> @endif</td>
                                    </tr>
                                    <tr>
                                        <td class="info-label">Złoto</td>
                                        <td class="info-value">@if($product->gold == 'tak') <i class="ri-check-line"></i> @else <i class="ri-close-line"></i> @endif</td>
                                    </tr>
                                    <tr>
                                        <td class="info-label">Platyna</td>
                                        <td class="info-value">@if($product->platinum == 'tak') <i class="ri-check-line"></i> @else <i class="ri-close-line"></i> @endif</td>
                                    </tr>
                                    <tr>
                                        <td class="info-label">Czy produkt jest metalizowany</td>
                                        <td class="info-value">@if($product->metalized == 'tak') <i class="ri-check-line"></i> @else <i class="ri-close-line"></i> @endif</td>
                                    </tr>
                                    <tr>
                                        <td class="info-label">Piaskowane / Częściowo malowane</td>
                                        <td class="info-value">@if($product->sandblasted_partial_painted == 'tak') <i class="ri-check-line"></i> @else <i class="ri-close-line"></i> @endif</td>
                                    </tr>
                                    <tr>
                                        <td class="info-label">Możliwość zapakowania na prezent</td>
                                        <td class="info-value">@if($product->gift_wrapping == 'tak') <i class="ri-check-line"></i> @else <i class="ri-close-line"></i> @endif</td>
                                    </tr>
                                    <tr>
                                        <td class="info-label">Możliwość customizacji</td>
                                        <td class="info-value">@if($product->possibility_of_customization == 'tak') <i class="ri-check-line"></i> @else <i class="ri-close-line"></i> @endif</td>
                                    </tr>
                                </table>
                            </div>
                            
                            <div class="col-md-12 mt-4 pt-2">
                                <h5>Opisy (SEO)</h5>

                                <table class="info-table">
                                    <tr>
                                        <td class="info-label">Kategoria handlowa</td>
                                        <td class="info-value">@if($product->category_id) {{ $product->category->name }} @endif</td>                                    
                                    </tr>
                                    <tr>
                                        <td class="info-label">Nadrzędna kategoria handlowa</td>
                                        <td class="info-value">@if($product->parent_category_id) {{ $product->parentCategory->name }} @endif</td>
                                    </tr>
                                </table>

                                <div class="d-flex mt-3">
                                    <div class="nav flex-column nav-pills me-3" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                        @foreach ($descriptionLanguages as $index => $language)
                                            <button class="nav-link @if($index === 0) active @endif" id="v-pills-{{ $language->id }}-tab" data-bs-toggle="pill" data-bs-target="#v-pills-{{ $language->id }}" type="button" role="tab" aria-controls="v-pills-{{ $language->id }}" aria-selected="@if($index === 0) true @else false @endif">
                                                <img src="{{ asset('assets/images/flags/' . strtolower($language->code) . '.jpg') }}" alt="{{ $language->name }}" class="me-2" style="width: 24px;">
                                                {{ $language->name }}
                                            </button>
                                        @endforeach
                                    </div>

                                    <div class="tab-content" id="v-pills-tabContent">
                                        @foreach ($descriptionLanguages as $index => $language)
                                            @php
                                                $description = $product->descriptions->firstWhere('description_language_id', $language->id) ?? new \App\Models\ProductDescription;
                                            @endphp
                                            <div class="tab-pane fade @if($index === 0) show active @endif" id="v-pills-{{ $language->id }}" role="tabpanel" aria-labelledby="v-pills-{{ $language->id }}-tab">
                                                <div class="mb-2">
                                                    <img src="{{ asset('assets/images/flags/' . strtolower($language->code) . '.jpg') }}" alt="{{ $language->name }}">
                                                </div>

                                                <div class="mb-2">
                                                    <label for="name_desc_{{ $language->id }}" class="mb-2">Nazwa (dla produktu w {{ $language->name }})</label>
                                                    <input id="name_desc_{{ $language->id }}" maxlength="150" type="text" class="form-control @error('descriptions.' . $language->id . '.name_desc') is-invalid @enderror" name="descriptions[{{ $language->id }}][name_desc]" value="{{ old('descriptions.' . $language->id . '.name_desc', $description->name) }}" autocomplete="name_desc_{{ $language->id }}">
                                                    @error('descriptions.' . $language->id . '.name_desc')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>

                                                <div class="mb-2">
                                                    <label for="short_description_{{ $language->id }}" class="mb-2">Krótki opis</label>
                                                    <textarea id="short_description_{{ $language->id }}" maxlength="1500" class="form-control @error('descriptions.' . $language->id . '.short_description') is-invalid @enderror" name="descriptions[{{ $language->id }}][short_description]">{{ old('descriptions.' . $language->id . '.short_description', $description->short_description) }}</textarea>
                                                    @error('descriptions.' . $language->id . '.short_description')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>

                                                <div class="mb-2">
                                                    <label for="description_{{ $language->id }}" class="mb-2">Opis produktu</label>
                                                    <textarea id="description_{{ $language->id }}" maxlength="2500" class="form-control @error('descriptions.' . $language->id . '.description') is-invalid @enderror" name="descriptions[{{ $language->id }}][description]">{{ old('descriptions.' . $language->id . '.description', $description->description) }}</textarea>
                                                    @error('descriptions.' . $language->id . '.description')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>

                                                <div class="mb-2">
                                                    <label for="tags_{{ $language->id }}" class="mb-2">Tagi (dla produktu w {{ $language->name }})</label>
                                                    <input id="tags_{{ $language->id }}" maxlength="150" type="text" class="form-control @error('descriptions.' . $language->id . '.tags') is-invalid @enderror" name="descriptions[{{ $language->id }}][tags]" value="{{ old('descriptions.' . $language->id . '.tags', $description->tags) }}" autocomplete="tags_{{ $language->id }}">
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

                            <div class="col-md-12 mt-4 pt-2">
                                <h5>Pakowanie</h5>
                        
                                <table class="info-table">
                                    @foreach ($product->packagings as $index => $packaging)   
                                        <tr>
                                            <td class="info-label">{{ $packaging->packagingType->name }}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>

                            <div class="col-md-12 mt-4 pt-2">
                                <h5>Załączniki</h5>
                        
                                <table class="info-table">
                                    <tr>
                                        <td class="info-label">Plik 3D</td>
                                        <td class="info-value">@if($product->product_three_d) <a href="{{ asset($product->product_three_d) }}">Pobierz plik</a> @else Brak załącznika @endif</td>
                                    </tr>
                                </table>
                            </div>

                            @if($productTypeVariant === "tak")
                                <div class="col-md-12 mt-4 pt-2">
                                    <h5>Warianty</h5>
                            
                                    <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>EAN</th>
                                                <th>Nazwa</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($variants as $variant)
                                                <tr style="vertical-align: middle;">
                                                    <td>{{ $variant->id }}</td>
                                                    <td>{{ $variant->variant_ean }}</td>
                                                    <td>{{ $variant->variant_name }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif

                        @endrole
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
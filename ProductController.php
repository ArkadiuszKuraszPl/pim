<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Color;
use App\Models\Price;
use App\Models\Shape;
use App\Models\Country;
use App\Models\Product;
use App\Models\StandId;
use App\Models\StandNo;
use App\Models\Variant;
use App\Models\WaxType;
use App\Models\Currency;
use App\Models\FinishId;
use App\Models\FinishNo;
use App\Models\Platform;
use App\Models\Producer;
use App\Models\GlassType;
use App\Models\Packaging;
use App\Models\StandName;
use App\Models\StandType;
use App\Models\Warehouse;
use Illuminate\View\View;
use App\Models\FinishName;
use App\Models\FinishType;
use App\Models\ProductLine;
use App\Models\ProductType;
use App\Models\DecorationNo;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Models\EanDictionary;
use App\Models\PackagingType;
use App\Models\ProductFinish;
use App\Models\AdditionalCode;
use App\Models\DecorationName;
use App\Models\DecorationType;
use App\Models\PlatformAccount;
use App\Models\ProductCategorie;
use App\Models\ProductOnPlatform;
use App\Models\ProductDescription;
use App\Models\DescriptionLanguage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $products = Product::where('status', 'tak')->orWhereNull('status')->get();
        $productTypes = ProductType::pluck('product_variant', 'id');

        return view("products.index", [
            'products' => $products,
            'productTypes' => $productTypes,
        ]);
    }

    public function archives(): View
    {
        $products = Product::where('status', 'nie')->get();
        $productTypes = ProductType::pluck('product_variant', 'id');

        return view("products.archives", [
            'products' => $products,
            'productTypes' => $productTypes,
        ]);
    }

    public function import(Request $request)
    {
        // Walidacja pliku
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);
    
        // Przechowaj plik tymczasowo
        $file = $request->file('file');
        $filePath = $file->getRealPath();
    
        // Otwórz plik
        if (($handle = fopen($filePath, 'r')) !== false) {
            // Wczytaj pierwszy wiersz, żeby wykryć separator
            $firstLine = fgets($handle);
    
            // Wykrywanie separatora: średnik czy przecinek
            $separator = strpos($firstLine, ';') !== false ? ';' : ',';
    
            // Ustaw nagłówki na podstawie wykrytego separatora
            $header = str_getcsv($firstLine, $separator);
            $header = array_map('trim', array_map('strtolower', $header));
    
            // Sprawdzenie zgodności nagłówków z oczekiwanymi
            $expectedHeader = ['name', 'sku', 'ean'];
            if ($header !== $expectedHeader) {
                return redirect()->route('products.index')->withErrors('Nieprawidłowe nagłówki w pliku CSV. Oczekiwano: Name, SKU, EAN.');
            }
    
            // Przetwórz każdy wiersz pliku CSV
            while (($row = fgetcsv($handle, 0, $separator)) !== false) {
                // Połącz nagłówki z wartościami z wiersza
                $data = array_combine($header, $row);
    
                // Sprawdź, czy SKU istnieje w tabeli products
                $existingProductBySku = Product::where('sku', $data['sku'])->first();
                if ($existingProductBySku) {
                    // Jeśli SKU już istnieje, pomiń import tego rekordu
                    continue;
                }
    
                // Sprawdź, czy EAN istnieje w tabeli ean_dictionaries
                $existingEan = EanDictionary::where('ean', $data['ean'])->first();
    
                if ($existingEan) {
                    // Jeśli EAN istnieje w tabeli ean_dictionaries, sprawdź, czy ma powiązanie z produktem
                    if (!$existingEan->product_id) {
                        // Jeśli EAN istnieje, ale nie ma przypisanego produktu, utwórz produkt i przypisz
                        $newProduct = Product::create([
                            'name' => $data['name'],
                            'sku' => $data['sku'],
                            'ean' => $data['ean'],
                        ]);
    
                        // Zaktualizuj tabelę ean_dictionaries, przypisując ID nowego produktu
                        $existingEan->update(['product_id' => $newProduct->id]);
                    } else {
                        // Jeśli produkt już istnieje, pomiń import
                        continue;
                    }
                } else {
                    // Jeśli EAN nie istnieje w tabeli ean_dictionaries, utwórz nowy produkt i wpis do ean_dictionaries
                    $newProduct = Product::create([
                        'name' => $data['name'],
                        'sku' => $data['sku'],
                        'ean' => $data['ean'],
                    ]);
    
                    EanDictionary::create([
                        'ean' => $data['ean'],
                        'product_id' => $newProduct->id,
                    ]);
                }
            }
    
            fclose($handle);
        }
    
        // Przekierowanie po zakończeniu
        return redirect()->route('products.index')->with('status', 'Plik CSV został zaimportowany pomyślnie.');
    }
    


    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    {
        // Pobierz pierwszy wolny EAN, który nie ma przypisanego produktu ani wariantu i nie jest zarezerwowany
        $availableEan = EanDictionary::whereNull('product_id')
        ->whereNull('variant_id')
        ->where(function ($query) {
            $query->whereNull('reserved_at')
                ->orWhere('reserved_at', '<', now()->subMinutes(10)); // Rezerwacje starsze niż 10 minut
        })
        ->first();

        // Zarezerwuj kod EAN
        if ($availableEan) {
            $availableEan->reserved_at = now();
            $availableEan->save();
        }

        return view("products.create", [
            'productCategories' => ProductCategorie::all(),
            'productTypes' => ProductType::all(),
            'productLines' => ProductLine::all(),
            'glassTypes' => GlassType::all(),
            'shapes' => Shape::all(),
            'colors' => Color::all(),
            'decorationNos' => DecorationNo::all(),
            'decorationNames' => DecorationName::all(),
            'decorationTypes' => DecorationType::all(),
            'countries' => Country::all(),
            'currencies' => Currency::all(),
            'finishIds' => FinishId::all(),
            'finishNos' => FinishNo::all(),
            'finishNames' => FinishName::all(),
            'finishTypes' => FinishType::all(),
            'standIds' => StandId::all(),
            'standNos' => StandNo::all(),
            'standNames' => StandName::all(),
            'standTypes' => StandType::all(),
            'descriptionLanguages' => DescriptionLanguage::all(),
            'packagingTypes' => PackagingType::all(),
            'warehouses' => Warehouse::all(),
            'product' => new Product(),
            'platformAccounts' => PlatformAccount::all(),
            'producers' => Producer::all(),
        ], compact('availableEan'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'sku' => 'required|string|unique:products,sku',  // Unikalne i wymagane pole SKU

            // Obsługa wykończeń
            'finishes' => 'nullable|array',
            'finishes.*.finish_number_id' => 'nullable|exists:finish_nos,id',
            'finishes.*.finish_name_id' => 'nullable|exists:finish_names,id',
            'finishes.*.finish_type_id' => 'nullable|exists:finish_types,id',
            'finishes.*.finish_description' => 'nullable|string|max:1500',
        ]);

        // Sprawdzenie, czy numer EAN został przesłany
        $ean = $request->input('ean');
        
        // Jeśli nie podano EAN, pobierz pierwszy zarezerwowany
        if (!$ean) {
            $eanRecord = EanDictionary::whereNull('product_id')
                ->whereNull('variant_id')
                ->whereNotNull('reserved_at')
                ->where('reserved_at', '>=', now()->subMinutes(10))
                ->first();

            if ($eanRecord) {
                $ean = $eanRecord->ean;
            } else {
                return back()->withErrors('Brak dostępnych kodów EAN.');
            }
        }
    
        $product = new Product($request->all());
        $product->ean = $ean; // Przypisanie numeru EAN do produktu
        $product->save();

        // Przypisz produkt do kodu EAN i zwolnij rezerwację
        EanDictionary::where('ean', $ean)->update([
            'product_id' => $product->id,
            'reserved_at' => null,
        ]);

        // Obsługa powiązanych wykończeń
        if (!empty($validatedData['finishes'])) {
            foreach ($validatedData['finishes'] as $finish) {
                $product->finishes()->create($finish);
            }
        }

        // Zapis głównego zdjęcia
        if ($request->hasFile('product_main_image')) {
            $mainImage = $request->file('product_main_image');
            $mainImageName = time() . '_' . $mainImage->getClientOriginalName(); // Unikalna nazwa pliku
            $mainImage->move(public_path('product-images'), $mainImageName); // Zapis pliku do folderu 'public/product-images'

            // Aktualizacja kolumny 'product_main_image' w tabeli produktów
            $product->update([
                'product_main_image' => 'product-images/' . $mainImageName,
            ]);
        }

        // Zapis pliku 3D
        if ($request->hasFile('product_three_d')) {
            $threeDFile = $request->file('product_three_d');
            $threeDFileName = time() . '_' . $threeDFile->getClientOriginalName(); // Unikalna nazwa pliku
            $threeDFile->move(public_path('3d-projects'), $threeDFileName); // Zapis pliku do folderu 'public/3d-projects'

            // Aktualizacja kolumny 'product_three_d' w tabeli produktów
            $product->update([
                'product_three_d' => '3d-projects/' . $threeDFileName,
            ]);
        }

        $product_id = $product->id;

        // Sprawdzenie, czy w formularzu są przesłane dodatkowe zdjęcia
        if ($request->has('additional_images') && is_array($request->input('additional_images'))) {
            foreach ($request->input('additional_images') as $index => $imageData) {
                // Sprawdzenie, czy plik jest przesłany
                if ($request->hasFile("additional_images.$index.file")) {
                    // Przechowywanie pliku
                    $file = $request->file("additional_images.$index.file");
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->move(public_path('product-images'), $fileName);

                    // Zapis dodatkowego zdjęcia w bazie danych
                    ProductImage::create([
                        'product_id' => $product->id,
                        'img_title' => $imageData['file_name'] ?? null, // Domyślna wartość, jeśli nazwa nie jest ustawiona
                        'img_src' => 'product-images/' . $fileName, // Ścieżka do pliku
                        'img_extension' => $imageData['file_extension'] ?? null, // Domyślna wartość, jeśli rozszerzenie nie jest ustawione
                    ]);
                }
            }
        }

        // 1. Zapisz opisy produktów w różnych językach
        if ($request->has('descriptions')) {
            foreach ($request->input('descriptions') as $language_id => $descriptionData) {
                // Sprawdzanie, czy pole name_desc jest wypełnione
                if (!empty($descriptionData['name_desc'])) {
                    // Walidacja danych dla opisów w różnych językach tylko, jeśli name_desc jest uzupełnione
                    $validatedDescriptionData = Validator::make($descriptionData, [
                        'name_desc' => 'required|string|max:255', // Pole name_desc musi być wypełnione
                        'short_description' => 'nullable|string|max:1500',
                        'description' => 'nullable|string|max:2500',
                        'tags' => 'nullable|string|max:150',
                    ])->validate();

                    // Zapis opisu w tabeli product_descriptions
                    ProductDescription::create([
                        'product_id' => $product_id,
                        'description_language_id' => $language_id, // ID języka
                        'name' => $validatedDescriptionData['name_desc'], // Zmienione na name_desc
                        'short_description' => $validatedDescriptionData['short_description'] ?? null,
                        'description' => $validatedDescriptionData['description'] ?? null,
                        'tags' => $validatedDescriptionData['tags'] ?? null,
                    ]);
                }
            }
        }

        // 2. Zapisz dodatkowe kody
        if ($request->has('additional_codes')) {
            foreach ($request->input('additional_codes') as $index => $code) {
                // Walidacja danych dla dodatkowych kodów
                $validatedCodeData = Validator::make($code, [
                    'code_type' => 'required|string|max:255',
                    'product_code' => 'nullable|string|max:255',
                    'file_code' => 'nullable|string|max:255',
                    'file' => 'nullable|file|mimes:jpg,png,pdf|max:2048', // Dodaj odpowiednie typy plików i maksymalny rozmiar
                    'file_extension' => 'nullable|string|max:10',
                ])->validate();

                // Sprawdź, czy pole product_code jest puste
                if (empty($validatedCodeData['product_code'])) {
                    // Jeśli pole product_code jest puste, pomiń ten kod
                    continue;
                }

                // Zapisz plik, jeśli został załączony
                $filePath = null;
                if ($request->hasFile("additional_codes.$index.file")) {
                    // Nadaj unikalną nazwę plikowi
                    $file = $request->file("additional_codes.$index.file");
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    
                    // Zapisz plik do folderu 'codes' w publicznym katalogu
                    $filePath = $file->move(public_path('codes'), $fileName);

                    // Zapisz ścieżkę pliku do bazy danych
                    $filePath = 'codes/' . $fileName;
                }

                // Zapisz dane w tabeli additional_codes
                AdditionalCode::create([
                    'product_id' => $product_id,
                    'code_type' => $validatedCodeData['code_type'],
                    'product_code' => $validatedCodeData['product_code'],
                    'file_code' => $validatedCodeData['file_code'] ?? null,
                    'file' => $filePath,
                    'file_extension' => $validatedCodeData['file_extension'] ?? null,
                ]);
            }
        }

        // 3. Zapisanie wybranych pakowań
        if ($request->has('packaging')) {
            foreach ($request->input('packaging') as $packagingData) {
                // Walidacja danych pakowania
                $validatedPackagingData = Validator::make($packagingData, [
                    'type' => 'required|exists:packaging_types,id', // Sprawdzenie, czy pakowanie istnieje
                ])->validate();

                // Zapis pakowania do bazy danych
                Packaging::create([
                    'product_id' => $product_id,
                    'packaging_type_id' => $validatedPackagingData['type'],
                ]);
            }
        }

        // Sprawdzanie, czy formularz zawiera ceny
        if ($request->has('prices')) {
            foreach ($request->input('prices') as $priceData) {
                // Sprawdzenie, czy przynajmniej jedno kluczowe pole (amount lub currency_id) jest uzupełnione
                if (!empty($priceData['amount']) || !empty($priceData['currency_id'])) {
                    // Walidacja danych ceny tylko jeśli amount lub currency_id są uzupełnione
                    $validatedPriceData = Validator::make($priceData, [
                        'currency_id' => 'nullable|exists:currencies,id', // Nullable, jeśli puste
                        'price_id' => 'nullable|string|max:255', // Nullable, jeśli puste
                        'amount' => 'nullable|numeric',  // Nullable, jeśli puste
                        'price_type' => 'nullable|in:netto,brutto',  // Nullable
                        'country_id' => 'nullable|exists:countries,id', // Opcjonalne, jeśli puste
                    ])->validate();

                    // Zapisanie ceny do bazy danych, zapisuj null dla pustych pól
                    Price::create([
                        'product_id' => $product->id, // Zapisanie powiązania z produktem
                        'currency_id' => $validatedPriceData['currency_id'] ?? null,
                        'price_id' => $validatedPriceData['price_id'] ?? null,
                        'amount' => $validatedPriceData['amount'] ?? null,
                        'price_type' => $validatedPriceData['price_type'] ?? null,
                        'country_id' => $validatedPriceData['country_id'] ?? null, // Zapisz null, jeśli country_id jest puste
                    ]);
                }
            }
        }
    
        // Zapis wybranych kont platform
        if ($request->has('accounts')) {
            foreach ($request->input('accounts') as $accountData) {
                ProductOnPlatform::create([
                    'product_id' => $product->id,
                    'platform_account_id' => $accountData['platform_account_id'],
                    'url' => $accountData['url'] ?? null,
                ]);
            }
        }

        Log::create([
            'user_id' => Auth::id(), // ID zalogowanego użytkownika
            'product_id' => $product_id,
            'action' => 'create',
            'ip_address' => $request->ip(), // Adres IP użytkownika
        ]);
    
        // Przekierowanie z komunikatem o sukcesie
        return redirect()->route('products.index')->with('status', 'Produkt dodano pomyślnie');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $productTypeVariant = $product->type ? $product->type->product_variant : null;

        return view("products.show", [
            'product' => $product,
            'productCategories' => ProductCategorie::all(),
            'productTypes' => ProductType::all(),
            'productLines' => ProductLine::all(),
            'glassTypes' => GlassType::all(),
            'shapes' => Shape::all(),
            'colors' => Color::all(),
            'decorationNos' => DecorationNo::all(),
            'decorationNames' => DecorationName::all(),
            'decorationTypes' => DecorationType::all(),
            'countries' => Country::all(),
            'currencies' => Currency::all(),
            'finishIds' => FinishId::all(),
            'finishNos' => FinishNo::all(),
            'finishNames' => FinishName::all(),
            'finishTypes' => FinishType::all(),
            'standIds' => StandId::all(),
            'standNos' => StandNo::all(),
            'standNames' => StandName::all(),
            'standTypes' => StandType::all(),
            'descriptionLanguages' => DescriptionLanguage::all(),
            'packagingTypes' => PackagingType::all(),
            'packagings' => Packaging::all(),
            'descriptions' => ProductDescription::all(),
            'additionalImages' => ProductImage::all(),
            'variants' => Variant::where('product_id', $product->id)->get(),
            'productTypeVariant' => $productTypeVariant,
            'warehouses' => Warehouse::all(),
            'producers' => Producer::all(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product): View
    {
        $productTypeVariant = $product->type ? $product->type->product_variant : null;

        // Pobierz wykończenia powiązane z produktem
        $productFinishes = $product->finishes()->with(['finishName', 'finishType'])->get();

        return view("products.edit", [
            'product' => $product,
            'productCategories' => ProductCategorie::all(),
            'productTypes' => ProductType::all(),
            'productLines' => ProductLine::all(),
            'glassTypes' => GlassType::all(),
            'shapes' => Shape::all(),
            'colors' => Color::all(),
            'decorationNos' => DecorationNo::all(),
            'decorationNames' => DecorationName::all(),
            'decorationTypes' => DecorationType::all(),
            'countries' => Country::all(),
            'currencies' => Currency::all(),
            'finishIds' => FinishId::all(),
            'finishNos' => FinishNo::all(),
            'finishNames' => FinishName::all(),
            'finishTypes' => FinishType::all(),
            'standIds' => StandId::all(),
            'standNos' => StandNo::all(),
            'standNames' => StandName::all(),
            'standTypes' => StandType::all(),
            'descriptionLanguages' => DescriptionLanguage::all(),
            'packagingTypes' => PackagingType::all(),
            'packagings' => Packaging::all(),
            'descriptions' => ProductDescription::all(),
            'additionalImages' => ProductImage::all(),
            'variants' => Variant::where('product_id', $product->id)->get(),
            'productTypeVariant' => $productTypeVariant,
            'warehouses' => Warehouse::all(),
            'productFinishes' => $productFinishes, // Dodano wykończenia
            'platformAccounts' => PlatformAccount::all(),
            'producers' => Producer::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        // Walidacja podstawowych danych produktu
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'sku' => 'required|string|unique:products,sku,' . $product->id,  // Unikalny SKU dla danego produktu (wykluczamy aktualny produkt)

            // Obsługa wykończeń
            'finishes' => 'nullable|array',
            'finishes.*.finish_number_id' => 'nullable|exists:finish_nos,id',
            'finishes.*.finish_name_id' => 'nullable|exists:finish_names,id',
            'finishes.*.finish_type_id' => 'nullable|exists:finish_types,id',
            'finishes.*.finish_description' => 'nullable|string|max:1500',
        ]);


        // Zapis głównego zdjęcia
        if ($request->hasFile('product_main_image')) {
            $mainImage = $request->file('product_main_image');
            $mainImageName = time() . '_' . $mainImage->getClientOriginalName(); // Unikalna nazwa pliku
            $mainImage->move(public_path('product-images'), $mainImageName); // Zapis pliku do folderu 'public/product-images'

            // Aktualizacja kolumny 'product_main_image' w tabeli produktów
            $product->product_main_image = 'product-images/' . $mainImageName;
        }

        // Zapis pliku 3D
        if ($request->hasFile('product_three_d')) {
            $threeDFile = $request->file('product_three_d');
            $threeDFileName = time() . '_' . $threeDFile->getClientOriginalName(); // Unikalna nazwa pliku
            $threeDFile->move(public_path('3d-projects'), $threeDFileName); // Zapis pliku do folderu 'public/3d-projects'

            // Aktualizacja kolumny 'product_three_d' w tabeli produktów
            $product->product_three_d = '3d-projects/' . $threeDFileName;
        }




        // Aktualizacja pozostałych danych z żądania
        $product->fill($request->except(['product_main_image', 'product_three_d'])); // Pomija dane już zaktualizowane
        $product->save(); // Zapis wszystkich zmian w bazie danych


        $product_id = $product->id;


        
        $existingFinishIds = $product->finishes->pluck('id')->toArray();
        $submittedFinishIds = collect($validatedData['finishes'] ?? [])->pluck('id')->filter()->toArray();
        
        // Usunięcie wykończeń, które zostały usunięte w formularzu
        $finishesToDelete = array_diff($existingFinishIds, $submittedFinishIds);
        ProductFinish::whereIn('id', $finishesToDelete)->delete();
        
        // Aktualizacja lub dodanie nowych wykończeń, jeśli jakieś istnieją
        if (!empty($validatedData['finishes'])) {
            foreach ($validatedData['finishes'] as $finishData) {
                if (!empty($finishData['id'])) {
                    ProductFinish::find($finishData['id'])->update($finishData);
                } else {
                    $finishData['product_id'] = $product->id;
                    ProductFinish::create($finishData);
                }
            }
        }



        // Obsługa zdjęć dodatkowych
if ($request->has('additional_images') && is_array($request->input('additional_images'))) {
    // Pobierz aktualne zdjęcia powiązane z produktem
    $currentImages = ProductImage::where('product_id', $product->id)->get();

    // Tablica ID zdjęć, które zostały przekazane w formularzu
    $submittedImageIds = [];

    foreach ($request->input('additional_images') as $index => $imageData) {
        // Sprawdzenie, czy przesłano nowe zdjęcie
        if ($request->hasFile("additional_images.$index.file")) {
            $file = $request->file("additional_images.$index.file");
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->move(public_path('product-images'), $fileName); // Zapis pliku w publicznym katalogu

            if (isset($imageData['id'])) {
                // Jeśli zdjęcie już istnieje, zaktualizuj je
                $existingImage = $currentImages->where('id', $imageData['id'])->first();
                if ($existingImage) {
                    // Usuń stare zdjęcie z systemu plików
                    if ($existingImage->img_src && file_exists(public_path($existingImage->img_src))) {
                        unlink(public_path($existingImage->img_src));
                    }

                    // Aktualizacja istniejącego rekordu
                    $existingImage->update([
                        'img_title' => $imageData['file_name'] ?? $existingImage->img_title,
                        'img_src' => 'product-images/' . $fileName,
                        'img_extension' => $imageData['file_extension'] ?? null,
                    ]);
                }
            } else {
                // Tworzenie nowego rekordu, jeśli nie podano ID
                ProductImage::create([
                    'product_id' => $product->id,
                    'img_title' => $imageData['file_name'] ?? null,
                    'img_src' => 'product-images/' . $fileName,
                    'img_extension' => $imageData['file_extension'] ?? null,
                ]);
            }
        } elseif (isset($imageData['id'])) {
            // Jeśli zdjęcie już istnieje, ale nie załadowano nowego pliku, aktualizuj tylko tytuł
            $existingImage = $currentImages->where('id', $imageData['id'])->first();
            if ($existingImage) {
                $existingImage->update([
                    'img_title' => $imageData['file_name'] ?? $existingImage->img_title,
                ]);

                // Dodaj ID zdjęcia do tablicy
                $submittedImageIds[] = $existingImage->id;
            }
        }
    }

    // Usunięcie zdjęć, które zostały zarejestrowane, ale nie ma ich w formularzu
    foreach ($currentImages as $image) {
        if (!in_array($image->id, $submittedImageIds)) {
            // Usuń plik z systemu plików
            $filePath = public_path($image->img_src);
            if (file_exists($filePath)) {
                unlink($filePath); // Usunięcie pliku
            }
            // Usuń rekord z bazy danych
            $image->delete();
        }
    }
} else {
    // Jeśli brak zdjęć w formularzu, usuń wszystkie zdjęcia produktu
    $currentImages = ProductImage::where('product_id', $product->id)->get();
    foreach ($currentImages as $image) {
        // Usuń plik z systemu plików
        $filePath = public_path($image->img_src);
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        $image->delete();
    }
}









        

         // Usuwanie kodów dodatkowych
        $codesToDelete = $request->input('codes_to_delete', []);
        AdditionalCode::whereIn('id', $codesToDelete)->each(function ($code) {
            if ($code->file && file_exists(public_path($code->file))) {
                // Usunięcie pliku, jeśli istnieje
                unlink(public_path($code->file));
            }
            $code->delete();
        });

        // Obsługa kodów dodatkowych
        if ($request->has('additional_codes')) {
            foreach ($request->input('additional_codes', []) as $codeData) {
                $validatedCodeData = Validator::make($codeData, [
                    'id' => 'nullable|integer',
                    'code_type' => 'required|string|max:255',
                    'product_code' => 'nullable|string|max:255',
                    'file_code' => 'nullable|string|max:255',
                    'file' => 'nullable|file|mimes:jpg,png,pdf|max:2048', // Dodaj odpowiednie typy plików i maksymalny rozmiar
                    'file_extension' => 'nullable|string|max:10',
                ])->validate();
        
                // Obsługa załączonego pliku
                $filePath = null;
                if (isset($codeData['file']) && $request->hasFile("additional_codes.{$codeData['id']}.file")) {
                    $file = $request->file("additional_codes.{$codeData['id']}.file");
                    $filePath = 'codes/' . uniqid() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('codes'), $filePath);
                }
        
                if (isset($validatedCodeData['id'])) {
                    // Aktualizacja istniejącego kodu
                    $code = AdditionalCode::find($validatedCodeData['id']);
                    if ($code) {
                        // Jeśli jest nowy plik, usuń stary
                        if ($filePath && $code->file && file_exists(public_path($code->file))) {
                            unlink(public_path($code->file));
                        }
                        $code->update([
                            'code_type' => $validatedCodeData['code_type'],
                            'product_code' => $validatedCodeData['product_code'],
                            'file_code' => $validatedCodeData['file_code'],
                            'file' => $filePath,
                            'file_extension' => $validatedCodeData['file_extension'] ?? null,
                        ]);
                    }
                } else {
                    // Tworzenie nowego kodu dodatkowego
                    AdditionalCode::create([
                        'product_id' => $product->id,
                        'code_type' => $validatedCodeData['code_type'],
                        'product_code' => $validatedCodeData['product_code'],
                        'file_code' => $validatedCodeData['file_code'] ?? null,
                        'file' => $filePath,
                        'file_extension' => $validatedCodeData['file_extension'] ?? null,
                    ]);
                }
            }
        }
    





        // Obsługa cen
        $existingPriceIds = $product->prices->pluck('id')->toArray();
        $newPriceData = $request->input('prices', []);

        if (is_array($newPriceData) && !empty($newPriceData)) {
            $newIds = array_filter(array_column($newPriceData, 'id')); // Pobierz tylko istniejące ID z formularza

            // Usuwanie cen, które są obecnie przypisane do produktu, ale nie występują w formularzu
            $pricesToDelete = array_diff($existingPriceIds, $newIds);
            Price::whereIn('id', $pricesToDelete)->delete();

            // Przetwarzanie nowych i zaktualizowanych cen
            foreach ($newPriceData as $priceData) {
                $validatedPriceData = Validator::make($priceData, [
                    'id' => 'nullable|integer', // Autoinkrementowany klucz
                    'price_id' => 'nullable|string|max:255', // Kod wpisywany przez użytkownika
                    'amount' => 'nullable|numeric',
                    'currency_id' => 'nullable|exists:currencies,id',
                    'price_type' => 'nullable|in:netto,brutto',
                    'country_id' => 'nullable|exists:countries,id',
                ])->validate();

                if (!empty($validatedPriceData['id'])) {
                    // Znajdź istniejący rekord i zaktualizuj go
                    $price = Price::find($validatedPriceData['id']);
                    if ($price) {
                        $price->update([
                            'price_id' => $validatedPriceData['price_id'],
                            'amount' => $validatedPriceData['amount'],
                            'currency_id' => $validatedPriceData['currency_id'],
                            'price_type' => $validatedPriceData['price_type'],
                            'country_id' => $validatedPriceData['country_id'],
                        ]);
                    }
                } else {
                    // Dodaj nową cenę, jeśli `id` jest puste
                    Price::create([
                        'product_id' => $product->id,
                        'price_id' => $validatedPriceData['price_id'],
                        'amount' => $validatedPriceData['amount'],
                        'currency_id' => $validatedPriceData['currency_id'],
                        'price_type' => $validatedPriceData['price_type'],
                        'country_id' => $validatedPriceData['country_id'],
                    ]);
                }
            }
        }



        
// Obsługa pakowań
$existingPackagingIds = $product->packagings->pluck('packaging_type_id')->toArray();
$newPackagingData = $request->input('packaging', []); // Domyślna wartość: pusta tablica

// Jeśli formularz zawiera dane o pakowaniach
if (is_array($newPackagingData) && !empty($newPackagingData)) {
    $newPackagingIds = collect($newPackagingData)->pluck('type')->toArray();

    // Usunięcie pakowań, które nie zostały przesłane w formularzu
    $packagingToDelete = array_diff($existingPackagingIds, $newPackagingIds);
    if (!empty($packagingToDelete)) {
        Packaging::where('product_id', $product->id)
            ->whereIn('packaging_type_id', $packagingToDelete)
            ->delete();
    }

    // Dodanie nowych pakowań, które zostały dodane w formularzu
    foreach ($newPackagingData as $packagingData) {
        if (!in_array($packagingData['type'], $existingPackagingIds)) {
            Packaging::create([
                'product_id' => $product->id,
                'packaging_type_id' => $packagingData['type'],
            ]);
        }
    }
} else {
    // Jeśli nie przesłano danych pakowań w formularzu, usuń wszystkie istniejące pakowania
    $product->packagings()->delete();
}





        // Obsługa opisów produktów
        if ($request->has('descriptions')) {
            foreach ($request->input('descriptions') as $language_id => $descriptionData) {
                // Sprawdzanie, czy pole name_desc jest wypełnione
                if (!empty($descriptionData['name_desc'])) {
                    // Walidacja danych dla opisów w różnych językach
                    $validatedDescriptionData = Validator::make($descriptionData, [
                        'name_desc' => 'nullable|string|max:255',
                        'short_description' => 'nullable|string|max:1500',
                        'description' => 'nullable|string|max:2500',
                        'tags' => 'nullable|string|max:150',
                    ])->validate();

                    // Znajdź istniejący opis dla danego języka
                    $productDescription = ProductDescription::where('product_id', $product->id)
                        ->where('description_language_id', $language_id)
                        ->first();

                    if ($productDescription) {
                        // Jeśli opis istnieje, zaktualizuj go
                        $productDescription->update([
                            // 'description_language_id' => $language_id,
                            'name' => $validatedDescriptionData['name_desc'] ?? null,
                            'short_description' => $validatedDescriptionData['short_description'] ?? null,
                            'description' => $validatedDescriptionData['description'] ?? null,
                            'tags' => $validatedDescriptionData['tags'] ?? null,
                        ]);
                    } else {
                        // Jeśli opis nie istnieje, stwórz nowy
                        ProductDescription::create([
                            'product_id' => $product->id,
                            'description_language_id' => $language_id,
                            'name' => $validatedDescriptionData['name_desc'],
                            'short_description' => $validatedDescriptionData['short_description'] ?? null,
                            'description' => $validatedDescriptionData['description'] ?? null,
                            'tags' => $validatedDescriptionData['tags'] ?? null,
                        ]);
                    }
                }
            }
        }



        if ($request->has('accounts')) {
            $existingRecords = ProductOnPlatform::where('product_id', $product->id)->get();
        
            foreach ($request->input('accounts') as $accountData) {
                // Sprawdź, czy wpis już istnieje
                $existingRecord = $existingRecords->first(function ($record) use ($accountData) {
                    return $record->platform_account_id == $accountData['platform_account_id'];
                });
        
                if ($existingRecord) {
                    // Jeśli wpis istnieje, zaktualizuj jego URL (jeśli się zmienił)
                    $existingRecord->update([
                        'url' => $accountData['url'] ?? $existingRecord->url,
                    ]);
                } else {
                    // Jeśli wpis nie istnieje, utwórz nowy rekord
                    ProductOnPlatform::create([
                        'product_id' => $product->id,
                        'platform_account_id' => $accountData['platform_account_id'],
                        'url' => $accountData['url'] ?? null,
                    ]);
                }
            }
        
            // Opcjonalnie: usuń rekordy, które nie istnieją w nowym żądaniu
            $newAccountIds = collect($request->input('accounts'))->pluck('platform_account_id')->toArray();
            ProductOnPlatform::where('product_id', $product->id)
                ->whereNotIn('platform_account_id', $newAccountIds)
                ->delete();
        }
        



        // Zapis logów
        Log::create([
            'user_id' => Auth::id(), // ID zalogowanego użytkownika
            'product_id' => $product->id,
            'action' => 'update',
            'ip_address' => $request->ip(), // Adres IP użytkownika
            'changes' => json_encode($product->getChanges()), // Opcjonalnie: zapisuj zmienione pola
        ]);

        // Przekierowanie z komunikatem o sukcesie
        return back()->with('status', 'Zmiany w produkcie zostały zapisane!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Models\Price;
use App\Models\Country;
use App\Models\Product;
use App\Mail\OfferCreated;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class OfferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            // Administrator widzi wszystkie oferty
            $offers = Offer::all();
        } else {
            // Użytkownik widzi tylko swoje oferty i oferty potomków
            $userAndDescendantsIds = $user->getAllDescendants()->pluck('id')->toArray();
            $userAndDescendantsIds[] = $user->id; // Dodaj ID zalogowanego użytkownika
            $offers = Offer::whereIn('user_id', $userAndDescendantsIds)->get();
        }
    
        return view('offers.index', compact('offers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $products = Product::where('status', 'tak')->orWhereNull('status')->get();
        $countries = Country::all();
        return view('offers.create', compact('products', 'countries'));
    }

    public function store(Request $request)
    {
        // Walidacja danych
        $validatedData = $request->validate([
            'expiration_date' => 'required|date',
            'client_company' => 'required|string|max:250',
            'client_name' => 'required|string|max:250',
            'client_street' => 'nullable|string|max:250',
            'client_city' => 'nullable|string|max:250',
            'client_post_code' => 'nullable|string|max:250',
            'client_country' => 'required|string|max:250',
            'description' => 'nullable|string',
            'price_country' => 'required|exists:countries,id', // Walidacja ID kraju
            'selected_products' => 'array', // Produkty są opcjonalne
            'selected_products.*.id' => 'required|exists:products,id', // Produkt musi istnieć
            'selected_products.*.quantity' => 'required|integer|min:0', // Ilość >= 0
            'selected_products.*.discount' => 'required|numeric|min:0|max:100', // Walidacja rabatu
            'offer_total_price' => 'nullable',
            'currency_name' => 'nullable',
            'offer_lead_time' => 'nullable',
            'client_nip' => 'nullable|max:20',
        ]);

        // Utwórz nową ofertę
        $offer = Offer::create([
            'expiration_date' => $validatedData['expiration_date'],
            'client_company' => $validatedData['client_company'],
            'client_name' => $validatedData['client_name'],
            'client_street' => $validatedData['client_street'],
            'client_city' => $validatedData['client_city'],
            'client_post_code' => $validatedData['client_post_code'],
            'client_country' => $validatedData['client_country'],
            'description' => $validatedData['description'],
            'price_country_id' => $validatedData['price_country'],
            'offer_lead_time' => $validatedData['offer_lead_time'],
            'client_nip' => $validatedData['client_nip'],
            'user_id' => Auth::id(),
        ]);

        // aktualizacja nazwy oferty
        $offer->update(['offer_name' => $offer->id . '/OFH/' . $offer->created_at->format('Y')]);

        $offerTotalPrice = 0;
        $offerCurrencyName = null;

        $user = Auth::user();

        // Zapisz wybrane produkty do tabeli `offer_products`
        $selectedProducts = [];
        if (!empty($validatedData['selected_products'])) {
            foreach ($validatedData['selected_products'] as $productData) {
                $product = Product::find($productData['id']);

                // Sprawdź, czy produkt ma zdjęcie główne
                $productImagePath = public_path($product->product_main_image);
                if (!file_exists($productImagePath) || is_dir($productImagePath)) {
                    // Ustaw zdjęcie zastępcze, jeśli brakuje zdjęcia głównego
                    $productImagePath = public_path('product-images/no-photo.png');
                }

                // Pobierz cenę dla wybranego kraju
                $price = DB::table('prices')
                    ->where('product_id', $product->id)
                    ->where('country_id', $validatedData['price_country'])
                    ->where('price_type', 'netto')
                    ->first();

                // Jeśli cena nie istnieje, ustaw na 0
                $productPrice = $price ? $price->amount : 0;
                $currencyName = $price
                    ? DB::table('currencies')->where('id', $price->currency_id)->value('name') 
                    : ''; // Domyślna waluta
                $countryName = DB::table('countries')->where('id', $validatedData['price_country'])->value('name') 
                    ?: 'Nieznany'; // Domyślny kraj

                // Jeśli cena istnieje, pobierz walutę (tylko jeśli `offerCurrencyName` jeszcze nie ustawione)
                if ($price && !$offerCurrencyName) {
                    $offerCurrencyName = DB::table('currencies')->where('id', $price->currency_id)->value('name');
                }

                // Oblicz wartość total_price po rabacie
                $discount = $productData['discount']; // Rabat z formularza
                $quantity = $productData['quantity']; // Ilość produktu
                $totalPrice = $productPrice * $quantity * (1 - $discount / 100);

                $offerTotalPrice += $totalPrice;

                // Zapisz produkt z ceną do tabeli `offer_products`
                DB::table('offer_products')->insert([
                    'offer_id' => $offer->id,
                    'product_id' => $product->id,
                    'product_main_image' => $product->product_main_image ?: NULL,
                    'prod_ean' => $product->ean,
                    'prod_sku' => $product->sku,
                    'prod_name' => $product->name,
                    'prod_quantity' => $productData['quantity'],
                    'prod_price' => $productPrice,
                    'discount' => $discount,
                    'total_price' => $totalPrice,
                    'currency_name' => $currencyName, // Nazwa waluty (np. PLN, USD)
                    'country_name' => $countryName, // Nazwa kraju (np. Polska, Niemcy)
                    'price_type' => 'netto', // Typ ceny
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Dodaj dane do tablicy do wygenerowania PDF
                $selectedProducts[] = [
                    'product_main_image' => $productImagePath, // Lokalna ścieżka do pliku
                    'offer_name' => $offer->offer_name,
                    'name' => $product->name,
                    'ean' => $product->ean,
                    'sku' => $product->sku,
                    'quantity' => $productData['quantity'], // Ilość produktu
                    'price' => $productPrice, // Cena netto za sztukę
                    'discount' => $discount, // Rabat
                    'total_price' => $totalPrice, // Cena całkowita po rabacie
                    'currency' => $currencyName, // Nazwa waluty (np. PLN)
                    'country' => $countryName, // Nazwa kraju (np. Polska)
                    'price_type' => 'netto', // Typ ceny
                    'client_name' => $offer->client_name,
                    'client_street' => $offer->client_street,
                    'client_city' => $offer->client_city,
                    'client_post_code' => $offer->client_post_code,
                    'client_country' => $offer->client_country,
                    'description' => $offer->description,
                    'offer_lead_time' => $offer->offer_lead_time,
                    'created_at' => $offer->created_at,

                    'user_name' => $user->name,
                    'user_email' => $user->email,
                    'user_phone' => $user->phone,
                ];
            }
        }

        // aktualizacja waluty offer_currency_name
        $offer->update([
            'offer_total_price' => $offerTotalPrice,
            'offer_currency_name' => $offerCurrencyName,
        ]);
        
            
        $offerId = $offer->id;
        $currentYear = now()->year;
        $pdfFileName = $offerId . '-OFH-' . $currentYear . '.pdf';
    
        // Wygeneruj PDF w pamięci
        $pdf = Pdf::loadView('offers.pdf', compact('offer', 'selectedProducts', 'user'))
            ->setPaper('a4', 'portrait')
            ->setOption('defaultFont', 'DejaVu Sans');
    
        // Pobierz aktualnego użytkownika (tworzącego ofertę)
        $user = Auth::user();
    
        // Wyślij e-mail z załącznikiem PDF
        Mail::to($user->email)->send(new OfferCreated($offer, $selectedProducts, $pdf->output(), $pdfFileName));
    
        // Przekieruj z komunikatem sukcesu
        return redirect()->route('offers.index')->with('status', 'Oferta ' . $offer->offer_name . ' została pomyślnie utworzona i wysłana na Twój adres e-mail.');
    }
    
    

    /**
     * Display the specified resource.
     */
    public function show(Offer $offer): View
    {
        $countries = Country::all();
        $offerProducts = DB::table('offer_products')->where('offer_id', $offer->id)->get();

        return view('offers.show', compact('offer', 'countries', 'offerProducts'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Offer $offer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Offer $offer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Offer $offer)
    {
        //
    }
}

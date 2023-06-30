<?php

namespace App\Http\Controllers\Front;

use Auth;
use Carbon\Carbon;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\Language;
use App\Models\Pcategory;
use App\Models\TimeFrame;
use App\Models\PostalCode;
use Illuminate\Http\Request;
use App\Models\BasicExtended;
use App\Models\ProductReview;
use App\Models\ServingMethod;
use App\Models\OfflineGateway;
use App\Models\PaymentGateway;
use App\Models\ShippingCharge;
use App\Models\BasicSetting as BS;
use App\Models\BasicExtended as BE;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class ProductController extends Controller
{

    public function __construct()
    {
        $this->middleware('setlang');
    }

    public function product(Request $request)
    {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }
        $data['currentLang'] = $currentLang;

        $lang_id = $currentLang->id;

        $data['categories'] = Pcategory::where('status', 1)->where('language_id', $currentLang->id)->get();

        $data['products'] = Product::where('language_id', $lang_id)->where('status', 1)->paginate(10);

        return view('front.product.product', $data);
    }

    public function productDetails($slug, $id)
    {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        Session::put('link', route('front.product.details', ['slug' => $slug, 'id' => $id]));

        $data['product'] = Product::where('id', $id)->where('language_id', $currentLang->id)->first();
        $data['categories'] = Pcategory::where('status', 1)->where('language_id', $currentLang->id)->get();
        $data['reviews'] = ProductReview::where('product_id', $id)->get();

        $data['related_product'] = Product::where('category_id', $data['product']->category_id)->where('language_id', $currentLang->id)->where('id', '!=', $data['product']->id)->get();

        return view('front.product.details', $data);
    }

    public function items(Request $request)
    {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }
        $data['currentLang'] = $currentLang;
        $lang_id = $currentLang->id;

        $data['products'] = Product::where('status', 1)->where('language_id', $currentLang->id)->paginate(6);
        $data['categories'] = Pcategory::where('status', 1)->where('language_id', $currentLang->id)->get();

        $search = $request->search;
        $minprice = $request->minprice;
        $maxprice = $request->maxprice;
        $category = $request->category_id;
        $subcategory = $request->subcategory_id;

        if ($request->type) {
            $type = $request->type;
        } else {
            $type = 'new';
        }


        $review = $request->review;

        $data['products'] =
            Product::when($category, function ($query, $category) {
                return $query->where('category_id', $category);
            })
            ->when($subcategory, function ($query, $subcategory) {
                return $query->where('subcategory_id', $subcategory);
            })
            ->when($lang_id, function ($query, $lang_id) {
                return $query->where('language_id', $lang_id);
            })
            ->when($search, function ($query, $search) {
                return $query->where('title', 'like', '%' . $search . '%')->orwhere('summary', 'like', '%' . $search . '%')->orwhere('description', 'like', '%' . $search . '%');
            })
            ->when($minprice, function ($query, $minprice) {
                return $query->where('current_price', '>=', $minprice);
            })
            ->when($maxprice, function ($query, $maxprice) {
                return $query->where('current_price', '<=', $maxprice);
            })

            ->when($review, function ($query, $review) {
                return $query->where('rating', '>=', $review);
            })

            ->when($type, function ($query, $type) {
                if ($type == 'new') {
                    return $query->orderBy('id', 'DESC');
                } elseif ($type == 'old') {
                    return $query->orderBy('id', 'ASC');
                } elseif ($type == 'high-to-low') {
                    return $query->orderBy('current_price', 'DESC');
                } elseif ($type == 'low-to-high') {
                    return $query->orderBy('current_price', 'ASC');
                }
            })

            ->where('status', 1)->paginate(9);

        return view('front.product.items', $data);
    }

    public function cart()
    {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        if (Session::has('cart')) {
            $cart = Session::get('cart');
        } else {
            $cart = null;
        }
        return view('front.product.cart', compact('cart'));
    }

    public function addToCart($id)
    {

        $cart = Session::get('cart');
        
        $data = explode(',,,', $id);
        $id = (int)$data[0];
        $qty = (int)$data[1];
        $total = (float)$data[2];
        $variant = json_decode($data[3], true);
        $addons = json_decode($data[4], true);

        $product = Product::findOrFail($id);
        // validations
        if ($qty < 1) {
            return response()->json(['error' => 'Quanty must be 1 or more than 1.']);
        }
        $pvariant = json_decode($product->variations, true);
        if (!empty($pvariant) && empty($variant)) {
            return response()->json(['error' => 'You must select a variant.']);
        }


        if (!$product) {
            abort(404);
        }
        $cart = Session::get('cart');
        $ckey = uniqid();

        // if cart is empty then this the first product
        if (!$cart) {

            $cart = [
                $ckey => [
                    "id" => $id,
                    "name" => $product->title,
                    "qty" => (int)$qty,
                    "variations" => $variant,
                    "addons" => $addons,
                    "product_price" => (float)$product->current_price,
                    "total" => $total,
                    "photo" => $product->feature_image
                ]
            ];

            Session::put('cart', $cart);
            return response()->json(['message' => 'Product added to cart successfully!']);
        }

        // if cart not empty then check if this product (with same variation) exist then increment quantity
        foreach ($cart as $key => $cartItem) {
            if ($cartItem["id"] == $id && $variant == $cartItem["variations"] && $addons == $cartItem["addons"]) {
                $cart[$key]['qty'] = (int)$cart[$key]['qty'] + $qty;
                $cart[$key]['total'] = (float)$cart[$key]['total'] + $total;
                Session::put('cart', $cart);
                return response()->json(['message' => 'Product added to cart successfully!']);
            }
        }

        // if item not exist in cart then add to cart with quantity = 1
        $cart[$ckey] = [
            "id" => $id,
            "name" => $product->title,
            "qty" => (int)$qty,
            "variations" => $variant,
            "addons" => $addons,
            "product_price" => (float)$product->current_price,
            "total" => $total,
            "photo" => $product->feature_image
        ];


        Session::put('cart', $cart);


        return response()->json(['message' => 'Product added to cart successfully!']);
    }


    public function updatecart(Request $request)
    {
        $cart = Session::get('cart');
        $qtys = $request->qty;
        $i = 0;


        foreach ($cart as $cartKey => $cartItem) {
            $total = 0;
            $cart[$cartKey]["qty"] = (int)$qtys[$i];

            // calculate total
            $addons = $cartItem["addons"];
            if (is_array($addons)) {
                foreach ($addons as $key => $addon) {
                    $total += (float)$addon["price"];
                }
            }
            $variations = $cartItem["variations"];
            if (is_array($variations)) {
                foreach ($variations as $key => $variation) {
                    $total += (float)$variation["price"];
                }
            }
            
            $total += (float)$cartItem["product_price"];
            $total = $total * $qtys[$i];

            // save total in the cart item
            $cart[$cartKey]["total"] = $total;

            $i++;
        }

        Session::put('cart', $cart);

        return response()->json(['message' => 'Cart Update Successfully.']);
    }


    public function cartitemremove($id)
    {
        if ($id) {
            $cart = Session::get('cart');
            unset($cart[$id]);
            Session::put('cart', $cart);

            return response()->json(['message' => 'Item removed successfully']);
        }
    }


    public function checkout(Request $request)
    {
        if ($request->type != 'guest' && !Auth::check()) {
            Session::put('link', route('front.checkout'));
            return redirect(route('user.login', ['redirected' => 'checkout']));
        }

        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        if (Session::has('cart')) {
            $data['cart'] = Session::get('cart');
        } else {
            $data['cart'] = null;
        }
        $data['shippings'] = ShippingCharge::where('language_id', $currentLang->id)->get();
        $data['postcodes'] = PostalCode::where('language_id', $currentLang->id)->orderBy('serial_number', 'ASC')->get();
        $data['ogateways'] = OfflineGateway::where('status', 1)->orderBy('serial_number', 'ASC')->get();
        $data['stripe'] = PaymentGateway::find(14);
        $data['paypal'] = PaymentGateway::find(15);
        $data['paystackData'] = PaymentGateway::whereKeyword('paystack')->first();
        $data['paystack'] = $data['paystackData']->convertAutoData();
        $data['flutterwave'] = PaymentGateway::find(6);
        $data['razorpay'] = PaymentGateway::find(9);
        $data['instamojo'] = PaymentGateway::find(13);
        $data['paytm'] = PaymentGateway::find(11);
        $data['mollie'] = PaymentGateway::find(17);
        $data['mercadopago'] = PaymentGateway::find(19);
        $data['payumoney'] = PaymentGateway::find(18);

        $data['scharges'] = $currentLang->shippings;
        $data['smethods'] = ServingMethod::where('website_menu', 1)->orderBy('serial_number', 'ASC')->get();

        $data['discount'] = session()->has('coupon') && !empty(session()->get('coupon')) ? session()->get('coupon') : 0;

        $days = ['sunday','monday','tuesday','wednesday','thursday','friday','saturday'];
        $disDays = [];
        foreach ($days as $key => $day) {
            $count = TimeFrame::where('day', $day)->count();
            if ($count == 0) {
                if ($day == 'sunday') {
                    $disDays[] = 0;
                } elseif ($day == 'monday') {
                    $disDays[] = 1;
                } elseif ($day == 'tuesday') {
                    $disDays[] = 2;
                } elseif ($day == 'wednesday') {
                    $disDays[] = 3;
                } elseif ($day == 'thursday') {
                    $disDays[] = 4;
                } elseif ($day == 'friday') {
                    $disDays[] = 5;
                } elseif ($day == 'saturday') {
                    $disDays[] = 6;
                }
            }
        }
        $data['disDays'] = $disDays;

        $data['ccodes'] = [["code" => "+7840","name" => "Abkhazia"],["code" => "+93","name" => "Afghanistan"],["code" => "+355","name" => "Albania"],["code" => "+213","name" => "Algeria"],["code" => "+1684","name" => "American Samoa"],["code" => "+376","name" => "Andorra"],["code" => "+244","name" => "Angola"],["code" => "+1264","name" => "Anguilla"],["code" => "+1268","name" => "Antigua and Barbuda"],["code" => "+54","name" => "Argentina"],["code" => "+374","name" => "Armenia"],["code" => "+297","name" => "Aruba"],["code" => "+247","name" => "Ascension"],["code" => "+61","name" => "Australia"],["code" => "+672","name" => "Australian External Territories"],["code" => "+43","name" => "Austria"],["code" => "+994","name" => "Azerbaijan"],["code" => "+1242","name" => "Bahamas"],["code" => "+973","name" => "Bahrain"],["code" => "+880","name" => "Bangladesh"],["code" => "+1246","name" => "Barbados"],["code" => "+1268","name" => "Barbuda"],["code" => "+375","name" => "Belarus"],["code" => "+32","name" => "Belgium"],["code" => "+501","name" => "Belize"],["code" => "+229","name" => "Benin"],["code" => "+1441","name" => "Bermuda"],["code" => "+975","name" => "Bhutan"],["code" => "+591","name" => "Bolivia"],["code" => "+387","name" => "Bosnia and Herzegovina"],["code" => "+267","name" => "Botswana"],["code" => "+55","name" => "Brazil"],["code" => "+246","name" => "British Indian Ocean Territory"],["code" => "+1284","name" => "British Virgin Islands"],["code" => "+673","name" => "Brunei"],["code" => "+359","name" => "Bulgaria"],["code" => "+226","name" => "Burkina Faso"],["code" => "+257","name" => "Burundi"],["code" => "+855","name" => "Cambodia"],["code" => "+237","name" => "Cameroon"],["code" => "+1","name" => "Canada"],["code" => "+238","name" => "Cape Verde"],["code" => "+345","name" => "Cayman Islands"],["code" => "+236","name" => "Central African Republic"],["code" => "+235","name" => "Chad"],["code" => "+56","name" => "Chile"],["code" => "+86","name" => "China"],["code" => "+61","name" => "Christmas Island"],["code" => "+61","name" => "Cocos-Keeling Islands"],["code" => "+57","name" => "Colombia"],["code" => "+269","name" => "Comoros"],["code" => "+242","name" => "Congo"],["code" => "+243","name" => "Congo, Dem. Rep. of (Zaire)"],["code" => "+682","name" => "Cook Islands"],["code" => "+506","name" => "Costa Rica"],["code" => "+385","name" => "Croatia"],["code" => "+53","name" => "Cuba"],["code" => "+599","name" => "Curacao"],["code" => "+537","name" => "Cyprus"],["code" => "+420","name" => "Czech Republic"],["code" => "+45","name" => "Denmark"],["code" => "+246","name" => "Diego Garcia"],["code" => "+253","name" => "Djibouti"],["code" => "+1767","name" => "Dominica"],["code" => "+1809","name" => "Dominican Republic"],["code" => "+670","name" => "East Timor"],["code" => "+56","name" => "Easter Island"],["code" => "+593","name" => "Ecuador"],["code" => "+20","name" => "Egypt"],["code" => "+503","name" => "El Salvador"],["code" => "+240","name" => "Equatorial Guinea"],["code" => "+291","name" => "Eritrea"],["code" => "+372","name" => "Estonia"],["code" => "+251","name" => "Ethiopia"],["code" => "+500","name" => "Falkland Islands"],["code" => "+298","name" => "Faroe Islands"],["code" => "+679","name" => "Fiji"],["code" => "+358","name" => "Finland"],["code" => "+33","name" => "France"],["code" => "+596","name" => "French Antilles"],["code" => "+594","name" => "French Guiana"],["code" => "+689","name" => "French Polynesia"],["code" => "+241","name" => "Gabon"],["code" => "+220","name" => "Gambia"],["code" => "+995","name" => "Georgia"],["code" => "+49","name" => "Germany"],["code" => "+233","name" => "Ghana"],["code" => "+350","name" => "Gibraltar"],["code" => "+30","name" => "Greece"],["code" => "+299","name" => "Greenland"],["code" => "+1473","name" => "Grenada"],["code" => "+590","name" => "Guadeloupe"],["code" => "+1671","name" => "Guam"],["code" => "+502","name" => "Guatemala"],["code" => "+224","name" => "Guinea"],["code" => "+245","name" => "Guinea-Bissau"],["code" => "+595","name" => "Guyana"],["code" => "+509","name" => "Haiti"],["code" => "+504","name" => "Honduras"],["code" => "+852","name" => "Hong Kong SAR China"],["code" => "+36","name" => "Hungary"],["code" => "+354","name" => "Iceland"],["code" => "+91","name" => "India"],["code" => "+62","name" => "Indonesia"],["code" => "+98","name" => "Iran"],["code" => "+964","name" => "Iraq"],["code" => "+353","name" => "Ireland"],["code" => "+972","name" => "Israel"],["code" => "+39","name" => "Italy"],["code" => "+225","name" => "Ivory Coast"],["code" => "+1876","name" => "Jamaica"],["code" => "+81","name" => "Japan"],["code" => "+962","name" => "Jordan"],["code" => "+77","name" => "Kazakhstan"],["code" => "+254","name" => "Kenya"],["code" => "+686","name" => "Kiribati"],["code" => "+965","name" => "Kuwait"],["code" => "+996","name" => "Kyrgyzstan"],["code" => "+856","name" => "Laos"],["code" => "+371","name" => "Latvia"],["code" => "+961","name" => "Lebanon"],["code" => "+266","name" => "Lesotho"],["code" => "+231","name" => "Liberia"],["code" => "+218","name" => "Libya"],["code" => "+423","name" => "Liechtenstein"],["code" => "+370","name" => "Lithuania"],["code" => "+352","name" => "Luxembourg"],["code" => "+853","name" => "Macau SAR China"],["code" => "+389","name" => "Macedonia"],["code" => "+261","name" => "Madagascar"],["code" => "+265","name" => "Malawi"],["code" => "+60","name" => "Malaysia"],["code" => "+960","name" => "Maldives"],["code" => "+223","name" => "Mali"],["code" => "+356","name" => "Malta"],["code" => "+692","name" => "Marshall Islands"],["code" => "+596","name" => "Martinique"],["code" => "+222","name" => "Mauritania"],["code" => "+230","name" => "Mauritius"],["code" => "+262","name" => "Mayotte"],["code" => "+52","name" => "Mexico"],["code" => "+691","name" => "Micronesia"],["code" => "+1808","name" => "Midway Island"],["code" => "+373","name" => "Moldova"],["code" => "+377","name" => "Monaco"],["code" => "+976","name" => "Mongolia"],["code" => "+382","name" => "Montenegro"],["code" => "+1664","name" => "Montserrat"],["code" => "+212","name" => "Morocco"],["code" => "+95","name" => "Myanmar"],["code" => "+264","name" => "Namibia"],["code" => "+674","name" => "Nauru"],["code" => "+977","name" => "Nepal"],["code" => "+31","name" => "Netherlands"],["code" => "+599","name" => "Netherlands Antilles"],["code" => "+1869","name" => "Nevis"],["code" => "+687","name" => "New Caledonia"],["code" => "+64","name" => "New Zealand"],["code" => "+505","name" => "Nicaragua"],["code" => "+227","name" => "Niger"],["code" => "+234","name" => "Nigeria"],["code" => "+683","name" => "Niue"],["code" => "+672","name" => "Norfolk Island"],["code" => "+850","name" => "North Korea"],["code" => "+1670","name" => "Northern Mariana Islands"],["code" => "+47","name" => "Norway"],["code" => "+968","name" => "Oman"],["code" => "+92","name" => "Pakistan"],["code" => "+680","name" => "Palau"],["code" => "+970","name" => "Palestinian Territory"],["code" => "+507","name" => "Panama"],["code" => "+675","name" => "Papua New Guinea"],["code" => "+595","name" => "Paraguay"],["code" => "+51","name" => "Peru"],["code" => "+63","name" => "Philippines"],["code" => "+48","name" => "Poland"],["code" => "+351","name" => "Portugal"],["code" => "+1787","name" => "Puerto Rico"],["code" => "+974","name" => "Qatar"],["code" => "+262","name" => "Reunion"],["code" => "+40","name" => "Romania"],["code" => "+7","name" => "Russia"],["code" => "+250","name" => "Rwanda"],["code" => "+685","name" => "Samoa"],["code" => "+378","name" => "San Marino"],["code" => "+966","name" => "Saudi Arabia"],["code" => "+221","name" => "Senegal"],["code" => "+381","name" => "Serbia"],["code" => "+248","name" => "Seychelles"],["code" => "+232","name" => "Sierra Leone"],["code" => "+65","name" => "Singapore"],["code" => "+421","name" => "Slovakia"],["code" => "+386","name" => "Slovenia"],["code" => "+677","name" => "Solomon Islands"],["code" => "+27","name" => "South Africa"],["code" => "+500","name" => "South Georgia and the South Sandwich Islands"],["code" => "+82","name" => "South Korea"],["code" => "+34","name" => "Spain"],["code" => "+94","name" => "Sri Lanka"],["code" => "+249","name" => "Sudan"],["code" => "+597","name" => "Suriname"],["code" => "+268","name" => "Swaziland"],["code" => "+46","name" => "Sweden"],["code" => "+41","name" => "Switzerland"],["code" => "+963","name" => "Syria"],["code" => "+886","name" => "Taiwan"],["code" => "+992","name" => "Tajikistan"],["code" => "+255","name" => "Tanzania"],["code" => "+66","name" => "Thailand"],["code" => "+670","name" => "Timor Leste"],["code" => "+228","name" => "Togo"],["code" => "+690","name" => "Tokelau"],["code" => "+676","name" => "Tonga"],["code" => "+1868","name" => "Trinidad and Tobago"],["code" => "+216","name" => "Tunisia"],["code" => "+90","name" => "Turkey"],["code" => "+993","name" => "Turkmenistan"],["code" => "+1649","name" => "Turks and Caicos Islands"],["code" => "+688","name" => "Tuvalu"],["code" => "+1340","name" => "U.S. Virgin Islands"],["code" => "+256","name" => "Uganda"],["code" => "+380","name" => "Ukraine"],["code" => "+971","name" => "United Arab Emirates"],["code" => "+44","name" => "United Kingdom"],["code" => "+1","name" => "United States"],["code" => "+598","name" => "Uruguay"],["code" => "+998","name" => "Uzbekistan"],["code" => "+678","name" => "Vanuatu"],["code" => "+58","name" => "Venezuela"],["code" => "+84","name" => "Vietnam"],["code" => "+1808","name" => "Wake Island"],["code" => "+681","name" => "Wallis and Futuna"],["code" => "+967","name" => "Yemen"],["code" => "+260","name" => "Zambia"],["code" => "+255","name" => "Zanzibar"],["code" => "+263","name" => "Zimbabwe"]];


        return view('front.product.checkout', $data);
    }


    public function Prdouctcheckout(Request $request, $slug)
    {
        $product = Product::where('slug', $slug)->first();

        if (!$product) {
            abort(404);
        }

        if ($request->qty) {
            $qty = $request->qty;
        } else {
            $qty = 1;
        }


        $cart = Session::get('cart');
        $id = $product->id;
        // if cart is empty then this the first product
        if (!($cart)) {
            if ($product->stock <  $qty) {
                Session::flash('error', 'Out of stock');
                return back();
            }
            $cart = [
                $id => [
                    "name" => $product->title,
                    "qty" => $qty,
                    "price" => $product->current_price,
                    "photo" => $product->feature_image
                ]
            ];

            Session::put('cart', $cart);

            return redirect(route('front.checkout'));
        }

        // if cart not empty then check if this product exist then increment quantity
        if (isset($cart[$id])) {

            if ($product->stock < $cart[$id]['qty'] + $qty) {
                Session::flash('error', 'Out of stock');
                return back();
            }
            $qt = $cart[$id]['qty'];
            $cart[$id]['qty'] = $qt + $qty;

            Session::put('cart', $cart);

            return redirect(route('front.checkout'));
        }

        if ($product->stock <  $qty) {
            Session::flash('error', 'Out of stock');
            return back();
        }


        $cart[$id] = [
            "name" => $product->title,
            "qty" => $qty,
            "price" => $product->current_price,
            "photo" => $product->feature_image
        ];
        Session::put('cart', $cart);

        return redirect(route('front.checkout'));
    }


    public function coupon(Request $request) {
        $coupon = Coupon::where('code', $request->coupon);
        $be = BasicExtended::first();

        if ($coupon->count() == 0) {
            return response()->json(['status' => 'error', 'message' => "Coupon is not valid"]);
        } else {
            $coupon = $coupon->first();
            if (cartTotal() < $coupon->minimum_spend) {
                return response()->json(['status' => 'error', 'message' => "Cart Total must be minimum " . $coupon->minimum_spend . " " . $be->base_currency_text]);
            }
            $start = Carbon::parse($coupon->start_date);
            $end = Carbon::parse($coupon->end_date);
            $today = Carbon::now();
            // return response()->json($end->lessThan($today));

            // if coupon is active
            if ($today->greaterThanOrEqualTo($start) && $today->lessThan($end)) {
                $cartTotal = cartTotal();
                $value = $coupon->value;
                $type = $coupon->type;

                if ($type == 'fixed') {
                    if ($value > cartTotal()) {
                        return response()->json(['status' => 'error', 'message' => "Coupon discount is greater than cart total"]);
                    }
                    $couponAmount = $value;
                } else {
                    $couponAmount = ($cartTotal * $value) / 100;
                }
                session()->put('coupon', round($couponAmount, 2));

                return response()->json(['status' => 'success', 'message' => "Coupon applied successfully"]);
            } else {
                return response()->json(['status' => 'error', 'message' => "Coupon is not valid"]);
            }
        }
    }

    public function timeframes(Request $request) {
        $date = Carbon::parse($request->date);
        $day = strtolower($date->format('l'));

        $timeframes = TimeFrame::where('day', $day)->get();

        if (count($timeframes) > 0) {
            // if (condition) {
            //     # code...
            // }
            return response()->json(['status' => 'success', 'timeframes' => $timeframes]);
        } else {
            return response()->json(['status' => 'error', 'message' => 'No delivery time frame is available on '.ucfirst($day) ]);
        }
    }
}

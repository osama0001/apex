<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Auth;
use Session;
use Image;
use App\Category;
use App\Product;
use App\ProductsAttribute;
use App\ProductsImage;
use App\Coupon;
use App\User;
use App\Country;
use App\DeliveryAddress;
use App\Order;
use App\OrdersProduct;
use DB;



class ProductsController extends Controller
{
    
    public function addProduct(Request $request){

    	if($request->isMethod('post')){
    		$data = $request->all();

    		if(empty($data['category_id'])){
    			return redirect()->back()->with('flash_message_error','Category is missing');
    		}

    		$product = new Product;
    		$product->category_id    = $data['category_id'];
    		$product->product_name   = $data['product_name'];
    		$product->product_code   = $data['product_code'];
    		$product->product_color  = $data['product_color'];

    		if(!empty($data['description'])){
    			$product->description    = $data['description'];
    		}else{
    			$product->description    = '';
    		}

            if(!empty($data['care'])){
                $product->care    = $data['care'];
            }else{
                $product->care    = '';
            }
    		
    		$product->price          = $data['price'];

    		// upload image
    		if($request->hasFile('image')){
    			$image_tmp = Input::file('image');
    			if($image_tmp->isValid()){
    				
    				$extension = $image_tmp->getClientOriginalExtension();
    				$filename = rand(111,99999).'.'.$extension;
    				$large_image_path = 'images/backend_images/products/large/'.$filename;
    				$medium_image_path = 'images/backend_images/products/medium/'.$filename;
    				$small_image_path = 'images/backend_images/products/small/'.$filename;
    				//Resize image
    				Image::make($image_tmp)->save($large_image_path);
    				Image::make($image_tmp)->resize(600,600)->save($medium_image_path);
    				Image::make($image_tmp)->resize(300,300)->save($small_image_path);
    				// store image name in products table

    				$product->image = $filename;
    			}
    		}

            if(empty($data['status'])){
                $status = 0;
            }else{
                $status = 1;
            }

            $product->status = $status;
    		$product->save();

    		return redirect('/admin/view-products')->with('flash_message_success','product has been added successfully!');
    	}

    	//Categories dropdown start
    	$categories = Category::where(['parent_id'=>0])->get();
    	$categories_dropdown = "<option selected disabled>Select</option> ";
    	foreach($categories as $cat){
    		$categories_dropdown .="<option value='" .$cat->id. "'>" .$cat->name."</option> ";
    		$sub_categories = Category::where(['parent_id'=>$cat->id])->get();
    		foreach ($sub_categories as $sub_cat) {
    			$categories_dropdown .= "<option value = '".$sub_cat->id ."'>&nbsp;-&nbsp;" .$sub_cat->name."</option>";
    		}
    	}

    	//Categories dropdown ends

    	return view('admin.products.add_product')->with(compact('categories_dropdown'));
    }

    public function editProduct(Request $request, $id = null){
    	if($request->isMethod('post')){
    		$data = $request->all();
    		// echo "<pre>"; print_r($data); die;

    		// upload image
    		if($request->hasFile('image')){
    			$image_tmp = Input::file('image');
    			if($image_tmp->isValid()){
    				
    				$extension = $image_tmp->getClientOriginalExtension();
    				$filename = rand(111,99999).'.'.$extension;
    				$large_image_path = 'images/backend_images/products/large/'.$filename;
    				$medium_image_path = 'images/backend_images/products/medium/'.$filename;
    				$small_image_path = 'images/backend_images/products/small/'.$filename;
    				//Resize image
    				Image::make($image_tmp)->save($large_image_path);
    				Image::make($image_tmp)->resize(600,600)->save($medium_image_path);
    				Image::make($image_tmp)->resize(300,300)->save($small_image_path);
    				
    			}
    		}else if(!empty($data['current_image'])){
                $filename = $data['current_image'];

            }else{
                $filename = '';
                
            }

    		if(empty($data['description'])){
    			$data['description'] = "";
    		}

            if(empty($data['care'])){
                $data['care'] = "";
            }

            if(empty($data['status'])){
                $status = 0;
            }else{
                $status = 1;
            }

    		product::where(['id'=>$id])->update(['category_id'=>$data['category_id'],'product_name'=>$data['product_name'],'product_code'=>$data['product_code'],'product_color'=>$data['product_color'],'description'=>$data['description'],'care'=>$data['care'],'price'=>$data['price'],'image'=>$filename,'status'=>$status]);
    		return redirect()->back()->with('flash_message_success','Product has been updated successfully!');
    	}
    	//Get Product details
    	$productDetails = Product::where(['id'=>$id])->first();

    	//Categories dropdown start
    	$categories = Category::where(['parent_id'=>0])->get();
    	$categories_dropdown = "<option value='' selected disabled>Select</option> ";
    	foreach($categories as $cat){
    		if($cat->id==$productDetails->category_id){
    			$selected = "selected";
    		}else{
    			$selected="";
    		}

    		$categories_dropdown .="<option value = '".$cat->id."' ".$selected.">".$cat->name."</option> ";
    		$sub_categories = Category::where(['parent_id'=>$cat->id])->get();
    		foreach ($sub_categories as $sub_cat) {
    			if($sub_cat->id==$productDetails->category_id){
    			$selected = "selected";
    		}else{
    			$selected="";
    		}
    			$categories_dropdown .= "<option value = '".$sub_cat->id ."'".$selected.">&nbsp;-&nbsp;" .$sub_cat->name."</option>";
    		}
    	}

    	//Categories dropdown ends

    	return view('admin.products.edit_product')->with(compact('productDetails','categories_dropdown'));
    }

    public function viewProducts(Request $request){
    	$products = Product::orderby('id','desc')->get();
        $products = json_decode(json_encode($products));
    	foreach ($products as $key => $val) {
    		$category_name = Category::where(['id'=>$val->category_id])->first();
    		$products[$key]->category_name = $category_name['name'];
    	}
    	return view('admin.products.view_products')->with(compact('products'));
    }

    public function deleteProduct($id = null){

    	$product = Product::findOrFail($id);

    	$image_path_first  = public_path().'/images/backend_images/products/large/'.$product->image;
    	$image_path_second = public_path().'/images/backend_images/products/medium/'.$product->image;
    	$image_path_third  = public_path().'/images/backend_images//products/small/'.$product->image;

    	$filename = array($image_path_first,$image_path_second,$image_path_third);

  		foreach ($filename as $file){
  			if(file_exists($file))
  				unlink($file);
  		}

    	$product->delete();

    	return redirect()->back()->with('flash_message_success','Product has been successfully deleted!');
    }

    public function deleteProductImage($id = null){

    	$product = Product::findOrFail($id);
        // Get product image path
    	$image_path_first  = public_path().'/images/backend_images/products/large/'.$product->image;
    	$image_path_second = public_path().'/images/backend_images/products/medium/'.$product->image;
    	$image_path_third  = public_path().'/images/backend_images//products/small/'.$product->image;

    	$filename = array($image_path_first,$image_path_second,$image_path_third);

  		foreach ($filename as $file){
  			if(file_exists($file))
  				unlink($file);
  		}

  	    Product::where(['id'=>$id])->update(['image'=>'']);
    	return redirect()->back()->with('flash_message_success','Product image deleted successfully!');
    }

    public function deleteAltImage($id = null){

        //Get product image name 
        $product = ProductsImage::where(['id'=>$id])->first();

        //Get product Image path
        $image_path_first  = public_path().'/images/backend_images/products/large/'.$product->image;
        $image_path_second = public_path().'/images/backend_images/products/medium/'.$product->image;
        $image_path_third  = public_path().'/images/backend_images//products/small/'.$product->image;

        $filename = array($image_path_first,$image_path_second,$image_path_third);

        foreach ($filename as $file){
            if(file_exists($file))
                unlink($file);
        }
        //Delete image from products table
        ProductsImage::where(['id'=>$id])->delete();
        return redirect()->back()->with('flash_message_success','Product Alternate image(s) deleted successfully!');
    }

    public function addAttributes(Request $request, $id = null){
    	$productDetails = Product::with('attributes')->where(['id'=>$id])->first();
    	// $productDetails = json_decode(json_encode($productDetails));
    	// echo "<pre>"; print_r($productDetails ); die;

    	if($request->isMethod('post')){
    		$data = $request->all();

    		foreach ($data['sku'] as $key => $val) {
    			if(!empty($val)){
                    // Prevent duplicate SKU check
                    $attrCountSKU = ProductsAttribute::where('sku',$val)->count();
                    if($attrCountSKU>0){
                        return redirect('admin/add-attributes/'.$id)->with('flash_message_error','SKU already exists! please add another SKU');
                    }

                    // Prevent duplcate Size check
                    $attrCountSizes = ProductsAttribute::where(['product_id'=>$id,'size'=>$data['size'][$key]])->count();
                    if($attrCountSizes>0){
                        return redirect('admin/add-attributes/'.$id)->with('flash_message_error','"'.$data['size'][$key].'" Size already exists! please add another Size');
                    }

    				$attribute = new ProductsAttribute;
    				$attribute->product_id = $id;
    				$attribute->sku  	   = $val;
    				$attribute->size 	   = $data['size'][$key];
    				$attribute->price 	   = $data['price'][$key];
    				$attribute->stock 	   = $data['stock'][$key];
    				$attribute->save();
    			}
    		}

    		return redirect('admin/add-attributes/'.$id)->with('flash_message_success','Product Attribute has been added successfully!');
    	}
    	return view('admin.products.add_attributes')->with(compact('productDetails'));
    }

    public function editAttributes(Request $request, $id = null){
        if($request->isMethod('post')){
            $data = $request->all();
            foreach($data['idAttr'] as $key => $attr){
                ProductsAttribute::where(['id'=>$data['idAttr'][$key]])->update(['price'=>$data['price'][$key],'stock'=>$data['stock'][$key]]);
            }
            return redirect()->back()->with('flash_message_success','Products Attributes has been updated successfully!');
        }
    }

    public function addImages(Request $request, $id = null){

        if($request->isMethod('post')){
            $data = $request->all();
            if($request->hasFile('image')){
                $files = $request->file('image');
                foreach($files as $file){
                    // Uplaod image after resize
                    $image              = new ProductsImage;
                    $extension          = $file->getClientOriginalExtension();
                    $filename           = rand(111,99999).'.'.$extension;
                    $large_image_path   = 'images/backend_images/products/large/'.$filename;
                    $medium_image_path  = 'images/backend_images/products/medium/'.$filename;
                    $small_image_path   = 'images/backend_images/products/small/'.$filename;
                    Image::make($file)->save($large_image_path);
                    Image::make($file)->resize(600,600)->save($medium_image_path);
                    Image::make($file)->resize(300,300)->save($small_image_path);
                    $image->image       = $filename;
                    $image->product_id  = $data['product_id'];
                    $image->save();
                }
                
            }
            return redirect('admin/add-images/'.$id)->with('flash_message_success','Product Images has been added successfully');
        }

        $productDetails = Product::with('attributes')->where(['id'=>$id])->first();

        $productsImages = ProductsImage::where(['product_id' =>$id])->get();

        return view('admin.products.add_images')->with(compact('productDetails','productsImages'));
    }

    public function deleteAttribute($id = null){
    	ProductsAttribute::where(['id'=>$id])->delete();
    	return redirect()->back()->with('flash_message_success','Attribute has been deleted successfully!');
    }

    public function products($url = null){
        // Show 404 page if category URL does not exxist  
        $countCategory = Category::where(['url' =>$url,'status'=>1])->count();
        if($countCategory==0){
            abort(404);
        }

        // Get all Categories and Sub Categories
        $categories = Category::with('categories')->where(['parent_id' => 0])->get();

        $categoryDetails = Category::where(['url' => $url])->first();

        if($categoryDetails->parent_id == 0){
             // If url is main category only
            $cat_ids[] = $categoryDetails->id;
            $subCategories = Category::where(['parent_id' => $categoryDetails->id])->get();
            foreach($subCategories as $subcat){
                $cat_ids[] = $subcat->id;
            }
             $productsAll = Product::whereIn('category_id',$cat_ids)->where('status',1)->get();
             
        }else{
            // If url is sub category only
            $productsAll = Product::where(['category_id' => $categoryDetails->id])->where('status',1)->get();
        }

        return view('products.listing')->with(compact('categories','categoryDetails','productsAll'));
    }

    public function searchProducts(Request $request){

        if($request->isMethod('post')){
            $data = $request->all();
            // echo "<pre>"; print_r($data); die;
            $categories = Category::with('categories')->where(['parent_id'=>0])->get();

            $search_product = $data['product'];

            $productsAll = Product::where('product_name','like','%'.$search_product.'%')->orwhere('product_code',$search_product)->where('status',1)->get();
            return view('products.listing')->with(compact('categories','search_product','productsAll'));
        }
    }

    public function product($id = null){
        // Show 404 page if product is disabled
        $productsCount = Product::where(['id'=>$id,'status'=>1])->count();
        if($productsCount == 0){
            abort(404);
        }

        // Get Product Details
        $productDetails = Product::with('attributes')->where('id',$id)->first();
        
        $relatedProducts = Product::where('id','!=',$id)->where(['category_id' =>$productDetails->category_id])->get();

        // foreach ($relatedProducts->chunk(3) as $chunk) {
        //     foreach($chunk as $item){
        //         echo $item; echo "<br>";
        //     }
        // }
        // Get all Categories and Sub Categories
        $categories = Category::with('categories')->where(['parent_id' => 0])->get();

        // Get product Alternate Images
        $productAltImages = ProductsImage::where('product_id',$id)->get();

        $total_stock = ProductsAttribute::where('product_id',$id)->sum('stock');

        return view('products.detail')->with(compact('productDetails','categories','productAltImages','total_stock','relatedProducts'));
    }

    public function getProductPrice(Request $request){
        $data = $request->all();
        $proArr = explode("-",$data['idSize']);
        $proAttr = ProductsAttribute::where(['product_id' => $proArr[0], 'size' => $proArr[1]])->first();
        echo $proAttr->price;
        echo "#";
        echo $proAttr->stock;
    }

    public function addtocart(request $request){
        Session::forget('CouponAmount');
        Session::forget('CouponCode');
        $data = $request->all();

        if(empty(Auth::user()->email)){
            $data['user_email'] = '';
        }else{
            $data['user_email'] = Auth::user()->email;
        }

        $session_id = Session::get('session_id');
        if(empty($session_id)){
            $session_id = str_random(40);
            Session::put('session_id',$session_id);
        }

        $sizeArr = explode("-",$data['size']);

        $countProducts = DB::table('cart')->where(['product_id'=>$data['product_id'],'product_color'=>$data['product_color'],'price'=>$data['price'],'size'=>$sizeArr[1],'session_id'=>$session_id])->count();

        if($countProducts>0){
            return redirect()->back()->with('flash_message_error','Product already exists in cart!');
        }else{

            $getSKU = ProductsAttribute::select('sku')->where(['product_id'=>$data['product_id'],'size'=>$sizeArr[1]])->first();

            DB::table('cart')->insert(['product_id'=>$data['product_id'],'product_name'=>$data['product_name'],'product_code'=>$getSKU->sku,'product_color'=>$data['product_color'],'price'=>$data['price'],'size'=>$sizeArr[1],'quantity'=>$data['quantity'],'user_email'=>$data['user_email'],'session_id'=>$session_id]);
        }

        
        return redirect('cart')->with('flash_message_success','Product has been added in cart!');
    }

    public function cart(){
        if(Auth::check()){
            $user_email = Auth::user()->email;
            $userCart = DB::table('cart')->where(['user_email'=>$user_email])->get();
        }else{
            $session_id = Session::get('session_id');
            $userCart = DB::table('cart')->where(['session_id'=>$session_id])->get();
        }

        

        foreach($userCart as $key => $product){
            $productDetails = Product::where('id',$product->product_id)->first();
            $userCart[$key]->image = $productDetails->image;
        }
        return view('products.cart')->with(compact('userCart'));
    }

    public function deleteCartProduct($id = null){
        Session::forget('CouponAmount');
        Session::forget('CouponCode');
        DB::table('cart')->where('id',$id)->delete();
        return redirect('cart')->with('flash_message_success','Product has been deleted from cart!');
    }

    public function updateCartQuantity($id = null,$quantity = null){
        Session::forget('CouponAmount');
        Session::forget('CouponCode');
        $getCartDetails = DB::table('cart')->where('id',$id)->first();
        $getAttributeStock = ProductsAttribute::where('sku',$getCartDetails->product_code)->first();
        $updated_quantity = $getCartDetails->quantity+$quantity;
        if($getAttributeStock->stock >= $updated_quantity){
            DB::table('cart')->where('id',$id)->increment('quantity',$quantity);
            return redirect('cart')->with('flash_message_success','Product quantty has been updated successfully!');
        }
        return redirect ('cart')->with('flash_message_error','Required Product Quantity is not available!');
    }

    public function applyCoupon(Request $request){
        

        $data = $request->all();
        $couponCount = Coupon::where('coupon_code',$data['coupon_code'])->count();
        if($couponCount == 0){
            return redirect()->back()->with('flash_message_error','Coupon does not exists!');
        }else{
            // with perform other chest like Active/Inactive , Expiry date

            // Get Coupon details
            $couponDetails = Coupon::where('coupon_code',$data['coupon_code'])->first();

            // If coupon is Inactive
            if($couponDetails->status == 0){
                return redirect()->back()->with('flash_message_error','This coupon is not active!');
            }

            // If coupon is Expired
            $expiry_date  = $couponDetails->expiry_date;
            $current_date = date('Y-m-d');
            if($expiry_date < $current_date){
                return redirect()->back()->with('flash_message_error','This coupon is expired!');
            }
            //Coupon is Valide for discount

            // Get cart Total amount
            $session_id = Session::get('session_id');

            if(Auth::check()){
                $user_email = Auth::user()->email;
                $userCart = DB::table('cart')->where(['user_email' => $user_email])->get();
            }else{
                $session_id = Session::get('session_id');
                $userCart = DB::table('cart')->where(['session_id' => $session_id])->get();
            }

            $total_amount = 0;
            foreach($userCart as $item){
                $total_amount = $total_amount + ($item->price * $item->quantity);
            }

            //Check if amount type is fixed or percentage
            if($couponDetails->amount_type=="Fixed"){
                $couponAmount = $couponDetails->amount;
            }else{
                $couponAmount = $total_amount * ($couponDetails->amount/100);
            }

            // Add Coupon Code and Amount in Session
            Session::put('CouponAmount',$couponAmount);
            Session::put('CouponCode',$data['coupon_code']);

            return redirect()->back()->with('flash_message_success','Coupon code successfully applied. you are availing discount!');
        }
    }

    public function checkout(Request $request){
        $user_id = Auth::user()->id;
        $user_email = Auth::user()->email;
        $userDetails = User::find($user_id);
        $countries = Country::get();

        //Check if shipping Address exists
        $shippingCount = DeliveryAddress::where('user_id',$user_id)->count();
        $shippingDetails = array();
        if($shippingCount>0){
            $shippingDetails = DeliveryAddress::where('user_id',$user_id)->first();
        }
        // Update cart table with user email
        $session_id = Session::get('session_id');
        DB::table('cart')->where(['session_id'=>$session_id])->update(['user_email'=>$user_email]);


        if($request->isMethod('post')){
            $data = $request->all();

            //Return to checkout page if any field is missing
            if(empty($data['billing_name']) || empty($data['billing_address']) || empty($data['billing_city']) || empty($data['billing_state']) || empty($data['billing_country']) || empty($data['billing_pincode']) || empty($data['billing_mobile']) || empty($data['shipping_name']) || empty($data['shipping_address']) || empty($data['shipping_city']) || empty($data['shipping_state']) || empty($data['shipping_country']) || empty($data['shipping_pincode']) || empty($data['shipping_mobile'])){

                return redirect()->back()->with('flash_message_error','Please fill all fields to checkout');
            }

            // Update user details
            User::where('id',$user_id)->update(['name'=>$data['billing_name'],'address'=>$data['billing_address'],'city'=>$data['billing_city'],'state'=>$data['billing_state'],'pincode'=>$data['billing_pincode'],'country'=>$data['billing_country'],'mobile'=>$data['billing_mobile']]);

            if($shippingCount>0){
                // Update shipping Address
                DeliveryAddress::where('user_id',$user_id)->update(['name'=>$data['shipping_name'],'address'=>$data['shipping_address'],'city'=>$data['shipping_city'],'state'=>$data['shipping_state'],'pincode'=>$data['shipping_pincode'],'country'=>$data['shipping_country'],'mobile'=>$data['shipping_mobile']]);
            }else{
                // Add New Shipping Address
                $shipping = new DeliveryAddress;
                $shipping->user_id = $user_id;
                $shipping->user_email = $user_email;
                $shipping->name = $data['shipping_name'];
                $shipping->address = $data['shipping_address'];
                $shipping->city = $data['shipping_city'];
                $shipping->state = $data['shipping_state'];
                $shipping->pincode = $data['shipping_pincode'];
                $shipping->country = $data['shipping_country'];
                $shipping->mobile = $data['shipping_mobile'];
                $shipping->save();
            }

            return redirect()->action('ProductsController@orderReview');
        }
        return view('products.checkout')->with(compact('userDetails','countries','shippingDetails'));
    }

    public function orderReview(){
        $user_id = Auth::user()->id;
        $user_email = Auth::user()->email;
        $userDetails = User::where('id',$user_id)->first();
        $shippingDetails = DeliveryAddress::where('user_id',$user_id)->first();

        $userCart = DB::table('cart')->where(['user_email'=>$user_email])->get();
        foreach($userCart as $key => $product){
            $productDetails = Product::where('id',$product->product_id)->first();
            $userCart[$key]->image = $productDetails->image;
        }

        return view('products.order_review')->with(compact('userDetails','shippingDetails','userCart'));
    }

    public function placeOrder(Request $request){
        if($request->isMethod('post')){
            $data = $request->all();
            $user_id = Auth::user()->id;
            $user_email = Auth::user()->email;

            // Get Shipping Address of User
            $shippingDetails = DeliveryAddress::where(['user_email' => $user_email])->first();

            if(empty(Session::get('CouponCode'))){
                $coupon_code = '';
            }else{
                $coupon_code = Session::get('CouponCode');
            }


            if(empty(Session::get('CouponAmount'))){
                $coupon_amount = '';
            }else{
                $coupon_amount = Session::get('CouponAmount');
            }

            $order = new Order;
            $order->user_id        = $user_id;
            $order->user_email     = $user_email;
            $order->name           = $shippingDetails->name;
            $order->address        = $shippingDetails->address;
            $order->city           = $shippingDetails->city;
            $order->state          = $shippingDetails->state;
            $order->pincode        = $shippingDetails->pincode;
            $order->country        = $shippingDetails->country;
            $order->mobile         = $shippingDetails->mobile;
            $order->coupon_code    = $coupon_code;
            $order->coupon_amount  = $coupon_amount;
            $order->order_status   = "New";
            $order->payment_method = $data['payment_method'];
            $order->grand_total    = $data['grand_total'];
            $order->save();

            $order_id = DB::getPdo()->lastInsertId();

            $cartProducts = DB::table('cart')->where(['user_email' =>$user_email])->get();
            foreach($cartProducts as $pro){
                $cartPro = new OrdersProduct;
                $cartPro->order_id   = $order_id;
                $cartPro->user_id    = $user_id;
                $cartPro->product_id = $pro->product_id;
                $cartPro->product_code  = $pro->product_code;
                $cartPro->product_name  = $pro->product_name;
                $cartPro->product_color = $pro->product_color;
                $cartPro->product_size  = $pro->size;
                $cartPro->product_price = $pro->price;
                $cartPro->product_qty   = $pro->quantity;
                $cartPro->save();
            }

            Session::put('order_id',$order_id);
            Session::put('grand_total',$data['grand_total']);

            if($data['payment_method'] == "COD"){

                $productDetails = Order::with('orders')->where('id',$order_id)->first();
                $productDetails = json_decode(json_encode($productDetails),true);

                $userDetails = User::where('id',$user_id)->first();
                $productDetails = json_decode(json_encode($productDetails),true);
                /* Code for Order Email starts */
                $email = $user_email;
                $messageData = [
                    'email' => $email,
                    'name' => $shippingDetails->name,
                    'order_id' => $order_id,
                    'productDetails' => $productDetails,
                    'userDetails' => $userDetails
                ];
                Mail::send('emails.order',$messageData,function($message) use($email){
                    $message->to($email)->subject('Order Placed - E-com website');
                });




                /* Code for order email ends */

                // COD - Redirect user to thanks page after saving order
                return redirect('/thanks');
            }else{
                return redirect('/paypal');
            }
            
        }

    }

    public function thanks(Request $request){
        $user_email = Auth::user()->email;
        DB::table('cart')->where('user_email',$user_email)->delete();
        return view('orders.thanks');
    }

    public function thanksPaypal(){
        return view('orders.thanks_papal');
    }

    public function paypal(Request $request){
        $user_email = Auth::user()->email;
        DB::table('cart')->where('user_email',$user_email)->delete();
        return view('orders.paypal');
    }

    public function userOrders(){
        $user_id = Auth::user()->id;
        $orders = Order::with('orders')->where('user_id',$user_id)->orderBy('id','DESC')->get();
        return view('orders.user_orders')->with(compact('orders'));
    }

    public function userOrderDetails($order_id){
        $user_id = Auth::user()->id;
        $orderDetails = Order::with('orders')->where('id',$order_id)->first();

        return view('orders.user_order_details')->with(compact('orderDetails'));
    }

    public function viewOrders(){
        $orders = Order::with('orders')->orderBy('id','Desc')->get();
        return view('admin.orders.view_orders')->with(compact('orders'));
    }

    public function viewOrderDetails($order_id){
        $orderDetails = Order::with('orders')->where('id',$order_id)->first();
        $user_id = $orderDetails->user_id;
        $userDetails = User::where('id',$user_id)->first();
        // $orderDetails = json_decode(json_encode($orderDetails));
        // echo "<pre>"; print_r($orderDetails); die;
        return view('admin.orders.order_details')->with(compact('orderDetails','userDetails'));
    }

    public function updateOrderStatus(Request $request){
        if($request->isMethod('post')){
            $data = $request->all();
            Order::where('id',$data['order_id'])->update(['order_status' => $data['order_status']]);
            return redirect()->back()->with('flash_message_success','Order Status has been updated successfully');
        }
    }

}



<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\Category;
use App\Banner;

class IndexController extends Controller
{
    public function Index(){

    	// In Ascending order (by default)
    	$productsAll = Product::get();

    	//In Decending order
    	$productsAll = Product::orderBy('id','DESC')->get();

    	//In Random order 
    	$productsAll = Product::inRandomOrder()->where('status',1)->get();

    	//Get all Categories and sub Categories
    	$categories = Category::with('categories')->where(['parent_id'=>0])->get();
    	// $categories = json_decode(json_encode($categories));
    	// echo "<pre>"; print_r($categories); die;
    	// $categories_menu = "";
    	// foreach ($categories as $cat) {
    	 //  $categories_menu .= "<div class='panel-heading'>
					// 				<h4 class='panel-title'>
					// 					<a data-toggle='collapse' data-parent='#accodrian' href='#".$cat->id."'>
					// 						<span class='badge pull-right'><i class='fa fa-plus'></i></span>
					// 						".$cat->name."
					// 					</a>
					// 				</h4>
					// 			</div>
					// 			<div id='".$cat->id."' class='panel-collapse collapse'>
					// 				<div class='panel-body'>
					// 					<ul>";
					// 					$sub_categories = Category::where(['parent_id'=>$cat->id])->get();
					// 		        	foreach ($sub_categories as $subcat) {
					// 		        		$categories_menu .= "<li><a href='".$subcat->url."'>".$subcat->name." </a></li>";
					// 		        	}

					// 					$categories_menu .= "</ul>
					// 				</div>
					// 			</div>
					// 			";
    	// }

    	$banners = Banner::where('status','1')->get();

    	return view('index')->with(compact('productsAll','categories','banners'));
    }
}

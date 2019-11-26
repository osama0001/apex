<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Image;
use Illuminate\Support\Facades\Input;
use App\Banner;

class BannersController extends Controller
{

    public function addBanner(Request $request){
    	if($request->isMethod('post')){
	    	$data = $request->all();
	    	$banner = new Banner;
	    	$banner->title    = $data['title'];
			$banner->link     = $data['link'];

			// upload image
			if($request->hasFile('image')){
				$image_tmp = Input::file('image');
				if($image_tmp->isValid()){
					
					$extension = $image_tmp->getClientOriginalExtension();
					$filename = rand(111,99999).'.'.$extension;
					$banner_path = 'images/frontend_images/banners/'.$filename;
					//Resize image 
					Image::make($image_tmp)->resize(1140,340)->save($banner_path);
					
					// store image name in products table

					$banner->image = $filename;
				}
			}

	        if(empty($data['status'])){
	            $status = 0;
	        }else{
	            $status = 1;
	        }

	        $banner->status = $status;
			$banner->save();

			return redirect('/admin/view-banners')->with('flash_message_success','banner has been added successfully!');
		}

    	return view('admin.banners.add_banner');
    }

    public function editBanner(Request $request, $id = null){
    	if($request->isMethod('post')){
    		$data = $request->all();

    		if(empty($data['status'])){
	            $status = '0';
	        }else{
	            $status = '1';
	        }

	        if(empty($data['title'])){
	        	$data['title'] = '';
	        }

	        if(empty($data['link'])){
	        	$data['link'] = '';
	        }

    		// upload image
			if($request->hasFile('image')){
				$image_tmp = Input::file('image');
				if($image_tmp->isValid()){
					
					$extension = $image_tmp->getClientOriginalExtension();
					$filename = rand(111,99999).'.'.$extension;
					$banner_path = 'images/frontend_images/banners/'.$filename;
					//Resize image 
					Image::make($image_tmp)->resize(1140,340)->save($banner_path);
					
					// store image name in products table

				}
    		}else if(!empty($data['current_image'])){
    			$filename = $data['current_image'];

    		}else{
    			$filename = '';

    		}

    		Banner::where('id',$id)->update(['status'=>$status,'title'=>$data['title'],'link'=>$data['link'],'image'=>$filename]);
    		return redirect()->back()->with('flash_message_success','Banner has been edited successfully!');
    	}

    	$bannerDetails = Banner::where('id',$id)->first();
    	return view('admin.banners.edit_banner')->with(compact('bannerDetails'));  	
   	}
    

    public function viewBanners(){
    	$banners = Banner::get();
    	return view('admin.banners.view_banners')->with(compact('banners'));
    }

    public function deleteBanner($id = null){

    	$banner = Banner::findOrFail($id);

    	$image_path  = public_path().'/images/frontend_images/banners/'.$banner->image;
  		if(file_exists($image_path)){
  			unlink($image_path);
  			
  		}

    	$banner->delete();

    	return redirect()->back()->with('flash_message_success','Banner has been successfully deleted!');
    }


}

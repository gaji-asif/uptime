<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\API\ApiController;
use App\Employee;
use App\Builds;
use App\Batch;
use App\Subcategory;
use App\Categories;
use App\Validations;
use DB;
use Auth;
use Mail;


class SubmitVerifyController extends Controller
{

    private function check_build(Builds $build)
    {
        if ($build->status >= 0) {
            return false;
        }
        return true;
    }

    private function blur($img, $blurFactor)
    {

            $originalWidth = imagesx($img);
            $originalHeight = imagesy($img);

            $smallestWidth = ceil($originalWidth * pow(0.5, $blurFactor));
            $smallestHeight = ceil($originalHeight * pow(0.5, $blurFactor));

            // For the first run, the previous image is the original input.
            $prevImage = $img;
            $prevWidth = $originalWidth;
            $prevHeight = $originalHeight;

            // Scale way down and gradually scale back up, blurring all the way.
            for ($i = 0; $i < $blurFactor; $i++) {
                // Determine dimensions of next image.
                $nextWidth = $smallestWidth * pow(2, $i);
                $nextHeight = $smallestHeight * pow(2, $i);

                // Resize previous image to next size.
                $nextImage = imagecreatetruecolor($nextWidth, $nextHeight);
                imagecopyresized(
                    $nextImage, $prevImage,
                    0, 0, 0, 0,
                    $nextWidth, $nextHeight, $prevWidth, $prevHeight
                );

                // Apply blur filter.
                imagefilter($nextImage, IMG_FILTER_GAUSSIAN_BLUR);

                // Now the new image becomes the previous image for the next step.
                $prevImage = $nextImage;
                $prevWidth = $nextWidth;
                $prevHeight = $nextHeight;
            }

            // Scale back to original size and blur one more time
            imagecopyresized(
                $img, $nextImage,
                0, 0, 0, 0,
                $originalWidth, $originalHeight, $nextWidth, $nextHeight
            );
            imagefilter($img, IMG_FILTER_GAUSSIAN_BLUR);

            return $img;
            // Clean up
            imagedestroy($prevImage);
    }

    public function preview_image(Request $request)
    {
        $batch = Batch::findOrFail($request->uuid);
        $build = Builds::where('batch_id', $batch->id)->first();

        $url = \Storage::disk('s3')->url('images/build/'.$build->image);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $image = curl_exec($ch);
        curl_close($ch);

        // $img = new \Imagick();
        // $img->readImageBlob($image);
        // $img->addNoiseImage(\imagick::NOISE_IMPULSE);
        // $img->embossImage(25,25);
        $img = imagecreatefromstring($image);
        // $blurSize = $img->getImageHeight() / 10.0;
        // $blurSize = $img->getImageHeight() / 10.0;
        $blurSize = imagesy($img) / 10.0;

        $img = $this->blur($img, 6);
        // $img->blurImage($blurSize,40);
        // $gaussian = array(array(1.0, 2.0, 1.0), array(2.0, 4.0, 2.0), array(1.0, 2.0, 1.0));
        // imageconvolution($img, $gaussian, 16, 0);
        // imagefilter($img, IMG_FILTER_GAUSSIAN_BLUR);
        // imagefilter($img, IMG_FILTER_GAUSSIAN_BLUR);
        // imagefilter($img, IMG_FILTER_SELECTIVE_BLUR);

        // $img->brightnessContrastImage(20,0);
        // $img->setImageColorSpace(\imagick::COLORSPACE_GRAY);
        header('Pragma: public');
        header('Cache-Control: max-age=86400');
        header('Expires: '. gmdate('D, d M Y H:i:s \G\M\T', time() + 86400));
        // header('Content-type: ' . $img->getImageMimeType());
        header('Content-Type: image/jpeg');
        imagejpeg($img);
        imagedestroy($img);
        // echo $img;
        // $img->destroy();
        die();
    }

    public function index(Request $request)
    {
        $batch = Batch::findOrFail($request->uuid);
        $build = Builds::where('batch_id', $batch->id)->first();
        if (!$build || !$this->check_build($build)) {
            return redirect()->route('verify.submission.closed', $request->uuid);
        }

        return view('verification.welcome')
                ->with('uuid', $request->uuid);
    }


    public function submission_closed(Request $request)
    {
        $batch = Batch::findOrFail($request->uuid);
        $builds = Builds::where('batch_id', $request->uuid)->get();
        $employee = Employee::findOrFail($builds[0]->employee_id);


        return view('verification.submission_closed')
                ->with('uuid', $request->uuid)
                ->with('user_data', $employee->toArray());
    }

    public function authenticate(Request $request)
    {
        //session()->forget('verifier_phonenumber');
        //session()->flush();

        $authStep = 0;
        $nextStep = route('verify.submission.authenticate', ['uuid'=>$request->uuid]);
        $batch = Batch::findOrFail($request->uuid);
        $build = Builds::where('batch_id', $batch->id)->first();
        $employee = Employee::findOrFail($build->employee_id);

        if (!$build || !$this->check_build($build)) {
            return redirect()->route('verify.submission.closed', $request->uuid);
        }

        $verificationId = "";

        // or fail

        $firstname = $batch->firstname;
        $lastname = $batch->lastname;
        $phonenumber = "";

        $verifiedBuildsAsSameCredientials = Builds::where('email_to', $batch->email_to)->where('status', "1")->get();
        
        if (count($verifiedBuildsAsSameCredientials) > 0){
            $old_batch = Batch::where('id', $verifiedBuildsAsSameCredientials[0]->batch_id)->first();
            $batch->firstname = $old_batch->firstname;
            $batch->lastname = $old_batch->lastname;
            $request->session()->put('verifier_firstname', $old_batch->firstname);
            $request->session()->put('verifier_lastname', $old_batch->lastname);
            $batch->phonenumber = $old_batch->phonenumber;
            $batch->save();
            $builds = Builds::where('batch_id', $request->uuid)->get();
                if (count($builds) > 0) {
                    if (!$this->check_build($builds[0])) {
                        return redirect()->route('verify.submission.closed', $request->uuid);
                    }
                } else {
                    return redirect()->route('verify.submission.closed', $request->uuid);
                }
                // or fail
        
                foreach($builds as $build){
                    $employee = Employee::findOrFail($build->employee_id);
                    //$category = Categories::find($build->subcategory);
                    $category = Subcategory::find($build->subcategory);
                    if(!empty($category))
                        //$build->category_name = $category->category_name;
                        $build->category_name = $category->subcategory_name;
                    else $build->category_name = '' ;
        
                }
        
                return view('verification.approval')
                        ->with('uuid', $request->uuid)
                        ->with('user_data', $employee->toArray())
                        ->with('verifier_name', $request->session()->get('verifier_firstname'). ' ' .$request->session()->get('verifier_lastname'))
                        ->with('builds', $builds);
        }

        if ($request->isMethod('post')) {
            $verificationId = $request->verificationId;
            $firstname = $request->firstname;
            $lastname = $request->lastname;
            $phonenumber = $request->phonenumber;
            $request->session()->put('verifier_firstname', $request->firstname);
            $request->session()->put('verifier_lastname', $request->lastname);
            $request->session()->put('verifier_phonenumber', $request->phonenumber);
            $otherBatchWithSameCrediential = Batch::where('email_to', $batch->email_to)->where('phonenumber', $request->phonenumber)->get();
            if ($otherBatchWithSameCrediential && count($otherBatchWithSameCrediential) > 0){
                $otherBatchWithSameCredientialQuery = Batch::where('email_to', $batch->email_to)->where('phonenumber', $request->phonenumber);
                $otherBatchWithSameCredientialQuery->update(['firstname' => $request->firstname, 'lastname' => $request->lastname]);
                
                $builds = Builds::where('batch_id', $request->uuid)->get();
                if (count($builds) > 0) {
                    if (!$this->check_build($builds[0])) {
                        return redirect()->route('verify.submission.closed', $request->uuid);
                    }
                } else {
                    return redirect()->route('verify.submission.closed', $request->uuid);
                }
                // or fail
        
                foreach($builds as $build){
                    $employee = Employee::findOrFail($build->employee_id);
                    //$category = Categories::find($build->subcategory);
                    $category = Subcategory::find($build->subcategory);
                    if(!empty($category))
                        //$build->category_name = $category->category_name;
                        $build->category_name = $category->subcategory_name;
                    else $build->category_name = '' ;
        
                }
        
        
                return view('verification.approval')
                        ->with('uuid', $request->uuid)
                        ->with('user_data', $employee->toArray())
                        ->with('verifier_name', $request->session()->get('verifier_firstname'). ' ' .$request->session()->get('verifier_lastname'))
                        ->with('builds', $builds);
            }
            $authStep = 1;
            $nextStep = route('verify.submission.approval', ['uuid' => $request->uuid]);
            $verifycode = mt_rand(100000 , 999999) . '';
            $request->session()->put('verifycode', $verificationId);
            // Mail::to($batch->email_to)->send( new \App\Mail\SubmitVerifyAuthenticate($verifycode) );
        }

        return view('verification.authenticate')
                ->with('uuid', $request->uuid)
                ->with('batch', $batch)
                ->with('verifier', [
                    'firstname' => $firstname,
                    'lastname' => $lastname,
                    'phonenumber' => $phonenumber,
                ])
                // ->with('preview_image', route('verify.submission.preview', ['uuid'=>$request->uuid]))
                ->with('preview_image', \Storage::disk('s3')->url('images/build/'.$build->image))
                ->with('firebaseConfig', file_get_contents(storage_path('firebase-config.json')))
                ->with('verificationId', $verificationId)
                ->with('employee', $employee)
                ->with('nextStep', $nextStep)
                ->with('authStep', $authStep);
    }

    public function approval(Request $request)
    {
        // if ($request->verifycode != $request->session()->get('verifycode')) {
        if (empty($request->idToken)) {
            // var_dump($request->verifycode);
            // die(session('verifycode'));
            return redirect()->back()->withErrors(['Invalid Verification Code']);;
        }
        $batch = Batch::findOrFail($request->uuid);
        $builds = Builds::where('batch_id', $request->uuid)->get();


        //////////////// Write the first name and last name with BatchId ////////////////////////

        $firstname = $request->session()->get('verifier_firstname');
        $lastname = $request->session()->get('verifier_lastname');
        $phonenumber = $request->session()->get('verifier_phonenumber');
        $batch->firstname = $firstname;
        $batch->lastname = $lastname;
        $batch->phonenumber = $phonenumber;
        $batch->save();

        ///////////////////////////////////////////////////////////////////////////////////////////


        if (count($builds) > 0) {
            if (!$this->check_build($builds[0])) {
                return redirect()->route('verify.submission.closed', $request->uuid);
            }
        } else {
            return redirect()->route('verify.submission.closed', $request->uuid);
        }
        // or fail

        foreach($builds as $build){
            $employee = Employee::findOrFail($build->employee_id);
            //$category = Categories::find($build->subcategory);
            $category = Subcategory::find($build->subcategory);
            if(!empty($category))
                //$build->category_name = $category->category_name;
                $build->category_name = $category->subcategory_name;
            else $build->category_name = '' ;

        }


        return view('verification.approval')
                ->with('uuid', $request->uuid)
                ->with('user_data', $employee->toArray())
                ->with('verifier_name', $request->session()->get('verifier_firstname'). ' ' .$request->session()->get('verifier_lastname'))
                ->with('builds', $builds);
    }

    public function process_approval(Request $request)
    {
        foreach ($request->all() as $key => $value) {
            if (strpos($key, 'build_') === 0) {
                $buildId = substr($key, 6);
                $build = Builds::findOrFail($buildId);
                $employee = Employee::findOrFail($build->employee_id);
                $verifier_email = $build->emailto;
                $verifier = Employee::where('email', $verifier_email)->first();

                $api = new ApiController;

                $validation = Validations::create([
                    'build_id' => $build->id,
                    'batch_id' => $build->batch_id
                ]);

                if ($verifier) {
                    $validation->employee_id = $verifier->id;
                }

                if ($value == 'approve') {
                    $build->status = '1';
                    $build->save();
                    $validation->win = '1';
                    $validation->status = '1';
                    $message = "Congratulations ".$employee->full_name.". Your Upload ".$build->build_text." is approved.";
                    if (env('APP_ENV') != 'local')
                        $api->sendpush($build->employee_id,"Submission Approved ",$message,$build->toArray(),'buildApprove');
                } else {
                    $build->status = '0';
                    $build->save();
                    $validation->win = '0';
                    $validation->status = '0';
                    $message = "Sorry ".$employee->full_name.". Your Upload ".$build->build_text." is rejected.";
                    if (env('APP_ENV') != 'local')
                        $api->sendpush($build->employee_id,"Submission Rejected ",$message,$build->toArray(),'buildrejected');
                }
                $validation->save();
            }
        }

        return redirect()->route('verify.submission.thanks', $request->uuid);

    }

    public function thanks(Request $request)
    {
        $batch = Batch::findOrFail($request->uuid);
        $builds = Builds::where('batch_id', $request->uuid)->get();
        $employee = Employee::findOrFail($builds[0]->employee_id);
        $status = 1;
        foreach($builds as $build){
            if ($build->status == 0){
                $status = 0;
            }
        }

        return view('verification.thanks')
                ->with('uuid', $request->uuid)
                ->with('user_data', $employee->toArray())
                ->with('status', $status);
    }

    public function share(Request $request)
    {
        $batch = Batch::findOrFail($request->uuid);
        $builds = Builds::where('batch_id', $request->uuid)->get();
        $build = Builds::where('batch_id', $request->uuid)->first();
        $employee = Employee::findOrFail($builds[0]->employee_id);

        if ($request->get('to') == 'google') {
            $employee->google_reviews_count = $employee->google_reviews_count + 1;
            $employee->save();
            $message = $batch->firstname." ".$batch->lastname." has started a google review for you!";
            if (env('APP_ENV') != 'local'){
                $api = new ApiController;
                $api->sendpush($employee->id,"Submission Shared ",$message,$build->toArray(),'buildShare');
            }
            return redirect()->to($employee->business_url);
        }

        if ($request->get('to') == 'fb') {
            $employee->fb_share_count = $employee->fb_share_count + 1;
            $employee->save();
            $message = $batch->firstname." ".$batch->lastname." has started a Facebook post that will link others to your portfolio!";
            if (env('APP_ENV') != 'local'){
                $api = new ApiController;
                $api->sendpush($employee->id,"Submission Shared ",$message,$build->toArray(),'buildShare');
            }
            return redirect()->to("https://www.facebook.com/sharer/sharer.php?u=" . route('verify.submission.share', ['uuid' => $request->uuid ]));
        }

        // Redirect to homepage if requested from users instead of FB sharer
        if (strpos($request->header('User-Agent'), 'facebook') === false) {
            $route = $employee->company_id == 119
                ? 'resume.employeeportfolioIndependent.employeeportfolioIndependent.dateindexIndependent'
                : 'employeeportfolio.employeeportfolio.dateindex';

            return redirect()->route($route, [
                'id' => $employee->id,
                'startmonth' => Carbon::parse($employee->created_at)->format('m-d-Y'),
                'endmonth' => Carbon::today()->format('m-d-Y'),
            ]);
        }

        return view('verification.share')
            ->with('uuid', $request->uuid)
            ->with('builds', $builds)
            ->with('full_name', $employee->full_name);
    }


}

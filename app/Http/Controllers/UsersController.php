<?php

namespace App\Http\Controllers;

use App\User;
use App\UserCreditScore;
use App\UserLoanDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        return view("users/index",['users'=>User::whereHas('detail')->where("role_id",2)->get()]);
    }

    public function uncompletedProfile()
    {
        $ids = User::whereHas('detail')->where("role_id",2)->pluck('id');
        $users = User::whereNotIn('id',$ids)->get();

        return view("users/index",['users'=>$users]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return inertia("users/Create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = $request->get('user');
        $details = $request->get('details');

//        $validator = Validator::make($user,['email'=>['unique:users,email']]);
//        if ($validator->fails()){
//            $validator->errors()->messages();
//        }

        $user['password'] = Hash::make($user['password']);
        $inserted_user = User::create($user);

        $detail = new UserLoanDetail();
        $detail->is_employed = $details['is_employed'];
        $detail->salary = $details['salary'];
        $detail->company_name = $details['company_name'];
        $detail->company_phone = $details['company_phone'];
        $detail->employer_name = $details['employer_name'];
        $detail->address1 = $details['address1'];
        $detail->address2 = $details['address2'];
        $detail->postcode = $details['postcode'];
        $detail->city = $details['city'];
        $detail->first_next_of_kin_name = $details['first_next_of_kin_name'];
        $detail->first_next_of_kin_phone = $details['first_next_of_kin_phone'];
        $detail->first_next_of_kin_ralationship = $details['first_next_of_kin_ralationship'];
        $detail->second_next_of_kin_name = $details['second_next_of_kin_name'];
        $detail->second_next_of_kin_phone = $details['second_next_of_kin_phone'];
        $detail->second_next_of_kin_ralationship = $details['second_next_of_kin_ralationship'];
        $inserted_user->detail()->save($detail);

        return redirect()->route('users.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return inertia('users/Show',['user'=>User::find($id)]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      return  view("users/edit",['user'=>User::with('detail')->find($id)]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = $request->get('user');
        $details = $request->get('details');

//        $validator = Validator::make($user,['email'=>['unique:users,email']]);
//        if ($validator->fails()){
//            $validator->errors()->messages();
//        }

        $inserted_user = User::find($id);
        $inserted_user->update($user);

        $inserted_user->detail->is_employed = $details['is_employed'];
        $inserted_user->detail->salary = $details['salary'];
        $inserted_user->detail->company_name = $details['company_name'];
        $inserted_user->detail->company_phone = $details['company_phone'];
        $inserted_user->detail->employer_name = $details['employer_name'];
        $inserted_user->detail->address1 = $details['address1'];
        $inserted_user->detail->address2 = $details['address2'];
        $inserted_user->detail->postcode = $details['postcode'];
        $inserted_user->detail->city = $details['city'];
        $inserted_user->detail->first_next_of_kin_name = $details['first_next_of_kin_name'];
        $inserted_user->detail->first_next_of_kin_phone = $details['first_next_of_kin_phone'];
        $inserted_user->detail->first_next_of_kin_ralationship = $details['first_next_of_kin_ralationship'];
        $inserted_user->detail->second_next_of_kin_name = $details['second_next_of_kin_name'];
        $inserted_user->detail->second_next_of_kin_phone = $details['second_next_of_kin_phone'];
        $inserted_user->detail->second_next_of_kin_ralationship = $details['second_next_of_kin_ralationship'];
        $inserted_user->detail->save();

        return response()->json($inserted_user);
    }

    public function userDetails(Request $request)
    {
        info($request->all());
     return 'success';
    $data = $request->all();


   $user = array(
       'flex_user_id' => $data['user_id'],
       'first_name' => $data['first_name'],
       'last_name' => $data['last_name'],
       'phone' => $data['phone_number_1'],
       'role_id' => 2,
       'dob' => $data['dob'],
       'password' => Hash::make("password"),
   );
   $user_inserted = User::create($user);
        //dd($data['total_score']);
   $score = array(
     'user_id' => $user_inserted->id,
     'total_score' => $data['current_score']['total_score'] ,
     'total_score_value' => $data['current_score']['total_score_value'],
     'score_type' => $data['current_score']['score_type'],
     'score_reason' => $data['current_score']['score_reason'],
     'score_description' => $data['current_score']['score_description']
   );

  UserCreditScore::create($score);
  response()->json("success");
  }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::destroy($id);
        return redirect("users.index");
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Helpers\DataTableHelper;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Event;
use App\Events\SubAdminCreated;

class TeamsController extends Controller
{
    use \App\Traits\TraitController;
    public function index(){
        return view('Teams.index',[
            'title'=>'Teams',
            'urlListData'=>routePut('teams.loadList'),'table' => 'tableTeams'
        ]);
    }
    public function loadList(){
        try {
			$q = User::with("roles")->whereHas("roles", function($q) {
                $q->whereIn("name", ["user"]);
            });
			if ($srch = DataTableHelper::search()) {
				$q = $q->where(function ($query) use ($srch) {
					foreach (['email', 'phone_number', 'first_name','last_name'] as $k => $v) {
						if (!$k) $query->where($v, 'like', '%' . $srch . '%');
						else $query->orWhere($v, 'like', '%' . $srch . '%');
					}
				});
			}
			$count = $q->count();

			if (DataTableHelper::sortBy() == 'ti_status') {
				$q = $q->orderBy(DataTableHelper::sortBy(), DataTableHelper::sortDir() == 'asc' ? 'desc' : 'asc');
			} else {
				$q = $q->orderBy(DataTableHelper::sortBy(), DataTableHelper::sortDir());
			}
			$q = $q->skip(DataTableHelper::start())->limit(DataTableHelper::limit());

			$data = [];
			foreach ($q->get() as $single) {
				$data[] = [
					// 'id' => '<input type="checkbox" class="chk-multi-check" value="' . $single->getId() . '" />',
					'first_name' => putNA($single->first_name),
                    'last_name'=>putNA($single->last_name),
					'email' => putNA($single->email),
					'ti_status' => $single->listStatusBadge(),
					'created_at' => putNA($single->showCreated(1)),
					'actions' => putNA(DataTableHelper::listActions([
                        'edit' => auth()->user()->can('team edit') ? routePut('teams.edit', ['id' => encrypt($single->getId())]) : '',
						'delete' => auth()->user()->can('team delete') ? routePut('teams.delete',['id'=>encrypt($single->getId())]) :'',
                        'approve'=>auth()->user()->can('team approve') && $single->getStatus()!= 1  ?routePut('teams.aprove',['id'=>encrypt($single->getId())]):'',
                        'reject'=>auth()->user()->can('team reject')&& $single->getStatus()!= 0 ?routePut('teams.reject',['id'=>encrypt($single->getId())]):'',
                        'block'=>auth()->user()->can('team block') && $single->getStatus()!= 2 ?routePut('teams.block',['id'=>encrypt($single->getId())]):'',
					]))
				];
			}

			return $this->resp(1, '', [
				'draw' => request('draw'),
				'recordsTotal' => $count,
				'recordsFiltered' => $count,
				'data' => $data
			]);
		} catch (\Throwable $th) {
			return $this->resp(0, exMessage($th), [], 500);
		}
    }
    public function create(){
        $subAdmin = new User();
        return view('Teams.form',['heading'=>"Create",'title'=>"Create Team Member",'user'=>$subAdmin]);
    }

    public function save(){
        // dd(useId(request()->get('id')));
        try {
            $rules = [
                'first_name'=>'required',
                'last_name'=>'required',
                'email'=>'required',
                'ph'=>'ne.phone',
                'admin_title'=>'required',
            ];
            $messages = [
                'first_name.required'=>"Please Provide First Name",
                'last_name.required'=>"Please Provide Last Name",
                'email.required'=>"Please Provide Email",
                'phone.phone'=>"Please provide phone number",
                'admin_title.required'=>"Please select Title"
            ];
            $validator = validate(request()->all(), $rules, $messages);
            if($validator){
                return $this->resp(0,$validator[0],[],500);
            }
            $status = userLogin()->hasRole('admin') ? 1 :0;
            $subadminData = [
                'first_name'=>request()->get('first_name'),
                'last_name'=>request()->get('last_name'),
                'email'=>request()->get('email'),
                'phone_number'=>request()->get('phone'),
                'title'=>request()->get('admin_title'),
                'ti_status'=>$status,
            ];
            if(useId(request()->get('id'))){
                User::where("id",useId(request()->get('id')))->update($subadminData);
                return $this->resp(1,"Team Member Updated successfully",['url'=>routePut('teams.list')]);
            } else {
                $password = generateRandomPassword(16);
                $subadminData['password']=Hash::make($password);
                $subadmin = User::create($subadminData);
                if($subadmin){
                    $subadmin->assignRole('user');
                    Event::dispatch(new SubAdminCreated($subadmin->getId(),$password));
                    return $this->resp(1,"Team Member Created successfully",['url'=>routePut('teams.list')]);
                }
            }
        } catch (\Throwable $th) {
            // return $this->resp(0,"something went wrong. try again later",[],500);
            return $this->resp(0,$th->getMessage(),[],500);
        }
    }

    public function edit($id){
        try {
            if(useId($id)){
                $subAdmin = User::find(useId($id));
                if($subAdmin){
                    return view('Teams.form',['heading'=>"Edit",'title'=>"Edit Team Member",'user'=>$subAdmin]);
                }
                return redirect()->route('teams.list')->with('error',"Team Member not found");
            } else {
                return redirect()->route('teams.list')->with('error',"Team Member not found");
            }
        } catch (\Throwable $th) {
            return redirect()->route('teams.list')->with('error',"Team Member not found");
        }
    }

    public function delete($id){
        if ($single = User::find(useId($id))) {
            $single->delete();
            return $this->resp(1, getMsg('deleted', ['name' => "Team Member"]));
        } else {
            return $this->resp(0, getMsg('not_found'));
        }
    }

    public function statusChange($id){
        if(routeCurrName()=="teams.aprove"){
            if ($single = User::find(useId($id))) {
                $single->ti_status= 1;
                $single->save();
                return $this->resp(1, getMsg('approve', ['name' => "Team Member"]));
            } else {
                return $this->resp(0, getMsg('not_found'));
            }
        } elseif(routeCurrName()=="teams.reject") {
            if ($single = User::find(useId($id))) {
                $single->ti_status= 0;
                $single->save();
                return $this->resp(1, getMsg('reject', ['name' => "Team Member"]));
            } else {
                return $this->resp(0, getMsg('not_found'));
            }
        } elseif(routeCurrName()=="teams.block"){
            if ($single = User::find(useId($id))) {
                $single->ti_status= 2;
                $single->save();
                return $this->resp(1, getMsg('block', ['name' => "Team Member"]));
            } else {
                return $this->resp(0, getMsg('not_found'));
            }
        }
    }


}

<?php

class PAS_Member extends PAS_API { 
    protected $member_id = null;
    protected $xml_obj;
    protected $errors;
    
    public static function getMemberByLogin($login) { 
        $find = parent::sendRequest('/publisher_members.xml', 'GET', null, '&search[order]=&criteria_0=login&operator_0=equals&query_0='.$login);
        if($find['total_entries'] == 1) { 
            $member = new PAS_Member( (string) $find->member->id );
            return $member;
        } else { 
            return false; // We are returning FALSE if we found More than 1 Member or None!
        }    
    }
    
    public function __construct($member_id = null) { 
        $this->member_id = $member_id;
        
        // Are we Viewing an existing Member or Creating a new one?
        if($member_id != null) { 
            $this->getMemberData();
        } else { 
            // We're going to CREATE a New User
            $this->xml_obj = new SimpleXMLElement('<member></member>');
        }
    }
    
    public function __get($var) { 
        return $this->xml_obj->$var;
    }
    
    public function __set($var, $value) { 
        $this->xml_obj->$var = $value;
    }
    
    public function asXML() { 
        return $this->xml_obj->asXML();
    }
    
    public function save() { 
        if($this->member_id != null) { 
            return $this->updateMember();
        } else { 
            return $this->createMember();
        }
    }
    
    public function authenticate($password) {
      $response = $this->sendRequest('/publisher_members/'.$this->member_id.'/authenticate.xml', 'POST', '<password>' . htmlentities($password) . '</password>');
      return $response->status == "authenticated";
    }
    
    public function getMemberTrackers() { 
        foreach($this->xml_obj->member_trackers->member_tracker as $tracker_data) { 
            $x++;
            foreach($tracker_data as $k => $v) { $all_trackers[$x][$k] = (string) $v; }
        }
        return $all_trackers;
    }
    
    public function getMemberTickets() { 
	    $get = $this->sendRequest('/publisher_members/'.$this->member_id.'/tickets.xml', 'GET');
	    foreach($get->ticket as $ticket) { 
            $tickets[] = new PAS_Ticket($this->member_id, $ticket->id);
        }
	    return $tickets;
    }
    
    public function addTracker($tracker_identifier, $offer_id) { 
        $payload = '<member_tracker>
                        <identifier>'.addslashes($tracker_identifier).'</identifier>
                        <website_offer_id>'.$offer_id.'</website_offer_id>
                    </member_tracker>';
	    $add_tracker = $this->sendRequest('/publisher_members/'.$this->member_id.'/publisher_member_trackers.xml', 'POST', $payload);
	    return $add_tracker;
    }
    
    // Dates should be in this format: 'YYYY-MM-DD'
    public function getMemberStats($start_date=null, $end_date=null) { 
        if($start_date != null && $end_date != null) {
            $add_params = '&start_date='.$start_date.'&end_date='.$end_date;
        } else { $add_params = ''; }
        
	    $stats = $this->sendRequest('/publisher_members/'.$this->member_id.'/stats.xml', 'GET', null, $add_params);
	    return $stats;
    }

    // Dates should be in this format: 'YYYY-MM-DD'.  
/////     NOTE: This method is currently limited to 1 month.  Any longer will likely timeout!
	public function getMemberStatsByDay($start_date, $end_date) { 
		// Validate Dates
		$ts_start = PAS_API::date2timestamp($start_date);
		$ts_end = PAS_API::date2timestamp($end_date);
		if(date('m', $ts_start) != date('m', $ts_end) || date('Y', $ts_start) != date('Y', $ts_end)) { 
			return false; // Our dates eclipsed 1 month or 1 year --> Would TIMEOUT if run, send FALSE!
		}
		
		// Are we working within this month?
		if(date('Ym') == date('Ym', $ts_start)) { 
			// We are --> We ONLY need to request up to today (yesterday, really, but we'll do today for completeness)
			$ts_end = PAS_API::date2timestamp(date('Y-m-d'));
		}
		
		// Now, lets go ahead & loop through each day!
		$current_time = $ts_start;
		while($current_time <= $ts_end) {
			$add_params = '&start_date='.date('Y-m-d',$current_time).'&end_date='.date('Y-m-d',$current_time);
	    	$stats = $this->sendRequest('/publisher_members/'.$this->member_id.'/stats.xml', 'GET', null, $add_params);

			// Build an array of Daily Stats for later return.
			$daily_stats_array[date('Y-m-d',$current_time)] = $stats;
			
			// On the next iteration, get `tomorrow`!
			$current_time += 86400;
		}

		// Return the Daily Stats!
	    return $daily_stats_array;
	}
    
    // Dates should be in this format: 'YYYY-MM-DD'
    public function getMemberRAFStats($start_date=null, $end_date=null) { 
        if($start_date != null && $end_date != null) {
            $add_params = '&start_date='.$start_date.'&end_date='.$end_date;
        } else { $add_params = ''; }
        
	    $stats = $this->sendRequest('/publisher_members/'.$this->member_id.'/referrals.xml', 'GET', null, $add_params);
	    return $stats;
    }
    
    public function getMemberPayments($start_date=null, $end_date=null, $page=1) { 
        if($start_date != null && $end_date != null) {
            $add_params = '&start_date='.$start_date.'&end_date='.$end_date;
        } else { $add_params = ''; }
        $add_params .= '&page='.$page;
        $payments = $this->sendRequest('/publisher_members/'.$this->member_id.'/payments.xml', 'GET', null, $add_params);
        return $payments;
    }
    
    public function getMemberCashouts($start_date=null, $end_date=null) { 
        if($start_date != null && $end_date != null) {
            $add_params = '&start_date='.$start_date.'&end_date='.$end_date;
        } else { $add_params = ''; }
        $add_params .= '&page='.$page;
        $payments = $this->sendRequest('/publisher_members/'.$this->member_id.'/cashouts.xml', 'GET', null, $add_params);
        return $payments;
    }

    public function newMemberCashout($cashout_details, $cashout_method_id) { 
        $payload = '<cashout>
                        <details>'.addslashes($cashout_details).'</details>
                        <cashout_method_id>'.$cashout_method_id.'</cashout_method_id>
                    </cashout>';
        $cashout = $this->sendRequest('/publisher_members/'.$this->member_id.'/cashouts.xml', 'POST', $payload);
        return $cashout;
    }
    
    // Returns a simple associative array of Member Balance data
    public function getMemberBalances() { 
        $data = $this->sendRequest('/publisher_members/'.$this->member_id.'/cashouts/new.xml', 'GET');
        $balances['available_balance'] = (float) $data->available_balance;
        $balances['total_balance'] = (float) $data->total_balance;
        $balances['problem_balance'] = (float) $data->problem_balance;        
        return $balances;
    }
    
    // Returns a simple array of Cashout Methods
    public function getMemberCashoutMethods() { 
        $data = $this->sendRequest('/publisher_members/'.$this->member_id.'/cashouts/new.xml', 'GET');
        foreach($data->cashout_methods->cashout_method as $method) { 
            $cashout_methods[(integer) $method->id]['id'] = (integer) $method->id;
            $cashout_methods[(integer) $method->id]['name'] = (string) $method->name;
            $cashout_methods[(integer) $method->id]['minimum_amount'] = (string) $method->minimum_amount;
            $cashout_methods[(integer) $method->id]['instructions'] = (string) $method->instructions;
        }
        return $cashout_methods;
    }
    
	public function getRemoteAuthToken() { 
	    $get = $this->sendRequest('/remote_auth.xml', 'POST', '<member_id>'.$this->member_id.'</member_id>');
	    return (string) $get[0];
	}
    
    private function getMemberData() { 
	    $this->xml_obj = $this->sendRequest('/publisher_members/'.$this->member_id.'.xml');
        if(parent::hasErrors($this->xml_obj)) { 
            return false;
        } return true;
	}
	
	private function updateMember() { 
	    $update = $this->sendRequest('/publisher_members/'.$this->member_id.'.xml', 'PUT', $this->asXML());
        if(parent::hasErrors($update)) { 
            return false;
        } return true;
	}
	
	private function createMember() { 
	    $create = $this->sendRequest('/publisher_members.xml', 'POST', $this->asXML());
	    if(parent::hasErrors($create)) { 
            return false;
        } return true;
	}    
}

?>
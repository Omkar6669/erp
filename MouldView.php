<?php
session_start();
include_once 'config/Cfg.php';
if (isset($_SESSION['last_action'])) {
    
    $secondsInactive = time() - $_SESSION['last_action'];
    
    $expireAfterSeconds = EXPIRE_AFTER * 60;
    
    if ($secondsInactive >= $expireAfterSeconds) {
        
        session_unset();
        session_destroy();
        header('Location:/erp/');
    }
}
if (! isset($_SESSION)) {
    session_start();
    $_SESSION['LoggedIn'] = 0;
}

if ($_SESSION['LoggedIn'] == 1) {
    include ('Navbar.php');
    ?>

<html>
<head>

<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">



<title>Mould List</title>

<!-- include material design CSS -->
<link rel="stylesheet" href="libs/css/materialize.min.css" />
<link rel="stylesheet" href="libs/css/VplaErp.css" />
<link rel="stylesheet" type="text/css"
	href="libs/css/jquery.timepicker.css" />
<link rel="stylesheet" type="text/css"
	href="libs/css/jquery.datetimepicker.css" />

<!-- include material design icons -->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons"
	rel="stylesheet" />
<!-- custom CSS -->

<style>
.width-100-pct {
	width: 100%;
}

.text-align-center {
	text-align: center;
}

.margin-bottom-1em {
	margin-bottom: 1em;
}

[ng\:cloak], [ng-cloak], [data-ng-cloak], [x-ng-cloak], .ng-cloak,
	.x-ng-cloak {
	display: none !important;
}
</style>
</head>
<body>

	<div class="container" ng-app="myApp" ng-controller="mouldCtrl">
		<br>
		<h4 align="center">Moulds</h4>
		<hr>
		<br>

		<div class="row">
			<div class="col s12" ng-cloak>

				<div class="input-field col s12" id="search">
					<i class="material-icons prefix">search</i> <input id="icon_prefix"
						type="text" ng-model="search" class="form-control"> <label
						for="icon_prefix">Search here..</label>
				</div>

				<div class="col s12" id="noMoulds">
					<h5 style="text-align: center">Moulds are not created yet...</h5>
				</div>

				<div style="width: 100%; overflow: auto;" id="table">
					<table class="striped responsive-table">

						<thead>
							<tr>
								<th style="padding-bottom: 32px"></th>
								<th class="width-30-pct">Name</th>
								<th class="width-30-pct">SFG name</th>
								<th class="width-30-pct">No. of cavities</th>
								<th class="width-30-pct">Item produced</th>

							</tr>
						</thead>

						<tbody ng-init="getAll()">
							<tr ng-repeat="mould in mouldList | filter:search ">
								<td>
									<button ng-click="readOne(mould.mould_id)" title="Edit"
										class="btn modal-trigger red">
										<i class="material-icons">edit</i>
									</button>
								</td>
								<td>{{ mould.mould_name }}</td>
								<td>{{ mould.sfg_name }}</td>
								<td>{{ mould.no_of_cavities}}</td>
								<td>{{ mould.item_produced}}</td>
								<td>
									<button id="delete-mould-button" title="Delete"
										ng-click="deleteMouldView(mould.mould_id)"
										class="btn modal-trigger red">
										<i class="material-icons">delete</i>
									</button>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				


				<!-- modal for for creating new product -->
				<div id="modal-mould-form" class="modal">
					<a class="btn btn-floating btn-flat modal-action modal-close"
						style="top: 4px; right: 3px; position: absolute;" title="close"><i
						class="small mould-icons" style="color: orange;">close</i></a>
					<div class="modal-content">
						<form name="mouldForm" novalidate>
							<h4 id="modal-mould-title">Add Mould</h4>
							<div class="row">
								<div class="input-field col s12">
									<span class="text_size">Mould Name</span> <input
										data-ng-model="mould_name" type="text" id="mould_name"
										name="mould_name" placeholder="Type mould name here..."
										required /> <span style="color: red" class="error"
										data-ng-show="mouldForm.mould_name.$error.required">Required.</span>
									<br>
								</div>

								<div class="input-field col s12">
									<div class="input-field col s6">
										<span class="text_size">SFG</span> <br>
										<br> 
										<select class="browser-default" data-ng-model="sfg_id" ng-init = "getSFG_id()"
											mouldize-select watch required />
										<option value="">-- Select Compatiable SFG --</option>
										<option ng-repeat="sfg in sfgList" value="{{sfg.sfg_id}}">{{sfg.sfg_name}}</option>
										</select> <br>
									</div>
								</div>

								<div class="input-field col s12">
									<div class="input-field col s6" id="datetimepicker">
									<span class="text_size">Date</span> <br>  <input
										type="text" ng-model="mould_date" required datetimepicker />
										<span
										style="color: red" class="error"
										data-ng-show="mouldForm.mould_date.$error.required">Required.</span>

								</div>
									<div class="input-field col s6">
										<span class="text_size">Cavities</span> <input
											type="text" name="no_of_cavities"
											data-ng-model="no_of_cavities" ng-pattern="/^\d+$/"
											placeholder="No of cavities.." required /> <span
											style="color: red" class="error"
											data-ng-show="mouldForm.no_of_cavities.$error.required">Required</span>
										<span style="color: red" class="error"
											data-ng-show="mouldForm.no_of_cavities.$error.pattern">Quantity
											field should be only digit.</span>
									</div>
								</div>

								<div class="input-field col s12">
									
									<div class="input-field col s6">
										<span class="text_size">Item produced</span> <input
											type="text" name="item_produced"
											data-ng-model="item_produced" ng-pattern="/^[0-9]*$/"
											placeholder="Number of item produced..." required /> <span
											style="color: red" class="error"
											data-ng-show="mouldForm.item_produced.$error.required">Required</span>
										<span style="color: red" class="error"
											data-ng-show="mouldForm.item_produced.$error.pattern">Numbers only</span>
									</div>
								</div>
							</div>

							<div class="input-field col s12">
									<button id="btn-create-mould"
										class="btn margin-bottom-1em red"
										data-ng-click="createMoulds(model);"
										ng-disabled="mouldForm.$invalid || create_mould">
										<i class="material-icons left">add</i>Add
									</button>

									<button id="btn-update-mould"
										class="btn margin-bottom-1em red"
										data-ng-click="updateMould(mould_id);"
										ng-disabled="mouldForm.$invalid">
										<i class="material-icons left">update</i>Update
									</button>

									<button
										class="modal-action modal-close btn margin-bottom-1em red">
										<i class="material-icons left">close</i>Cancel
									</button>
								</div>

						</form>
					</div>
				</div>

				<div class="fixed-action-btn" style="bottom: 45px; right: 24px;">
					<a
						class="waves-effect waves-light btn modal-trigger btn-floating btn-large red"
						title="add" href="#modal-mould-form" ng-click="showCreateForm()"><i
						class="large material-icons">add</i></a>
				</div>
				</div>
				</div>
				</div>
				



				<!-- include jquery -->
				<script type="text/javascript" src="libs/js/jquery.min.js"></script>

				<!-- material design js -->
				<script src="libs/js/materialize.min.js"></script>
				<script type="text/javascript" src="libs/js/jquery.timepicker.js"></script>
				<script type="text/javascript"
					src="libs/js/jquery.datetimepicker.full.js"></script>
				<!-- include angular js -->
				<script src="libs/js/angular.min.js"></script>

				<script>

//angular js codes will be here

var app = angular.module('myApp', []);
app.controller('mouldCtrl', function($scope, $http) {

	
	$scope.showCreateForm = function(){
	    // clear form
	    $scope.clearForm();
	    $scope.create_mould = false;
	     
	    // change modal title
	    $('#modal-mould-title').text("Add New Mould");   
	    
	    // hide update mould button
	    $('#btn-update-mould').hide();
	     
	    // show create mould button
	    $('#btn-create-mould').show();

	    $('#modal-mould-form').openModal({ dismissible: false});
	     
	}
	$scope.clearForm = function() { 
		$scope.mould_name = "";
		$scope.sfg_id = "";
		$scope.mould_date = "";
		$scope.no_of_cavities = "";
		$scope.item_produced = "";
		}

	$scope.getSFG_id = function(){
		$scope.params_array = [];
		$http.post("server.php",{ 
			'object_name' : 'SFG',
			'fun_name' : 'getSFGs',
			'paramsArray' : $scope.params_array
		}).success(function(data){
		   console.log(data);	    
		    //console.log($scope.user_id.length);
		    $scope.sfgList = data;
		});
	}

	

	
	$scope.createMoulds = function(model){
		var date = new Date($scope.mould_date);
		//console.log(date.getFullYear()+"-"+ parseInt(date.getMonth()+1)+"-"+date.getDate());
		var final_date = date.getFullYear()+"-"+ parseInt(date.getMonth()+1)+"-"+date.getDate();
	    if ($scope.mouldForm.$valid) {   
	    $scope.params_aray = [];
	      $scope.params_aray.push({
	    		name: $scope.mould_name,
	    		sfg_id: $scope.sfg_id,
	    		date: final_date,
	    		cavities: $scope.no_of_cavities,
	    		item_produced: $scope.item_produced
	       });
	      console.log($scope.params_aray);
	 	  $http.post("server.php",{ 
	 		'object_name' : 'Mould',
	 		'fun_name' : 'addMould',
	 		'paramsArray' : $scope.params_aray
	 	 }).success(function (data, status, headers, config) {
	 		console.log(data);
		 	$scope.result = data;
	 	 	
	 		if($scope.result === 'true')
			{
				
	 			Materialize.toast("New Mould Created Successfully.", 2000);
				
			}
			else
			{
				Materialize.toast("Unable to Create New Mould.", 2000);
			}	
			    // clear modal content
			    $scope.clearForm();
				
				// close modal
				$('#modal-mould-form').closeModal();

				$scope.getAll();
			
			}); 
	   }
	    else {
	        //if form is not valid set$scope.mouldForm.submitted to true     
	        $scope.mouldForm.submitted=true;    
	    };
	  
	}


	$scope.pageSize = 30;
	$scope.getAll = function(){

		$scope.params_aray = [];
		$http.post("server.php",{ 
			'object_name' : 'Mould',
			'fun_name' : 'getMoulds',
			'paramsArray' : $scope.params_aray
		}).success(function (data, status, headers, config) {
			console.log(data);
			$scope.mouldList = data;
			//$scope.totalmoulds = $scope.mouldList.length; 
			
			if(data === 'false')
			{
				$('#search').hide();
				$('#table').hide();
				$('#noMoulds').show();
			}
			else
			{
				$('#search').show();
				$('#table').show();
				$('#noMoulds').hide();
			}
		  
	        
	    });
	} 


$scope.readOne = function(mould_id){
		
	    // change modal title read_one.php
	    $('#modal-mould-title').text("Edit Mould");
	     
	    // show udpate mould button
	    $('#btn-update-mould').show();
	     
	    // show create mould button
	    $('#btn-create-mould').hide();  
	    $scope.params_aray = [];
	   
	    $scope.params_aray.push({
	    	mould_id: mould_id
	     }); 

	     console.log($scope.params_aray);
		 $http.post("server.php",{ 
			'object_name' : 'Mould',
			'fun_name' : 'getMould',
			'paramsArray' : $scope.params_aray	
		}).success(function(data, status, headers, config){
			console.log(data);
	 
	      //  $scope.mould_id = data[0]["mould_id"];
	        $scope.mould_name = data[0]["mould_name"];
	        $scope.sfg_id = data[0]["sfg_id"];
	        $scope.no_of_cavities = data[0]["no_of_cavities"];
	        $scope.mould_date = data[0]["bdate"];
	        $scope.item_produced = data[0]["item_produced"];
	        $scope.mould_id = data[0]["mould_id"];
	         
	        // show modal
	        $('#modal-mould-form').openModal({ dismissible: false});
	      
			  })
	    .error(function(data, status, headers, config){
	        Materialize.toast('Unable to retrieve record.', 2000);
	    });
	}

	
	$scope.updateMould = function(mould_id){ 
		console.log(mould_id);
		var date = new Date($scope.mould_date);
		//console.log(date.getFullYear()+"-"+ parseInt(date.getMonth()+1)+"-"+date.getDate());
		var final_date = date.getFullYear()+"-"+ parseInt(date.getMonth()+1)+"-"+date.getDate();
		$scope.params_aray = [];			 
			 $scope.params_aray.push({
					mould_id:  mould_id, 
					name: $scope.mould_name,
			        sfg_id : $scope.sfg_id, 
			        no_of_cavities : $scope.no_of_cavities,
			        mould_date : final_date, 
			        item_produced : $scope.item_produced
			     
		       });
        console.log($scope.params_aray);
 	    $http.post("server.php",{ 
 		 'object_name' : 'Mould',
 		 'fun_name' : 'updateMould',
 		 'paramsArray' : $scope.params_aray
 	    })
		.success(function (data, status, headers, config){  
			console.log(data);
			$scope.result = data;
			if($scope.result == 'true')
			{
	 		    // close modal
				$('#modal-mould-form').closeModal();
				Materialize.toast("mould Details Updated.", 2000);
				//setInterval(function(){ location.reload(); }, 2000);
				// clear modal content
				$scope.clearForm();		 
				// refresh the mould list
				$scope.getAll();
				
			}
			else
			{
				Materialize.toast("Unable to Update Mould Details.", 2000);
			}		       
			 
		});
}


	$scope.deleteMouldView = function(mould_id){
console.log(mould_id);	    // ask the mould if he is sure to delete the record
	    if(confirm("The selected Mould will be deleted permanently, do you wish to continue?")){
	        // post the id of product to be deleted
	        $scope.params_aray = [];
			 
		    $scope.params_aray.push({
					mould_id: mould_id
		     });
		
	          $http.post("server.php",{ 
		        'object_name' : 'Mould',
		        'fun_name' : 'deleteMould',
		        'paramsArray' : $scope.params_aray
	         }).success(function (data, status, headers, config){  
				console.log(data);
		          $scope.result = data;
			   if($scope.result == 'true')
			   {
				
				Materialize.toast("Mould Deleted.", 2000);
			    // close modal
				$('#modal-mould-form').closeModal();
				 
				// clear modal content
				$scope.clearForm();
				 
				// refresh the mould list
				$scope.getAll();
				
			    }
			    else
			    {
				Materialize.toast("Unable to Delete Mould.", 2000);
			    }		       
	                  
	  });
	    }
	    
	}
	
	
});
app.directive("datetimepicker", function () {

    function link(scope, element, attrs) {
        // CALL THE "datepicker()" METHOD USING THE "element" OBJECT.
        	element.datetimepicker({
            timepicker:false,
            format:'d M Y', 
            formatDate:'Y-m-d',
            maxDate: new Date(),
            ignoreReadonly: true,
            scrollInput : false
            
        });
    }
    return {
        require: 'ngModel',
        link: link
    };
});

angular.module('myApp').filter('pagination', function(){
    return function(input, start) {
        if (!input || !input.length) { return; }
        start = +start; //parse to int
        return input.slice(start);
    }
});

$(document).ready(function(){
	
	$(".button-collapse").sideNav();
    // initialize modal
    $('.modal-trigger').leanModal();

});

</script>

</body>
<?php
} else {
    header('Location: /erp/');
}
?>
</html>
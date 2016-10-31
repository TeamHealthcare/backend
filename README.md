# backend

Given a controller with a function called update():

	$scope.update = function() {
	
		$http( {
			method: 'POST',
			url: '/backend/v1/adduser2',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			data: $httpParamSerializerJQLike({
				'employeenumber': $scope.employeenumber,
				'jobtitle': $scope.jobtitle,
				'password': $scope.password,
				'employeename': $scope.employeename
			})
		} ).success( function( data ) { console.log( data ); } );
	}
	
Given a controller with a function called SignUp():
	$scope.SignUp = function() {

		var data = {
			'employeenumber': $scope.employeenumber, 
			'jobtitle': $scope.jobtitle, 
			'password': $scope.password,
			'employeename': $scope.employeename                     	
		}

		var config = {
			headers : {
				'Content-Type': 'application/json'
			}
		}

		$http.post('/backend/v1/adduser', data, config)
		.success(function(data, status, headers, config) {
			if (data.msg != '')
			{
				$scope.msgs.push(data.msg);
			}
			else
			{
				$scope.errors.push(data.error);
			}
		}).error(function(data, status) { // called asynchronously if an error occurs
			// or server returns response with an error status.
			$scope.errors.push(data);
		});
	}
	
# TODO:  Clean up methods to post data so that success/error behaviour is consistent	
//Angular

<!DOCTYPE html>  
<html lang="en">  
<head>  
    <meta charset="utf 8">
    <title>Guru99</title>     
</head>  
<body ng-app="app"> <<< Denotes this is an angular JS app - any name can be given in ""
<h1 ng-controller="HelloWorldCtrl">{{message}}</h1> << Used to hold biz logic the message is a place holder for when we display helloworld
<script src="https://code.angularjs.org/1.6.9/angular.js"></script> << references angular script so functions from angular work
<script>  
    angular.module("app", []).controller("HelloWorldCtrl", function($scope) {  << creating business logic for the controller so when h1 references it, it'll work' Creating a function that will called when we call this controller. $scope is a sp global object in Angular manages data between controller and view.
    $scope.message="Hello World" << Assigning hello world as value of message
    } )
</script> 

</body>  
</html>


//Creating Controller
<!DOCTYPE html>
<html>
<head>
	<meta chrset="UTF 8">
	<link rel="stylesheet" href="css/bootstrap.css"/> << using bootstrap CSS
</head>
<body>
<h1> Guru99 Global Event</h1>
<script src="https://code.angularjs.org/1.6.9/angular.js"></script>
<script src="lib/angular.js"></script> << Adding Angular
<script src="lib/bootstrap.js"></script> << Adding Bootstrap
<script src="lib/jquery-1.11.3.min.js"></script> << Adding JQuery for DOM via Angular
<< ng is a prefix for directive
<div ng-app="DemoApp" ng-controller="DemoController"> << Name AJS App DemoApp and added DemoController to the div tag, must mention under directive so you access fx defined in controlelr

	Tutorial Name : <input type="text" ng-model="tutorialName"><br> << model binds text box for Tutorial Name to 

	This tutorial is {{tutorialName}}
</div>
<script>
	var app = angular.module('DemoApp',[]);

	app.controller('DemoController', function($scope){
	$scope.tutorialName = "Angular JS"; << defaults Angular JS as tutorialName value, but user can change by updating tutorialName text box
	});
</script>

</body>
</html>


//More stuff

<!DOCTYPE html>  
<html lang="en">  
<head>  
    <meta chrset="UTF 8">
    <title>Guru99</title>     
</head>  
<body ng-app="DemoApp">
<h1> Guru99 Global Event</h1>
<script src="https://code.angularjs.org/1.6.9/angular.js"></script>
<div ng-controller="DemoController">
	{{fullName("Guru","99")}} << access the behavior from the view
</div>
<script type="text/javascript">  
	var app = angular.module("DemoApp", []);
	app.controller("DemoController", function($scope) {
    
    $scope.fullName=function(firstName,lastname){ >> 1: Create behavior that takes 2 parameters
		return firstName + lastname; << return value for the behavior
		}
	} );
</script> 

</body>  
</html>

//ng-repeat

<!DOCTYPE html>
<html>
<head>
	<meta chrset="UTF 8"> << Char Set
	<title>Event Registration</title> << title 
	<link rel="stylesheet" href="css/bootstrap.css"/> << Using bootstrap CSS
</head>
<body >
<h1> Guru99 Global Event</h1> << Header 
<script src="https://code.angularjs.org/1.6.9/angular.js"></script> << setting script as angluar

<div ng-app="DemoApp" ng-controller="DemoController"> << create app and controller names
<h1>Topics</h1>
<ul><li ng-repeat="tpname in TopicNames"> << 2. NG repeat goes through topic names
		{{tpname.name}} << 3. Fetch value for each array item
		</li></ul>
</div>

<script> 
	var app = angular.module('DemoApp',[]);
	app.controller('DemoController', function($scope){ << tying controller to scope

	$scope.TopicNames =[ << 1. Creating array of items in scope
		{name: "What controller do from Angular's perspective"},
		{name: "Controller Methods"},
		{name: "Building a basic controller"}];	
		});
</script>

</body>
</html>

// Multiple controllers

<!DOCTYPE html>
<html>
<head>
	<meta chrset="UTF 8">
	<title>Event Registration</title>
	<link rel="stylesheet" href="css/bootstrap.css"/>
</head>
<body >
<h1> Guru99 Global Event</h1>
<script src="https://code.angularjs.org/1.6.9/angular.js"></script>

<div ng-app="DemoApp">
	<div ng-controller="firstcontroller">
		<div ng-controller="secondcontroller"> << directives for 2 controllers under app
		{{lname}} <--- only calling lname, so pname won't display, only accessing controller 2
		</div>
	</div>
</div>

<script>
	var app = angular.module('DemoApp',[]);
	app.controller('firstcontroller', function($scope){ << tying controller 1 to scope
		$scope.pname="firstcontroller"; << adding function to scope for c1
			});
		app.controller('secondcontroller', function($scope){ << tying controller 2 to scope
			$scope.lname="secondcontroller"; << adding function to scope for c2
			});
</script>
</body>
</html>